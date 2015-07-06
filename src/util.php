<?php

namespace Krak\Marshal;

use Closure,
    DateTime;

/**
 * marshal a value by the given marshaler. There are multiple supported
 * marshalers, so this function will marshal a value by any of those marshalers
 * allowing a uniform syntax for marshaling values
 * @param $marshaler
 * @param $value
 * @return mixed
 */
function marshal($marshaler, $value) {
    if ($marshaler instanceof Marshaler) {
        return $marshaler->marshal($value);
    }
    else if ($marshaler instanceof Closure) {
        return $marshaler($value);
    }
    else if (is_callable($marshaler)) {
        return call_user_func($marshaler, $value);
    }
    else {
        throw new InvalidMarshalerException();
    }
}

/**
 * Convert a DateTime object to a timestamp
 * @param mixed $val
 * @return int|null the timestamp if a datetime object or null
 */
function timestamp($val)
{
    if ($val instanceof DateTime) {
        return $val->getTimestamp();
    }

    return null;
}
