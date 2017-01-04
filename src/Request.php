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
        self::setMethod(server()->get('REQUEST_METHOD'));
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
        
        if(server()->get('REDIRECT_QUERY_STRING')!=null){
            self::setUrl(server()->get('REDIRECT_QUERY_STRING'));
            $pos = strpos(server()->get('REDIRECT_QUERY_STRING'), '&');
            
            if($pos !== false){
                $path = str_replace(substr(server()->get('REDIRECT_QUERY_STRING'), $pos), '',server()->get('REDIRECT_QUERY_STRING'));
            }else{
                $path = server()->get('REDIRECT_QUERY_STRING');
            }
                        
        }else if(server()->get('REQUEST_URI') != null){
            self::setUrl(server()->get('REQUEST_URI'));
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

        self::setArgs(self::__extractArgs($path));        
        self::setUri($path);
        return $path;

    }
    
    private static function escapePostFilesGetData() {
        if ( get_magic_quotes_gpc() ) {
            $_POST   = stripSlashesDeep($_POST  );
            $_COOKIE = stripSlashesDeep($_COOKIE);
            $_FILES = stripSlashesDeep($_FILES);
            $_GET = stripSlashesDeep($_GET);
        }
        self::setData(array_merge(self::getData(), $_POST));
        self::setData(array_merge(self::getData(), $_FILES));
        
        $get = [];
        
        foreach($_GET as $k => $val){
            if($val == ''){continue;}
            $get[$k] = $val;
        }
        
        self::setGet($get);

        $_POST = $_FILES = $_GET = [];
        
        if(isset(self::getData()['_method'])){
            self::setMethod(self::getData()['_method']);
        }
    }
    
    public static function getModule() {
        return self::$module;
    }

    public static function getController() {
        return self::$controller;
    }

    public static function getAction() {
        return self::$action;
    }

    public static function getParams() {
        return self::$params;
    }

    public static function getMethod() {
        return self::$method;
    }

    public static function getUrl() {
        return self::$url;
    }

    public static function getPrefix() {
        return self::$prefix;
    }

    public static function getArgs() {
        return self::$args;
    }

    public static function getData() {
        return self::$data;
    }
    
    public static function getGet() {
        return self::$get;
    }
    
    public static function getUri() {
        return self::$uri;
    }

    public static function setModule($module) {
        self::$module = $module;
    }

    public static function setController($controller) {
        self::$controller = $controller;
    }

    public static function setAction($action) {
        self::$action = $action;
    }

    public static function setParams($params) {
        self::$params = $params;
    }

    public static function setMethod($method) {
        self::$method = $method;
    }

    public static function setUrl($url) {
        self::$url = $url;
    }

    public static function setPrefix($prefix) {
        self::$prefix = $prefix;
    }

    public static function setArgs($args) {
        self::$args = $args;
    }

    public static function setData($data) {
        self::$data = is_array($data)?$data:(array)$data;
    }
    
    public static function setGet($get) {
        self::$get = $get;
    }
    
    public static function setUri($uri) {
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
}
