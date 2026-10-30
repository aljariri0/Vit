<?php

class AIReviewAnalyzer
{
    private string $apiUrl = 'http://localhost:11434/api/generate';
    private string $model = 'gemma3:4b';

    public function analyzeReviews(array $reviews): array
    {
        $sentimentCounts = [
            'positive' => 0,
            'negative' => 0,
            'neutral' => 0,
            'mixed' => 0
        ];

        $positivePoints = [];
        $negativePoints = [];
        $individualReviews = [];

        if (empty($reviews)) {
            return [
                'total_reviews' => 0,
                'sentiment_counts' => $sentimentCounts,
                'sentiment_percentages' => $sentimentCounts,
                'positive_points' => [],
                'negative_points' => [],
                'individual_reviews' => [],
                'overall_summary' => 'No approved reviews available.'
            ];
        }

        foreach ($reviews as $review) {

            $comment = trim($review['comment'] ?? '');

            if ($comment === '') {
                continue;
            }

            $prompt = "
Analyze this customer review for a vitamins store.

Review:
{$comment}

Return ONLY valid JSON:
{
    \"sentiment\": \"positive, negative, neutral, or mixed\",
    \"positive_points\": [],
    \"negative_points\": []
}

Rules:
- Choose exactly one sentiment.
- Positive: mainly positive opinion.
- Negative: mainly negative opinion.
- Neutral: no clear positive or negative opinion.
- Mixed: contains both positive and negative opinions.
- Do not provide medical advice.
- Do not make health claims.
- Do not recommend supplements or treatments.
- Return English only.
";

            $result = $this->sendRequest($prompt);

            $sentiment = strtolower(trim($result['sentiment'] ?? ''));

            if (!isset($sentimentCounts[$sentiment])) {
                $sentiment = 'neutral';
            }

            $sentimentCounts[$sentiment]++;

            $positive = $result['positive_points'] ?? [];
            $negative = $result['negative_points'] ?? [];

            if (is_array($positive)) {
                $positivePoints = array_merge($positivePoints, $positive);
            }

            if (is_array($negative)) {
                $negativePoints = array_merge($negativePoints, $negative);
            }

            $individualReviews[] = [
                'review_id' => $review['id'],
                'sentiment' => $sentiment,
                'positive_points' => $positive,
                'negative_points' => $negative
            ];
        }

        $total = array_sum($sentimentCounts);

        $percentages = [];

        foreach ($sentimentCounts as $sentiment => $count) {
            $percentages[$sentiment] = $total > 0
                ? round(($count / $total) * 100, 2)
                : 0;
        }

        return [
            'total_reviews' => $total,
            'sentiment_counts' => $sentimentCounts,
            'sentiment_percentages' => $percentages,
            'positive_points' => array_values(array_unique($positivePoints)),
            'negative_points' => array_values(array_unique($negativePoints)),
            'individual_reviews' => $individualReviews
        ];
    }

    private function sendRequest(string $prompt): array
    {
        $data = [
            'model' => $this->model,
            'prompt' => $prompt,
            'stream' => false,
            'format' => 'json'
        ];

        $ch = curl_init($this->apiUrl);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 180
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new Exception('Ollama connection failed: ' . $error);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Ollama returned HTTP status: ' . $httpCode);
        }

        $result = json_decode($response, true);

        if (!isset($result['response'])) {
            throw new Exception('Invalid Ollama response.');
        }

        $analysis = json_decode($result['response'], true);

        if (!is_array($analysis)) {
            throw new Exception('AI returned invalid JSON.');
        }

        return $analysis;
    }
}
