<?php

namespace App\Tests\Service;

use App\Service\QueryStringSanitiser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class QueryStringSanitiserTest extends TestCase
{
   /**
     * @dataProvider intProvider
     */
    public function testInt(array $input, int $expected): void
    {
        $request = new Request(['page' => $input[0]], [], [], [], [], []);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $sanitiser  = new QueryStringSanitiser($requestStack);
        $result = $sanitiser->get('page');

        $this->assertTrue($result === $expected);
    }

    public function intProvider(): array
    {
        return [
            [[[]], 1],
            [[[1, 2, 3]], 1],
            [[''], 1],
            [['orange'], 1],
            [['-1'], 1],
            [['0'], 1],
            [['14.3'], 14],
            [['14'], 14]
        ];
    }

   /**
     * @dataProvider intListProvider
     */
    public function testIntList(array $input, array $expected): void
    {
        $requestStack = new RequestStack();
        $request = new Request(['size' => $input[0]], [], [], [], [], []);
        $requestStack->push($request);

        $sanitiser  = new QueryStringSanitiser($requestStack);
        $result = $sanitiser->getList('size');

        $this->assertTrue($result === $expected);
    }

    public function intListProvider(): array
    {
        return [
            [[[]], []],
            [[[1, 2, 3]], []],
            [[''], []],
            [[' '], []],
            [['0'], []],
            [['0,4,orange, ,1,5,,-1'], [1 => 4, 4 => 1, 5 => 5, 7 => 1]]
        ];
    }

   /**
     * @dataProvider choiceProvider
     */
    public function testChoice(array $input, string $expected): void
    {
        $requestStack = new RequestStack();
        $request = new Request(['fruit' => $input[0]], [], [], [], [], []);
        $requestStack->push($request);

        $sanitiser  = new QueryStringSanitiser($requestStack);
        $choices = ['orange', 'apple', 'banana', 'strawberry'];
        $result = $sanitiser->getChoice('fruit', $choices);

        $this->assertTrue($result === $expected);
    }

    public function choiceProvider(): array
    {
        return [
            [[[]], 'orange'],
            [[[1, 2, 3]], 'orange'],
            [[''], 'orange'],
            [['lemon'], 'orange'],
            [['banana'], 'banana']
        ];
    }
}
