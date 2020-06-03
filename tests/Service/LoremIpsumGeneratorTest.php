<?php

namespace App\Tests\Service;

use App\Service\LoremIpsumGenerator;
use PHPUnit\Framework\TestCase;

class LoremIpsumGeneratorTest extends TestCase
{
    public function testParagraph(): void
    {
        $ipsum  = new LoremIpsumGenerator();
        $text = $ipsum->getParagraph(10);

        // no duplicates
        $text = explode(' ', $text);
        $this->assertTrue(count($text) === count(array_unique($text)));
    }
}
