<?php

namespace App\Tests\Service;

use App\Service\LoremIpsumGenerator;
use PHPUnit\Framework\TestCase;

class LoremIpsumGeneratorTest extends TestCase
{
    private int $minSentence = 16;
    private int $maxSentence = 40;

    public function testSentence(): void
    {
        $ipsum  = new LoremIpsumGenerator();
        $sentence = $ipsum->getSentence();
        $length  = str_word_count($sentence);

        // paragraph length
        $this->assertTrue($length >= $this->minSentence && $length <= $this->maxSentence);
        // starts with a capital letter
        $this->assertTrue(ctype_upper(substr($sentence, 0, 1)));
        // ends with a fullstop
        $this->assertStringEndsWith('.', $sentence);
    }

    public function testParagraph(): void
    {
        $ipsum  = new LoremIpsumGenerator();
        $paragraph = $ipsum->getParagraph();
        $length  = str_word_count($paragraph);

        // paragraph length
        $this->assertTrue($length <= $this->maxSentence * 3);
        $this->assertTrue($length >= $this->minSentence * 3);

        $this->assertTrue(substr_count($paragraph, '.') == 3);
    }

    public function testWord(): void
    {
        $wordList    = ['apple', 'orange', 'banana', 'strawberry'];
        $recentWords = ['apple', 'strawberry'];

        $ipsum  = new LoremIpsumGenerator();
        $words  = [];
        for ($i = 1; $i <= 2; $i++) {
            $words[] = $ipsum->pickWord($wordList, $recentWords);
            $recentWords[] = end($words);
        }

        $this->assertCount(2, $words);
        $this->assertContains('orange', $words);
        $this->assertContains('banana', $words);
    }
}
