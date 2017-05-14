<?php

namespace Krak\Marshal\Util;

use Closure,
    DateTime;

function reduce($data, $map, $start = null) {
    $acc = $start;
    foreach ($data as $key => $value) {
        $acc = $map($acc, $value, $key);
    }
    return $acc;
}

function map($data, $map) {
    return reduce($data, function($acc, $v, $k) use ($map) {
        $acc[$k] = $map($v, $k);
        return $acc;
    }, []);
}
