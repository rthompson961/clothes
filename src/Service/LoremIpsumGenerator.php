<?php

namespace App\Service;

class LoremIpsumGenerator
{
    public function getParagraph(int $size = 3): string
    {
        $paragraph = '';
        for ($i = 1; $i <= $size; $i++) {
            if ($i !== 1) {
                $paragraph .= ' ';
            }
            $paragraph .= self::getSentence();
        }

        return $paragraph;
    }

    public function getSentence(int $min = 16, int $max = 40): string
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

        $length = mt_rand($min, $max);
        $recent = [];
        $recentSize  = 10;
        $sentence  = [];
        for ($i = 1; $i <= $length; $i++) {
            $word = self::pickWord($list, $recent);

            // restrict recent words to appropriate size
            if (count($recent) >= $recentSize) {
                // remove oldest entry
                array_shift($recent);
            }

            $recent[] = $word;
            $sentence[] = $word;
        }

        // capitalise first word
        $sentence[0] = ucfirst($sentence[0]);
        $sentence = implode(' ', $sentence);
        $sentence .= '.';

        return $sentence;
    }

    public function pickWord(array $list, array $recent): string
    {
        $word = $list[mt_rand(0, count($list) - 1)];

        if (in_array($word, $recent)) {
            $word = self::pickWord($list, $recent);
        }

        return $word;
    }
}
