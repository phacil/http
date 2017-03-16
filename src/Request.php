<?php

namespace Phacil\HTTP;

class Request {
    
    use InstanceTrait;
    
    private static $method = 'get';
    private static $url = null;
    private static $uri = null;
    
    private static $prefix = null;
    private static $module = null;
    private static $controller = null;
    private static $action = null;
    private static $params = [];    
    
    private static $args = [];
    private static $get = [];    
    private static $data = [];
    
    public function __construct() {
        $this->init();
        self::$instance = $this;
        return $this;
    }
        
    public static function init(){        
        self::method(server()->get('REQUEST_METHOD'));
        self::__parseUri();
        self::escapePostFilesGetData();        
    }
    
    private function __extractArgs($path){
        $parts = explode('/', $path);
        $new_parts = $args = [];
        foreach ($parts as $part) {
            if(strpos($part, '=')){
                list($k, $v) = explode('=', $part);
                $args[$k] = $v;
            }
        }        
        return $args;
    }
    
    private static function __parseUri(){
        
        $request = !is_null(server()->get('REDIRECT_QUERY_STRING'))
                ?server()->get('REDIRECT_QUERY_STRING')
                :server()->get('QUERY_STRING');
        
        if($request!=null){
            self::url($request);
            $pos = strpos($request, '&');
            
            if($pos !== false){
                $path = str_replace(substr($request, $pos), '',$request);
            }else{
                $path = $request;
            }
                        
        }else if(server()->get('REQUEST_URI') != null){
            self::url(server()->get('REQUEST_URI'));
            $pos = strpos(server()->get('REQUEST_URI'), '?');
            
            if($pos !== false){
                $path = str_replace(substr(server()->get('REQUEST_URI'), $pos), '',server()->get('REQUEST_URI'));
            }else{
                $path = server()->get('REQUEST_URI');
            }                
            
        }else{
            $path = '';
        }
             
        $path = ($path != '/' && !empty($path))
                ?rtrim($path, '/')
                :'/';

        self::args(self::__extractArgs($path));
        self::uri($path);
        return $path;

    }
    
    private static function escapePostFilesGetData() {
        if ( get_magic_quotes_gpc() ) {
            $_POST   = stripSlashesDeep($_POST  );
            $_COOKIE = stripSlashesDeep($_COOKIE);
            $_FILES = stripSlashesDeep($_FILES);
            $_GET = stripSlashesDeep($_GET);
        }
        self::data(array_merge(self::data(), $_POST));
        self::data(array_merge(self::data(), $_FILES));
        
        $get = [];
        
        foreach($_GET as $k => $val){
            if($val == ''){continue;}
            $get[$k] = $val;
        }
        
        self::get($get);

        $_POST = $_FILES = $_GET = [];
        
        if(isset(self::data()['_method'])){
            self::method(self::data()['_method']);
        }
    }
    
    public static function module($module = false) {
        if($module === false){
            return self::$module;
        }
        self::$module = $module;
    }

    public static function controller($controller = false) {
        if($controller === false){
            return self::$controller;
        }
        self::$controller = $controller;
    }

    public static function action($action = false) {
        if($action === false){
            return self::$action;
        }
        self::$action = $action;
    }

    public static function params($params = false) {
        if($params === false){
            return self::$params;
        }
        self::$params = $params;
    }

    public static function method($method = false) {
        if($method === false){
            return self::$method;
        }
        self::$method = $method;
    }

    public static function url($url = false) {
        if($url === false){
            return self::$url;
        }
        self::$url = $url;
    }

    public static function prefix($prefix = false) {
        if($prefix === false){
            return self::$prefix;
        }
        self::$prefix = $prefix;
    }

    public static function args($args = false) {
        if($args === false){
            return self::$args;
        }
        self::$args = $args;
    }

    public static function data($data = false) {
        if($data === false){
            return self::$data;
        }
        self::$data = is_array($data)?$data:(array)$data;
    }
    
    public static function get($get = false) {
        if($get === false){
            return self::$get;
        }
        self::$get = $get;
    }
    
    public static function uri($uri = false) {
        if($uri === false){
            return self::$uri;
        }
        self::$uri = $uri;
    }
    
    public static function suppressArgs($path){
        $parts = explode('/', $path);
        $new_parts = $args = [];
        foreach ($parts as $part) {
            if(!strpos($part, '=')){                
               $new_parts[] = $part;
            }
        }
        return join('/',$new_parts);
    }
    
    public static function info($key = null){
        if(!$key){
            return array(
                'method' => self::$method,
                'url' => self::$url,
                'uri' => self::$uri,    
                
                'prefix' => self::$prefix,
                'module' => self::$module,
                'controller' => self::$controller,
                'action' => self::$action,
                'params' => self::$params,
                
                'args' => self::$args,
                'get' => self::$get,
                'data' => self::$data,
            );
        }else if(isset(self::${$key})){
            return self::${$key};
        }
        return false;
    }
    
    public static function is($method){
        if(is_array($method)){
            foreach ($method as $m){
                if (strtoupper($m) == self::$method){
                    return true;
                }
            }            
        }else{
            return strtoupper($method) == self::$method;
        }
        return false;        
    }
    
    
    public static function isAjax(){
        if(!empty(server()->get('HTTP_X_REQUESTED_WITH')) && strtolower(server()->get('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest')
        {    
          return true;    
        }
        return false;
    }
}
