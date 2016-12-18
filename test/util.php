<?php

use Krak\Marshal\Util;

describe('#reduce', function() {
    it('performs a reduction on an iterable', function() {
        $sum = util\reduce([1,1,1], function($acc, $v, $k) { return $acc + $v + $k; }, 0);
        assert($sum == 6);
    });
});
describe('#map', function() {
    it('maps an interable', function() {
        assert([2,3] == util\map([1,2], function($v) { return $v + 1; }));
    });
});
