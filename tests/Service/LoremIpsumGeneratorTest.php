<?php

namespace App\Tests\Service;

use App\Service\LoremIpsumGenerator;
use PHPUnit\Framework\TestCase;

class LoremIpsumGeneratorTest extends TestCase
{
    public function testParagraph(): void
    {
        $ipsum  = new LoremIpsumGenerator();
        $paragraph = $ipsum->getParagraph();
        $length  = str_word_count($paragraph);

        // paragraph length
        $this->assertTrue($length >= 80 && $length <= 120);
        // starts with a capital letter
        $this->assertTrue(ctype_upper(substr($paragraph, 0, 1)));
        // ends with a fullstop
        $this->assertStringEndsWith('.', $paragraph);
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
