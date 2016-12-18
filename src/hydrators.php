<?php

namespace Krak\Marshal;

interface Hydrator {
    public function __invoke($class, $data);
}

/** returns a default statically cached instance of a hydrator */
function hydrator() {
    static $hydrate;

    if (!$hydrate) {
        $hydrate = classNameHydrator(publicPropertyHydrator());
    }

    return $hydrate;
}

function classNameHydrator($hydrator) {
    return function($class, $data) use ($hydrator) {
        return $hydrator(new $class(), $data);
    };
}

function publicPropertyHydrator() {
    return function($obj, $data) {
         foreach ($data as $key => $value) {
             if (property_exists($obj, $key)) {
                 $obj->{$key} = $value;
             }
         }

         return $obj;
    };
}
