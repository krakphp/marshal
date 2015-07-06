# Krak Marshal

A library for marshaling data with a functional design. This is useful for transforming/marshaling data for API output or for any other types types of transforming.

## Usage

```
<?php

use Krak\Marshal as m;

$users = // get users from an orm or something

$m = m\map(function($user)
{
    return m\merge([
        m\properties(['id', 'first_name', 'last_name']),
        function($user) {
            return [
                'like_count' => (int) $user->like_count,
                'biography' => substr($user->biography, 0, 64),
                'created_at_ts' => m\timestamp($user->created_at),
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

### Accessors

For certain marshalers, you'll want to marshal an entity in the form of the array or object. For that, we have accessors. An accessor is just a tuple of functions for getting and setting based on the type of data.

- **key accessor**: the key accessor will access a key from an array
- **property accessor**: the property accessor will access a property from an object

```php
<?php

use Krak\Marshal as m;

list($get, $has) = m\key_accessor();

$data = ['id' => 1];

print_r($has($data, 'id')); // true
print_r($get($data, 'id')); // 1
```
