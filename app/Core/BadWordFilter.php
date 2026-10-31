<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use ConsoleTVs\Profanity\Builder;
use CleanSpeak\CleanSpeak;

class BadWordFilter
{

    private array $customWords = [
        'fuck',
        'fucking',
        'fucker',
        'motherfucker',
        'shit',
        'bullshit',
        'shitty',
        'bitch',
        'bastard',
        'asshole',
        'dick',
        'dickhead',
        'piss',
        'pissed',
        'crap',
        'idiot',
        'moron',
        'stupid',
        'dumbass',
        'loser',
        'jerk',
        'retard',
        'son of a bitch',
        'piece of shit',
        'fuck off',
        'shut the fuck up'
    ];


    public function containsBadWords(string $comment): bool
    {
        if (trim($comment) === '') {
            return false;
        }

        $consoleDetected = !Builder::blocker(
            $comment,
            languages: ['en']
        )->clean();

        $cleanSpeak = new CleanSpeak();

        $cleanSpeak->setWords($this->customWords);
        $cleanSpeak->setStrictMode(false);
        $cleanSpeak->setText($comment);

        $cleanSpeakDetected = !$cleanSpeak->isClean();

        return $consoleDetected || $cleanSpeakDetected;
    }
}
