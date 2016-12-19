<?php

namespace Krak\Marshal;

use Stringy;

interface Marshal {
    public function __invoke($data);
}

/** marshaler which accepts class/object and optional hydrator and transforms the
    data by hydrating a class */
function hydrate($class, $hydrator = null) {
    $hydrator = $hydrator ?: hydrator();
    return function($data) use ($class, $hydrator) {
        return $hydrator($class, $data);
    };
}

/** transforms the keys of the data into a new key */
function keyMap($map) {
    return function($data) use ($map) {
        return Util\reduce($data, function($acc, $v, $k) use ($map) {
            $acc[$map($k)] = $v;
            return $acc;
        }, []);
    };
}

/** renames key fields into a new name */
function rename(array $map) {
    return keyMap(function($key) use ($map) {
        if (array_key_exists($key, $map)) {
            return $map[$key];
        }
        return $key;
    });
}

/**
 * Creates a marshaler that pipes the result of one marshaler into the
 * next marshaler
 * @param $marshalers an array of marshalers
 * @return \Closure
 */
function pipe($marshalers) {
    return function($value) use ($marshalers) {
        return Util\reduce($marshalers, function($acc, $marshaler) {
            return $marshaler($acc);
        }, $value);
    };
}

/**
 * Creates a marshaler that will apply $marshalers onto a value and then
 * merges all of the results with array_merge. This expects the $marshalers
 * to return arrays
 * @param $marshalers an array of marshalers that return arrays
 * @return \Closure
 */
function merge($marshalers) {
    return function($value) use ($marshalers) {
        return array_merge(...Util\map($marshalers, function($m) use ($value) {
            return $m($value);
        }));
    };
}

/**
 * Creates a marshaler that retuns the fields of the data
 * @param $fields
 * @return \Closure
 */
function keys($fields, Access $acc = null) {
    $acc = $acc ?: access();
    return function($data) use ($fields, $acc) {
        $new_vals = [];
        foreach ($fields as $field) {
            if ($acc->has($data, $field)) {
                $new_vals[$field] = $acc->get($data, $field);
            }
        }
        return $new_vals;
    };
}

/**
 * Creates a marshaler which takes a collection and returns an array of
 * each of the marshaled items
 * @param $marshaler The marshaler to apply to the array
 * @return \Closure
 */
function map($marshaler) {
    return function($values) use ($marshaler) {
        return Util\map($values, $marshaler);
    };
}

/**
 * Creates a marshaler of a collection based off of the collection of marshalers
 * passed in. Each $marshaler in $key => $marshaler will marshal each
 * $value in $key => $value based on the $key
 * @param array $acc an accessor which can be used to access the data
 * @param array $marshalers the collection of marshalers
 * @return \Closure
 */
function collection($marshalers, Access $acc = null) {
    $acc = $acc ?: access();
    return function($data) use ($marshalers, $acc) {
        $marshaled = [];
        foreach ($marshalers as $key => $marshaler) {
            if ($acc->has($data, $key)) {
                $marshaled[$key] = $marshaler($acc->get($data, $key));
            }
        }
        return $marshaled;
    };
}

/** perform these marshalers on the fields of data if they exist, only works on arrays
    for now. Works exactly the same way as `collection` except it retuns all of the data */
function on($marshalers, Access $acc = null) {
    $acc = $acc ?: access();
    return function($data) use ($marshalers, $acc) {
        foreach ($marshalers as $key => $marshaler) {
            if ($acc->has($data, $key)) {
                $data[$key] = $marshaler($acc->get($data, $key));
            }
        }
        return $data;
    };
}

/** Maps a key by allowing a stringy instance passed to callback for key manipulation */
function stringyKeys($cb) {
    return keyMap(function($key) use ($cb) {
        return (string) $cb(Stringy\create($key));
    });
}

/** Converts the keys to underscore style keys using the Stringy::underscored function */
function underscoredKeys() {
    return stringyKeys(function($s) {
        return $s->underscored();
    });
}

function camelizeKeys() {
    return stringyKeys(function($s) {
        return $s->camelize();
    });
}

/**
 * Creates a marshaler for the identity function
 */
function identity() {
    return function($val) {
        return $val;
    };
}

/**
 * Creates a marshaler that returns $val always. This is useful for testing
 * @param $val
 * @return \Closure The marshaler
 */
function mock($val) {
    return function($data) use ($val) {
        return $val;
    };
}

/**
 * Creates a marshaler that will not allow null values to be passed to the marshaler
 * if a null value is passed, it just returns null and doesn't call the marshaler
 */
function notnull($marshaler) {
    return function($val) use ($marshaler) {
        if (is_null($val)) {
            return;
        }

        return $marshaler($val);
    };
}
