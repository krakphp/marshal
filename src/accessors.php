<?php

namespace Krak\Marshal;

function key_accessor()
{
    return [
        function($data, $key) {
            return $data[$key];
        },
        function($data, $key) {
            return array_key_exists($key, $data);
        },
    ];
}

function property_accessor()
{
    return [
        function($data, $prop) {
            return $data->{$prop};
        },
        function($data, $prop) {
            return property_exists($data, $prop);
        },
    ];
}
