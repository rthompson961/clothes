<?php

namespace App\Service;

class QueryString
{
    public function csvToArray(?string $list): array
    {
        if (!$list) {
            return [];
        }

        // split comma separated list into array
        $listArray = explode(',', $list);

        // convert each element of array to positive integer
        array_walk($listArray, function (&$val) {
            $val = abs((int) $val);
        });

        // remove zero value elements
        $listArray = array_filter($listArray);

        sort($listArray);

        return $listArray;
    }
}
