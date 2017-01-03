<?php

namespace Phacil\HTTP;

class Server {
    
    protected static $collection = [];

    public static function init($serverArray = [])
    {
        foreach($serverArray as $k => $item){
            self::$collection[$k] = $item;
            $_SERVER[$k] = null;
        }
    }
    
    public function get($key) 
    {
        if(isset(self::$collection[$key]))
        {
            return self::$collection[$key];
        }
        return false;
    }
    
    public function getAll() {
        return self::getCollection();
    }
    
    public static function getCollection() {
        return self::$collection;
    }
}
