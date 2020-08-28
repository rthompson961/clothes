<?php

namespace App\Service;

class LoremIpsumGenerator
{
    private const HISTORYSIZE = 20;

    public function getParagraph(int $length = 84): string
    {
        $paragraph = [];
        $history = [];

        for ($i = 1; $i <= $length; $i++) {
            $word = self::pickWord($history);

            // restrict history of words already picked to max size
            if (count($history) >= self::HISTORYSIZE) {
                array_shift($history); // remove oldest entry
            }

            $paragraph[] = $word;
            $history[] = $word;
        }
        $paragraph[0] = ucfirst($paragraph[0]);
        $paragraph = implode(' ', $paragraph);
        $paragraph .= '.';

        return $paragraph;
    }

    private function pickWord(array $history): string
    {
        $list = [
            'aenean',
            'aliquam',
            'ante',
            'arcu',
            'condimentum',
            'cras',
            'dictum',
            'dignissim',
            'dui',
            'eget',
            'enim',
            'et',
            'felis',
            'feugiat',
            'fusce',
            'gravidar',
            'hendrerit',
            'impedit',
            'interdum',
            'ipsum',
            'justo',
            'lectus',
            'leo',
            'libero',
            'lorem',
            'maecenas',
            'magnis',
            'mattis',
            'maximus',
            'mi',
            'montes',
            'morbi',
            'natoque',
            'necessitatibus',
            'neque',
            'nisl',
            'non',
            'nulla',
            'odio',
            'oluptatibus',
            'optio',
            'orci',
            'pellentesque',
            'pharetra',
            'pulvinar',
            'purus',
            'quisque',
            'repellat',
            'risus',
            'rutrum',
            'saepe',
            'sed',
            'soluta',
            'suspendisse',
            'tristique',
            'turpis',
            'ullamcorper',
            'varius',
            'vel',
            'venenatis',
            'vivamus',
            'viverra',
        ];

        // select a word
        $word = $list[mt_rand(0, count($list) - 1)];

        // if word selected recently pick again
        if (in_array($word, $history)) {
            $word = self::pickWord($history);
        }

        return $word;
    }
}
