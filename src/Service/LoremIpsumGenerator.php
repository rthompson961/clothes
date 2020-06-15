<?php

namespace App\Service;

class LoremIpsumGenerator
{
    private const RECENTPICKSSIZE = 20;

    public function getParagraph(int $size = 84): string
    {
        $paragraph = [];
        $recentPicks = [];

        for ($i = 1; $i <= $size; $i++) {
            $word = self::pickWord($recentPicks);

            // restrict recently picked works to max size
            if (count($recentPicks) >= self::RECENTPICKSSIZE) {
                // remove oldest entry
                array_shift($recentPicks);
            }

            $paragraph[]   = $word;
            $recentPicks[] = $word;
        }
        $paragraph[0] = ucfirst($paragraph[0]);
        $paragraph = implode(' ', $paragraph);
        $paragraph .= '.';

        return $paragraph;
    }

    private function pickWord(array $recent): string
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
        if (in_array($word, $recent)) {
            $word = self::pickWord($recent);
        }

        return $word;
    }
}
