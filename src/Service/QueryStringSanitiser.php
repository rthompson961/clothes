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

    public function getInt(string $key, int $default = 0): int
    {
        $val = $this->request->query->get($key, $default);

        return abs((int) $val);
    }

    public function getIntArray(string $key): array
    {
        $values = $this->request->query->get($key);
        $clean = [];

        if (!is_array($values)) {
            return $clean;
        }

        foreach ($values as $val) {
            $clean[] = abs((int) $val);
        }

        return $clean;
    }

    public function getChoice(string $key, array $choices, string $default): string
    {
        $val = $this->request->query->get($key);

        if (in_array($val, $choices)) {
            return $val;
        }

        return $default;
    }
}
