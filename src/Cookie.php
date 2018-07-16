<?php

namespace Phacil\HTTP;

class Cookie {

    use \Phacil\Common\Traits\InstanceTrait;

    public function __construct()
    {
        self::$instance = $this;
        return $this;
    }

}
