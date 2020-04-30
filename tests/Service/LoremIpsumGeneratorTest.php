<?php

namespace App\Tests\Service;

use App\Service\LoremIpsumHelper;
use PHPUnit\Framework\TestCase;

class LoremIpsumHelperTest extends TestCase
{
    public function testParagraph(): void
    {
        $ipsum  = new LoremIpsumHelper();
        $result = $ipsum->getParagraph();
        $count  = str_word_count($result);

        // paragraph length
        $this->assertTrue($count >= 80 && $count <= 120);
        // starts with a capital letter
        $this->assertTrue(ctype_upper(substr($result, 0, 1)));
        // ends with a fullstop
        $this->assertStringEndsWith('.', $result);
    }

    public function testWord(): void
    {
        $wordList    = ['apple', 'orange', 'banana', 'strawberry'];
        $recentWords = ['apple', 'strawberry'];

        $ipsum  = new LoremIpsumHelper();
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
