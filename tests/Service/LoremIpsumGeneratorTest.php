<?php

namespace App\Tests\Service;

use App\Service\LoremIpsumGenerator;
use PHPUnit\Framework\TestCase;

class LoremIpsumGeneratorTest extends TestCase
{
    public function testParagraph(): void
    {
        $ipsum  = new LoremIpsumGenerator();
        $result = $ipsum->getParagraph(10);

        $result = explode(' ', $result);
        // result is appropriate length and contains no duplicates
        $this->assertTrue(count(array_unique($result)) == 10);
    }
}
