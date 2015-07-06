<?php

namespace Krak\Marshal;

/**
 * Marshaler
 * Simple interface for class based marshaling. You can use these along with
 * any closure or function.
 */
interface Marshaler
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function marshal($value);
}
