# Krak Marshal

A library for marshaling data with a functional design. This is useful for transforming/marshaling data for API output, hydrating serialized data, or for any other types types of transforming.

## Usage

```php
<?php

use Krak\Marshal as m;

$users = getUsers(); // get users from an orm or something

$m = m\map(function($user)
{
    return m\merge([
        m\keys(['id', 'first_name', 'last_name']),
        function($user) {
            return [
                'like_count' => (int) $user->like_count,
                'biography' => substr($user->biography, 0, 64),
                'created_at_ts' => $user->created_at->getTimestamp(),
            ];
        }
    ]);
});

$marshaled = $m($users);

/*
[
    [
        'id' => ...,
        'first_name' => ...,
        'last_name' => ...,
        'biography' => ...,
        'created_at_ts' => ...,
    ],
    ...
]
*/
```

## Accessors

For certain marshalers, you'll want to marshal an entity in the form of the array or object. For that, we have accessors. An accessor implements the `Krak\Marshal\Access` interface.

```php
<?php

interface Access {
    public function get($data, $key, $default = null);
    public function has($data, $key);
}
```

usage:

```php
<?php

$access = new Krak\Marshal\ArrayKeyAccess();
$data = ['a' => 1];
$access->has($data, 'a'); // true
$access->has($data, 'b'); // false
$access->get($data, 'a'); // 1
$access->get($data, 'b', 0); // 0
```

## Hydrators

Hydrators are used to marshal array data into an object. Each hydrator is any callable with the following interface

```php
<?php

interface Hydrator {
    public function __invoke($class, $data);
}
```

```php
<?php

class MyClass {
    public $a;
    public $b;
}

$hydrate = publicPropertyHydrator();
$obj = $hydrate(new MyClass(), [
    'a' => 1,
    'b' => 2,
]);

// $obj now is hydrated with those values
```

## API

### hydrate($class, $hydrator = null)

Creates a marshaler that will hydrate the data with the given `$class` parameter and forwards the `$class` and the `$data` from the marshaler to the `$hydrator`. If no hydrator is supplied, the default `hydrator()` will be used.

```php
<?php

class MyClass {
    public $a;
}

$marshal = Krak\Marshal\hydrate(MyClass::class);
$obj = $marshal(['a' => 1]);
assert($obj instanceof MyClass);
```

### keyMap($map)

transforms the keys of the data into a new key

### rename(array $map)

renames key fields into a new name

### only(array $fields)

It only includes the given fields. Everything else is filtered out.

### except(array $fields)

It includes all except the given fields. Everything else is kept.

### filter(callable $filter)

Filters the collection via the filter func which has the signature `$filter($value, $key): bool`

### dates($format = 'r')

Formats all instances of `DateTimeInterface` with the given format specifier.

### objectVars()

Converts the properties of an object into an array. This is just an alias of `get_object_vars`.

### typeCast(array $fields, $type)

Type casts the given fields into a specific type.

### pipe($marshalers)

Creates a marshaler that pipes the result of one marshaler into the next marshaler

```php
<?php

$m = pipe([camelizeKeys(), keys(['id', 'firstName'])]);
$m(['id' => 1, 'first_name' => 2]);
```

### merge($marshalers)

Creates a marshaler that will apply $marshalers onto a value and then merges all of the results with array_merge. This expects the $marshalers to return arrays.

### keys($fields, Access $acc = null)

Creates a marshaler that retuns the fields of the data

### map($marshaler)

Creates a marshaler which takes a collection and returns an array of each of the marshaled items

### collection($marshalers, Access $acc = null)

Creates a marshaler of a collection based off of the collection of marshalers passed in. Each $marshaler in `$key => $marshaler` will marshal each $value in `$key => $value` based on the `$key`.

### on($marshalers, Access $acc = null)

Similar to `collection`, it marshals the fields of the collection based off of the map of marshalers passed in. The only difference is that it updates the fields in the original collection and returns the entire modified collection.

### stringyKeys($cb)

Maps a key by allowing a stringy instance passed to callback for key manipulation

### underscoredKeys()

Converts the keys to underscore style keys using the `Stringy::underscored` function

### camelizeKeys()

Converts the keys to camelCase style keys using the `Stringy::camelize` function

### identity()

Creates a marshaler for the identity function

### mock($val)

Creates a marshaler that returns $val always. This is useful for testing

### notnull($marshaler)

Creates a marshaler that will not allow null values to be passed to the marshaler. if a null value is passed, it just returns null and doesn't call the marshaler

### hydrator()

Returns a statically cached instance of a default hydrator instance.

### classNameHydrator($hydrator)

Treats the `$class` parameter as a class name and will instantiate a class and delgate to the internal hydrator

### publicPropertyHydrator()

Assigns the properties from array into the `$class` object passed in via publicly accessible properties.

### class ArrayKeyAccess

Performs access on arrays via the key.

### class ObjectPropertyAccess

Performs access on object properties via the property name

### class AnyAccess

Delegates access to either `ArrayKeyAccess` or `ObjectPropertyAccess` if the data is an array or not.

This is the default accessor used.

### access()

Returns a statically cached instance of AnyAccess
