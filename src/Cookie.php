<?php

namespace Phacil\HTTP;

class Cookie {

    use InstanceTrait;

    public function __construct()
    {
        self::$instance = $this;
        return $this;
    }

}
