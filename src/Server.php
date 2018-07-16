<?php

namespace Phacil\HTTP;
use Phacil\Common\AbstractClass\AbstractCollection;

class Server extends AbstractCollection{
    
    use \Phacil\Common\Traits\InstanceTrait;
    
    protected $type = self::TYPE_MIXED;
    protected $final = true;
    
    protected static $collection = [];

    public function __construct() {
        
        self::$instance = $this;
        
        return parent::__construct($_SERVER);
        
    }
    
}
