<?php

namespace Krak\Marshal;

/** interface for grabbing keys and value */
interface Access {
    public function get($data, $key, $default = null);
    public function has($data, $key);
}

function access() {
    static $access;
    if (!$access) {
        $access = new AnyAccess();
    }
    return $access;
}

class AnyAccess implements Access {
    private $key_acc;
    private $property_acc;

    public function __construct(Access $key_acc = null, Access $property_acc = null) {
        $this->key_acc = $key_acc ?: new ArrayKeyAccess();
        $this->property_acc = $property_acc ?: new ObjectPropertyAccess();
    }

    public function get($data, $key, $default = null) {
        if (is_array($data)) {
            return $this->key_acc->get($data, $key, $default);
        }

        return $this->property_acc->get($data, $key, $default);
    }
    public function has($data, $key) {
        if (is_array($data)) {
            return $this->key_acc->has($data, $key);
        }

        return $this->property_acc->has($data, $key);
    }
}

class ArrayKeyAccess implements Access {
    public function get($data, $key, $default = null) {
        if (!array_key_exists($key, $data)) {
            return $default;
        }

        return $data[$key];
    }
    public function has($data, $key) {
        return array_key_exists($key, $data);
    }
}

class ObjectPropertyAccess implements Access {
    public function get($data, $key, $default = null) {
        if (!property_exists($data, $key)) {
            return $default;
        }

        return $data->{$key};
    }
    public function has($data, $key) {
        return property_exists($data, $key);
    }
}
