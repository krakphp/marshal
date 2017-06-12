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

function filter($data, $filter) {
    return reduce($data, function($acc, $v, $k) use ($filter) {
        if ($filter($v, $k)) {
            $acc[] = $v;
        }
        return $acc;
    }, []);
}

function all($data, $test) {
    foreach ($data as $key => $value) {
        if (!$test($value, $key)) {
            return false;
        }
    }

    return true;
}

function some($data, $test) {
    foreach ($data as $key => $value) {
        if ($test($value, $key)) {
            return true;
        }
    }

    return false;
}
