<?php

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

        foreach ($this->customWords as $word) {
            $pattern = '/(?<![\p{L}\p{N}])' . preg_quote($word, '/') . '(?![\p{L}\p{N}])/iu';
            if (preg_match($pattern, $comment) === 1) {
                return true;
            }
        }

        return false;
    }
}
