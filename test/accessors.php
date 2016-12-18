<?php

use Krak\Marshal;

function describeAccess($name, Marshal\Access $access, $data) {
    describe($name, function() use ($access, $data) {
        beforeEach(function() use ($access, $data) {
            $this->access = $access;
            $this->data = $data;
        });
        describe('->has', function() {
            it('checks if the key is available', function() {
                assert($this->access->has($this->data, 'b') == false);
            });
        });
        describe('->get', function() {
            it('grabs data if the key exists', function() {
                assert($this->access->get($this->data, 'a') == 1);
            });
            it('uses the default if the key does not exist', function() {
                assert($this->access->get($this->data, 'b', 2) == 2);
            });
        });
    });
}

describeAccess('ArrayKeyAccess', new Marshal\ArrayKeyAccess(), [
    'a' => 1
]);
describeAccess('ObjectPropertyAccess', new Marshal\ObjectPropertyAccess(), (object) [
    'a' => 1
]);
