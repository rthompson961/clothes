<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class QueryStringSanitiser
{
    private ?Request $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function get(string $key): int
    {
        $input = $this->request->query->get($key);

        if (is_array($input) || (int) $input < 1) {
            return 1;
        }

        return $input;
    }

    public function getList(string $key): array
    {
        $input = $this->request->query->get($key);

        if (is_array($input) || !$input) {
            return [];
        }

        // split comma separated list into array
        $result = explode(',', $input);
        // convert each element of array to positive integer
        array_walk($result, function (&$val) {
            $val = abs((int) $val);
        });
        // remove zero value elements
        $result = array_filter($result);

        return $result;
    }

    public function getChoice(string $key, array $choices): string
    {
        $input = $this->request->query->get($key);

        if (in_array($input, $choices)) {
            return $input;
        }

        return $choices[0];
    }
}
