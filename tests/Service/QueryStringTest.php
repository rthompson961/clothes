<?php

namespace App\Tests\Service;

use App\Service\QueryString;
use PHPUnit\Framework\TestCase;

class QueryStringTest extends TestCase
{
    /**
     * @dataProvider queryStringProvider
     */
    public function testCsvToArray(?string $arg, array $expected): void
    {
        $query  = new QueryString();
        $result = $query->csvToArray($arg, $expected);

        $this->assertTrue($result === $expected);
    }

    public function queryStringProvider(): array
    {
        return [
            'null'                   => [null, []],
            'commas only'            => [',,,,,', []],
            'single int'             => ['3', [3]],
            'single non-int'         => ['apple', []],
            'contains non-int'       => ['3,2,apple', [2, 3]],
            'contains negative int'  => ['3,2,-1', [1, 2, 3]],
            'contains zero'          => ['3,2,0', [2, 3]],
            'contains missing value' => ['3,2,,', [2, 3]]
        ];
    }
}
