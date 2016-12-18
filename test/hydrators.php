<?php

use Krak\Marshal;

describe('#classNameHydrator', function() {
    it('instantiates the class name and passes along to hydrator', function() {
        $hydrate = Marshal\classNameHydrator(function($obj) {
            return $obj;
        });
        $obj = $hydrate('StdClass', []);
        assert($obj instanceof StdClass);
    });
});
describe('#publicPropertyHydrator', function() {
    it('assigns the properties to an object via the publicly accessable properties', function() {
        $hydrate = Marshal\publicPropertyHydrator();
        $obj = new StdClass();
        $obj->a = null;
        $obj->b = null;

        $obj = $hydrate($obj, [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ]);

        assert($obj->a == 1 && $obj->b == 2);
    });
});
