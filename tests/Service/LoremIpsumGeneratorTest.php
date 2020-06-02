<?php

namespace App\Tests\Service;

use App\Service\LoremIpsumGenerator;
use PHPUnit\Framework\TestCase;

class LoremIpsumGeneratorTest extends TestCase
{
    public function testParagraph(): void
    {
        $ipsum  = new LoremIpsumGenerator();
        $paragraph = $ipsum->getParagraph(10);

        // no duplicates
        $array = explode(' ', $paragraph);
        $this->assertTrue(count($array) === count(array_unique($array)));
        // starts with a capital letter and ends with fullstop
        $this->assertTrue(ctype_upper(substr($paragraph, 0, 1)));
        $this->assertStringEndsWith('.', $paragraph);
    }
}
