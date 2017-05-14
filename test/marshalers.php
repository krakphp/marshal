<?php

use Krak\Marshal as m;

describe('#only', function() {
    it('only allows the given fields', function() {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];
        $m = m\only(['a']);
        assert($m($data) == ['a' => 1]);
    });
});
describe('#except', function() {
    it('allows all except the given fields', function() {
        $data = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];
        $m = m\except(['b', 'c']);
        assert($m($data) == ['a' => 1]);
    });
});
describe('#objectVars', function() {
    it('converts an object into an array', function() {
        $data = (object) ['a' => 1];
        $m = m\objectVars();
        assert($m($data) == ['a' => 1]);
    });
});
describe('#dates', function() {
    it('formats any dates', function() {
        $data = [new \DateTime()];
        $data[0]->format('r');
        $m = m\dates();
        assert($m($data)[0] == $data[0]->format('r'));
    });
});
describe('#typeCast', function() {
    it('type casts certain fields into a type', function() {
        $data = ['a' => '0', 'b' => 1];
        $m = m\typeCast(['a', 'b'], 'bool');
        $data = $m($data);
        assert($data['a'] === false && $data['b'] === true);
    });
});

describe('#mock', function() {
    it('returns the same data always', function() {
        $m = m\mock('abc');
        assert('abc' == $m('def'));
    });
});
describe('#map', function() {
    it('maps an iterable and marshals the individual values', function() {
        $m = m\map(m\mock(2));
        assert([2] == $m([1]));
    });
});
describe('#pipe', function() {
    it('pipes the result of one marshaler into the parameters of the next', function() {
        $add = function($val) {
            return $val + 1;
        };
        $m = m\pipe([$add, $add]);
        assert(2 == $m(0));
    });
});
describe('#merge', function() {
    it('merges result arrays together', function() {
        $m = m\merge([
            m\mock(['a' => 1]),
            m\mock(['b' => 2])
        ]);
        assert(['a' => 1, 'b' => 2] == $m(null));
    });
});
describe('#keys', function() {
    it('returns a subset of the data passed in if the array keys match the array of keys provided', function() {
        $m = m\keys(['id']);
        assert(['id' => 1] == $m(['id' => 1, 'a' => 2]));
    });
});
describe('#collection', function() {
    it('marshals sub fields and returns those marshaled values as an array', function() {
        $m = m\collection([
            'a' => m\mock(1),
        ]);

        assert(['a' => 1] == $m(['a' => null, 'b' => null]));
    });
});
describe('#on', function() {
    it('delegates marshaling to sub fields', function() {
        $m = m\on([
            'a' => m\mock(1),
        ]);

        assert(['a' => 1, 'b' => null] == $m(['a' => null, 'b' => null]));
    });
});
describe('#identity', function() {
    it('returns the data that is passed into the marshaler', function() {
        $m = m\identity();
        assert(1 == $m(1));
    });
});
describe('#notnull', function() {
    it('returns nothing if the field is null', function() {
        $m = m\notnull(m\mock(1));
        assert(null === $m(null));
    });
    it('delegates to the wrapped marshaler if is NOT null', function() {
        $m = m\notnull(m\mock(1));
        assert(1 == $m(1));
    });
});
// keyMap, rename, hydrate, stringyKeys, underscoredKeys, camelizeKeys
describe('#keyMap', function() {
    it('maps the keys of a set via a callback', function() {
        $m = m\keymap('strtoupper');
        assert(['A' => 1] == $m(['a' => 1]));
    });
});
describe('#rename', function() {
    it('renames a set of field by the map of old => new', function() {
        $m = m\rename(['a' => 'b']);
        assert(['b' => 1] == $m(['a' => 1]));
    });
});
describe('#hydrate', function() {
    it('hydrates the data using a hydrator', function() {
        $m = m\hydrate('a', function($cls, $data) { return $cls . $data; });
        assert('ab' == $m('b'));
    });
});
describe('#stringyKeys', function() {
    it('maps keys using a stringy callback', function() {
        $m = m\stringyKeys(function($s) { return $s->last(1); });
        assert(['b' => 1] == $m(['ab' => 1]));
    });
});
describe('#underscoredKeys', function() {
    it('transforms the keys into underscored style', function() {
        $m = m\underscoredKeys();
        assert(['under_score' => 1] == $m(['UnderScore' => 1]));
    });
});
describe('#camelizeKeys', function() {
    it('transforms the keys into camel case style', function() {
        $m = m\camelizeKeys();
        assert(['camelCase' => 1] == $m(['Camel_Case' => 1]));
    });
});
