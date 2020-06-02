<?php

namespace App\Service;

class LoremIpsumGenerator
{
    public function getParagraph(int $size = 84): string
    {
        $recent = [];
        $recentSize = 10;
        $paragraph = [];
        for ($i = 1; $i <= $size; $i++) {
            $word = self::pickWord($recent);
            // restrict recent words to appropriate size
            if (count($recent) >= $recentSize) {
                // remove oldest entry
                array_shift($recent);
            }
            $recent[] = $word;
            $paragraph[] = $word;
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

        $word = $list[mt_rand(0, count($list) - 1)];
        if (in_array($word, $recent)) {
            $word = self::pickWord($recent);
        }

        return $word;
    }
}
