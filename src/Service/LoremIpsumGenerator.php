<?php

namespace App\Service;

class LoremIpsumGenerator
{
    public function getParagraph(int $min = 80, int $max = 120): string
    {
        $wordList = [
            'lorem',
            'ipsum',
            'dui',
            'non',
            'hendrerit',
            'maximus',
            'dignissim',
            'felis',
            'libero',
            'rutrum',
            'sed',
            'venenatis',
            'lectus',
            'risus',
            'eget',
            'turpis',
            'pellentesque',
            'feugiat',
            'neque',
            'orci',
            'mi',
            'vel',
            'viverra',
            'pulvinar',
            'morbi',
            'condimentum',
            'varius',
            'natoque',
            'et',
            'magnis',
            'montes',
            'nisl',
            'aenean',
            'ullamcorper',
            'cras',
            'nulla',
            'justo',
            'suspendisse',
            'enim',
            'leo',
            'arcu',
            'aliquam',
            'gravidar',
            'fusce',
            'ante',
            'purus',
            'odio',
            'quisque',
            'vivamus',
            'maecenas',
            'necessitatibus',
            'saepe',
            'oluptatibus',
            'repellat',
            'pharetra',
            'interdum',
            'optio',
            'soluta',
            'impedit',
            'tristique',
            'mattis',
            'dictum'
        ];

        $length = mt_rand($min, $max);
        $recentWords     = [];
        $recentWordSize  = 10;
        $paragraph       = '';
        for ($i = 1; $i <= $length; $i++) {
            $word = self::pickWord($wordList, $recentWords);

            // restrict recent words to appopriate size
            if (count($recentWords) >= $recentWordSize) {
                // remove oldest entry
                array_shift($recentWords);
            }

            $recentWords[] = $word;

            if ($i === 1) {
                $word = ucfirst($word);
            } else {
                // space before each word except first
                $paragraph .= ' ';
            }

            $paragraph .= $word;
        }
        $paragraph .= '.';

        return $paragraph;
    }

    public function pickWord(array $wordList, array $recentWords): string
    {
        $word = $wordList[mt_rand(0, count($wordList) - 1)];

        if (in_array($word, $recentWords)) {
            $word = self::pickWord($wordList, $recentWords);
        }

        return $word;
    }
}
