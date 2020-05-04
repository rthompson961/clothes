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
    public function testInt(string $input, int $expected): void
    {
        $request = new Request(['page' => $input], [], [], [], [], []);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $sanitiser  = new QueryStringSanitiser($requestStack);
        $result = $sanitiser->getInt('page');

        $this->assertTrue($result === $expected);
    }

    public function intProvider(): array
    {
        return [
            ['14', 14],
            ['14.3', 14],
            ['-14', 14],
            ['orange', 0],
            ['', 0]
        ];
    }

    public function testIntArray(): void
    {
        $requestStack = new RequestStack();
        $request = new Request(
            ['page' => ['14', '14.3', '-14', 'orange', '']],
            [],
            [],
            [],
            [],
            []
        );
        $requestStack->push($request);

        $sanitiser  = new QueryStringSanitiser($requestStack);
        $result = $sanitiser->getIntArray('page');
        $expected = [14, 14, 14, 0, 0];

        $this->assertTrue($result === $expected);
    }

   /**
     * @dataProvider choiceProvider
     */
    public function testChoice(string $input, string $expected): void
    {
        $requestStack = new RequestStack();
        $request = new Request(['fruit' => $input], [], [], [], [], []);
        $requestStack->push($request);

        $sanitiser  = new QueryStringSanitiser($requestStack);
        $choices = ['orange', 'apple', 'banana', 'strawberry'];
        $result = $sanitiser->getChoice('fruit', $choices, 'orange');

        $this->assertTrue($result === $expected);
    }

    public function choiceProvider(): array
    {
        return [
            ['banana', 'banana'],
            ['lemon', 'orange'],
            ['', 'orange']
        ];
    }
}
