<?php

namespace Krak\Marshal;

use RuntimeException;

/**
 * InvalidMarshalerException
 */
class InvalidMarshalerException extends RuntimeException
{
    public function __construct() {
        $msg = 'Marshaler must be an instance of Krak\Marshal\Marshaler, ' .
            'Closure, or a callable';
        parent::__construct($msg);
    }
}
