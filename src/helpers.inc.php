<?php

/**
 * 
 * @return \Phacil\HTTP\Server
 */
function server()
{
    if(!is_null(\Phacil\HTTP\Server::getInstance())){
        return \Phacil\HTTP\Server::getInstance();
    }
    return new \Phacil\HTTP\Server();
}

/**
 * 
 * @return \Phacil\HTTP\Request
 */
function request()
{
    if(!is_null(\Phacil\HTTP\Request::getInstance())){
        return \Phacil\HTTP\Request::getInstance();
    }
    return new \Phacil\HTTP\Request();
}

/**
 * 
 * @return \Phacil\HTTP\Session
 */
function session()
{
    if(!is_null(\Phacil\HTTP\Session::getInstance())){
        return \Phacil\HTTP\Session::getInstance();
    }
    return new \Phacil\HTTP\Session();
}

/**
 * 
 * @return \Phacil\HTTP\Response
 */
function response()
{
    if(!is_null(\Phacil\HTTP\Response::getInstance())){
        return \Phacil\HTTP\Response::getInstance();
    }
    return new \Phacil\HTTP\Response();
}

/**
 * 
 * @return \Phacil\HTTP\Cookie
 */
function cookie()
{
    if(!is_null(\Phacil\HTTP\Cookie::getInstance())){
        return \Phacil\HTTP\Cookie::getInstance();
    }
    return new \Phacil\HTTP\Cookie();
}