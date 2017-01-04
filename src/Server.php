<?php

namespace Phacil\HTTP;

class Server {
    
    use InstanceTrait;
    
    protected static $collection = [];

    public function __construct() {
        $this->init($_SERVER);
        self::$instance = $this;
        return $this;
    }
    
    public static function init($serverArray = [])
    {
        foreach($serverArray as $k => $item){
            self::$collection[$k] = $item;
            $_SERVER[$k] = null;
        }
    }
            
    public static function getCollection() {
        return self::$collection;
    }
    
    public static function get($key) 
    {
        if(isset(self::$collection[$key]))
        {
            return self::$collection[$key];
        }
        return false;
    }
    
    public static function getAll() {
        return self::getCollection();
    }
    
}
