<?php

namespace Krak\Marshal;

/**
 * Creates a marshaler that pipes the result of one marshaler into the
 * next marshaler
 * @param $marshalers an array of marshalers
 * @return \Closure
 */
function pipe($marshalers)
{
    return function($value) use ($marshalers) {
        foreach ($marshalers as $marshaler) {
            $value = marshal($marshaler, $value);
        }
        return $value;
    };
}

/**
 * Creates a marshaler that will apply $marshalers onto a value and then
 * merges all of the results with array_merge. This expects the $marshalers
 * to return arrays
 * @param $marshalers an array of marshalers that return arrays
 * @return \Closure
 */
function merge($marshalers)
{
    return function($values) use ($marshalers) {
        $res = [];
        foreach ($marshalers as $marshaler) {
            $res[] = marshal($marshaler, $values);
        }

        return call_user_func_array('array_merge', $res);
    };
}

/**
 * Creates a marshaler that retuns the fields of an array from the
 * $fields array
 * @param $fields
 * @return \Closure
 */
function keys($fields)
{
    return function($values) use ($fields) {
        $new_vals = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $values)) {
                $new_vals[$field] = $values[$field];
            }
        }
        return $new_vals;
    };
}

/**
 * Creates a marshaler that retuns the properties of an object from the
 * $fields array
 * @param $fields
 * @return \Closure
 */
function properties($fields)
{
    return function($obj) use ($fields) {
        $new_vals = [];
        foreach ($fields as $field) {
            if (property_exists($obj, $field)) {
                $new_vals[$field] = $obj->{$field};
            }
        }
        return $new_vals;
    };
}

/**
 * Creates a marshaler that returns a subset of the data passed in by
 * only getting the fields from the $fields array
 * @param array $acc Accessor for the data
 * @param $fields The fields that need to be marshaled
 * @return \Closure
 */
function fields($acc, $fields)
{
    return function($data) use ($acc, $fields) {
        $new_vals = [];
        list($get, $has) = $acc;
        foreach ($fields as $field) {
            if ($has($data, $field)) {
                $new_vals[$field] = $get($data, $field);
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
function map($marshaler)
{
    return function($values) use ($marshaler) {
        $marshaled = [];
        foreach ($values as $value) {
            $marshaled[] = marshal($marshaler, $value);
        }
        return $marshaled;
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
function collection($acc, $marshalers)
{
    return function($data) use ($acc, $marshalers, $skip_null) {
        list($get, $has) = $acc;
        $marshaled = [];
        foreach ($marshalers as $key => $marshaler) {
            if ($has($data, $key)) {
                $marshaled[$key] = marshal($marshaler, $get($data, $key));
            }
        }
        return $marshaled;
    };
}

/**
 * Creates a marshaler for the identity function
 */
function id() {
    return 'krak\marshal\id_marshaler';
}

/**
 * Alias of id
 */
function identity() {
    return id();
}

function id_marshaler($val)
{
    return $val;
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
function notnull($marshaler)
{
    return function($val) use ($marshaler) {
        if (is_null($val)) {
            return null;
        }

        return marshal($marshaler, $val);
    };
}
