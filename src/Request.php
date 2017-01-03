<?php

namespace Phacil\HTTP;

class Request {
    
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
        
    static function init(){
        Server::init($_SERVER);
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
    
    static function getModule() {
        return self::$module;
    }

    static function getController() {
        return self::$controller;
    }

    static function getAction() {
        return self::$action;
    }

    static function getParams() {
        return self::$params;
    }

    static function getMethod() {
        return self::$method;
    }

    static function getUrl() {
        return self::$url;
    }

    static function getPrefix() {
        return self::$prefix;
    }

    static function getArgs() {
        return self::$args;
    }

    static function getData() {
        return self::$data;
    }
    
    static function getGet() {
        return self::$get;
    }
    
    static function getUri() {
        return self::$uri;
    }

    static function setModule($module) {
        self::$module = $module;
    }

    static function setController($controller) {
        self::$controller = $controller;
    }

    static function setAction($action) {
        self::$action = $action;
    }

    static function setParams($params) {
        self::$params = $params;
    }

    static function setMethod($method) {
        self::$method = $method;
    }

    static function setUrl($url) {
        self::$url = $url;
    }

    static function setPrefix($prefix) {
        self::$prefix = $prefix;
    }

    static function setArgs($args) {
        self::$args = $args;
    }

    static function setData($data) {
        self::$data = is_array($data)?$data:(array)$data;
    }
    
    static function setGet($get) {
        self::$get = $get;
    }
    
    static function setUri($uri) {
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
