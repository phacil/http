<?php

namespace Phacil\HTTP;

class Response extends \Symfony\Component\HttpFoundation\Response{
    
    use \Phacil\Common\Traits\InstanceTrait;
    
    public function __construct($content = '', $status = 200, $headers = array()) {
        parent::__construct($content, $status, $headers);
        self::$instance = $this;
        return $this;
    }
}
