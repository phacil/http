<?php

namespace Phacil\Component\HTTP;

class Request {
    private static $module = null;
    private static $controller = null;
    private static $action = null;
    private static $params = [];
        
    private static $method = 'get';
    private static $url = null;
    private static $uri = null;
    private static $prefix = null;
    private static $args = [];
    private static $get = [];
    
    private static $data = [];
    
    private $request = array('url'=>'',
                             'prefix'=>'',
                             'module'=>'',
                             'controller'=>'',
                             'action'=>'',
                             'params'=>array(),
                             'args'=>array() );
    
    static function init(){
        Server::init($_SERVER);
        self::setMethod(Server::get('REQUEST_METHOD'));        
        self::__parseUri();
        self::escapePostFilesGetData();
        //self::__diffUrl();
    }
    
    private static function __parseUri(){       
        
        if(Server::get('REDIRECT_QUERY_STRING')!=null){
            self::setUri(Server::get('REDIRECT_QUERY_STRING'));
            $pos = strpos(Server::get('REDIRECT_QUERY_STRING'), '&');
            
            if($pos !== false){
                $path = str_replace(substr(Server::get('REDIRECT_QUERY_STRING'), $pos), '',Server::get('REDIRECT_QUERY_STRING'));
            }else{
                $path = Server::get('REDIRECT_QUERY_STRING');
            }
                        
        }else if(Server::get('REQUEST_URI') != null){
            self::setUri(Server::get('REQUEST_URI'));
            $pos = strpos(Server::get('REQUEST_URI'), '?');
            
            if($pos !== false){
                $path = str_replace(substr(Server::get('REQUEST_URI'), $pos), '',Server::get('REQUEST_URI'));
            }else{
                $path = Server::get('REQUEST_URI');
            }                
            
        }else{
            $path = '';
        }
             
        $path = ($path != '/' && !empty($path))
                ?rtrim($path, '/')
                :'/';
        
        self::setUrl($path);
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
    
    public static function info($key = null){
        if(!$key){
            return array(
                'module' => self::$module,
                'controller' => self::$controller,
                'action' => self::$action,
                'params' => self::$params,

                'method' => self::$method,
                'url' => self::$url,
                'uri' => self::$uri,
                'prefix' => self::$prefix,
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
