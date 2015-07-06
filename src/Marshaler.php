<?php

namespace Krak\Marshal;

interface Marshaler
{
    /**
     * @return mixed
     */
    public function marshal($value);
}
