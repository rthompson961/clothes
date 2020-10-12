<?php

namespace App\Tests\Service;

use App\Service\LoremIpsum;
use PHPUnit\Framework\TestCase;

class LoremIpsumTest extends TestCase
{
    public function testParagraph(): void
    {
        $ipsum  = new LoremIpsum();
        $result = $ipsum->getParagraph(20);

        $result = explode(' ', $result);
        
        // result is appropriate length and contains no duplicates
        $this->assertTrue(count(array_unique($result)) == 20);
    }
}
