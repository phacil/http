<?php

namespace Phacil\HTTP;

class Session{
	
    public static function start($name='a', $limit = 0, $path = '/', $domain = null, $secure = null){
        
        ini_set('session.cookie_httponly', 1);
        
        if(!$name){
            $name = md5(Server::get('REMOTE_ADRESS') . Server::get('HTTP_USER_AGENT'));
        }else{
            $name = md5($name);
        }
        
        session_name($name . '_Session');

        // Set SSL level
        $https = isset($secure) ? $secure : isset($_SERVER['HTTPS']);

        // Set session cookie options
        session_set_cookie_params($limit, $path, $domain, $https, true);
        session_start();

        // Make sure the session hasn't expired, and destroy it if it has
        if(self::validateSession())
        {
                // Check to see if the session is new or a hijacking attempt
                if(!self::preventHijacking())
                {
                        // Reset session data and regenerate id
                        self::clean();
                        self::set('_config.IPaddress', Server::get('REMOTE_ADDR'));
                        self::set('_config.userAgent', Server::get('HTTP_USER_AGENT'));
                        
                        self::regenerateSession();

                // Give a 5% chance of the session id changing on any request
                }elseif(rand(1, 100) <= 5){
                        self::regenerateSession();
                }
        }else{
                $_SESSION = array();
                session_destroy();
                session_start();
        }
    }
    
    private static function clean(){
        $_SESSION = [];
    }

    protected static function preventHijacking(){
            if(!(self::get('_config.IPaddress')) || !(self::get('_config.userAgent'))){
                    return false;
            }
            
            if (self::get('_config.IPaddress') != Server::get('REMOTE_ADDR'))
                    return false;

            if( self::get('_config.userAgent') != Server::get('HTTP_USER_AGENT'))
                    return false;

            return true;

            /*if(!self::preventHijacking())
            {
                    $_SESSION = array();
                    self::get('_config.IPaddress') = Server::get('REMOTE_ADDR');
                    self::get('_config.userAgent') = Server::get('HTTP_USER_AGENT');
            }*/
    }

    protected static function regenerateSession(){

            // If this session is obsolete it means there already is a new id
//            if(isset($_SESSION['OBSOLETE']) || $_SESSION['OBSOLETE'] == true)
//                    return;

            // Set current session to expire in 10 seconds
            $_SESSION['OBSOLETE'] = true;
            $_SESSION['EXPIRES'] = time() + 10;

            // Create new session without destroying the old one
            session_regenerate_id(false);

            // Grab current session ID and close both sessions to allow other scripts to use them
            $newSession = session_id();
            session_write_close();

            // Set session ID to the new one, and start it back up again
            session_id($newSession);
            session_start();

            // Now we unset the obsolete and expiration values for the session we want to keep
            unset($_SESSION['OBSOLETE']);
            unset($_SESSION['EXPIRES']);
    }

    protected static function validateSession()
    {
        if( isset($_SESSION['OBSOLETE']) && !isset($_SESSION['EXPIRES']) )
                return false;

        if(isset($_SESSION['EXPIRES']) && $_SESSION['EXPIRES'] < time())
                return false;

        return true;
    }
	
    public static function get($name)
    {
        $parsed = explode('.', $name);
        $result = $_SESSION;
        while ($parsed) {
            $next = array_shift($parsed);
            if (isset($result[$next])) {
                $result = $result[$next];
            } else {
                return null;
            }
        }
        return $result;
    }
	
    public static function set($name, $value)
    {
        $parsed = explode('.', $name);
        $session =& $_SESSION;
        while (count($parsed) > 1) {
            $next = array_shift($parsed);
            if ( ! isset($session[$next]) || ! is_array($session[$next])) {
                $session[$next] = [];
            }
            $session =& $session[$next];
        }
        $session[array_shift($parsed)] = $value;
    }
    
    public static function check($name){
        return is_null(self::get($name))?false:true;
    }
    
    public static function delete($name) {
        $parsed = explode('.', $name);
        $session =& $_SESSION;
        while (count($parsed) > 1) {
            $next = array_shift($parsed);
            if ( ! isset($session[$next]) || ! is_array($session[$next])) {
                $session[$next] = [];
            }
            $session =& $session[$next];
        }
        unset($session[array_shift($parsed)]);
    }
    
    public static function setMessage($msg, $id = '', $class = 'alert', $div = 'div'){
        $divHtml = "<$div id='$id' class='$class'>$msg</$div>";
        self::set('message'.$id, $divHtml);
    }
    
    public static function showMessage($id = ''){
        $msg = self::get('message'.$id);
        self::delete('message'.$id);
        return $msg;
    }
	
}
