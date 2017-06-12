<?php

use Krak\Marshal;

describe("Krak Marshal", function() {
    describe('Accessors', function() {
        require_once __DIR__ . '/accessors.php';
    });
    describe('Util', function() {
        require_once __DIR__ . '/util.php';
    });
    describe('Marshalers', function() {
        require_once __DIR__ . '/marshalers.php';
    });
    describe('Hydrators', function() {
        require_once __DIR__ . '/hydrators.php';
    });
    describe('Unmarshalers', function() {
        require_once __DIR__ . '/unmarshal.php';
    });
});
