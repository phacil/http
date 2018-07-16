<?php

namespace Phacil\HTTP;
use Phacil\Common\AbstractClass\AbstractCollection;

class Session extends AbstractCollection{
    
    use \Phacil\Common\Traits\InstanceTrait;
    
    protected $type = self::TYPE_MIXED;
    
    public function __construct() {
        self::$instance = $this;
        parent::__construct($_SESSION);
        return $this;
    }
	
    public static function start($name='a', $limit = 0, $path = '/', $domain = null, $secure = null)
    {
        
        ini_set('session.cookie_httponly', 1);
        
        if(!$name){
            $name = md5(server()->get('REMOTE_ADRESS') . server()->get('HTTP_USER_AGENT'));
        }else{
            $name = md5($name);
        }
        
        session_name($name . '_Session');

        // Set SSL level
        $https = isset($secure) ? $secure : is_null(server()->check('HTTPS'));

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
                    session()->clean();
                    session()->set('_config.IPaddress', server()->get('REMOTE_ADDR'));
                    session()->set('_config.userAgent', server()->get('HTTP_USER_AGENT'));

                    self::regenerateSession();

            // Give a 5% chance of the session id changing on any request
            }elseif(rand(1, 100) <= 5){
                    self::regenerateSession();
            }
        }else{
            $this->clean();
        }
    }
    
    protected function clean()
    {
        $_SESSION = [];
        session_destroy();
        session_start();
        parent::clean();
    }

    private static function preventHijacking()
    {
        if(!(session()->check('_config.IPaddress')) || !(session()->check('_config.userAgent'))){
            return false;
        }

        if (session()->check('_config.IPaddress') != server()->check('REMOTE_ADDR')){
            return false;
        }

        if( session()->check('_config.userAgent') != server()->check('HTTP_USER_AGENT')){
            return false;
        }
            
        return true;

        /*if(!self::preventHijacking())
            {
                    $_SESSION = array();
                    session()->get('_config.IPaddress') = server()->get('REMOTE_ADDR');
                    session()->get('_config.userAgent') = server()->get('HTTP_USER_AGENT');
            }*/
    }

    private static function regenerateSession()
    {
        // If this session is obsolete it means there already is a new id
//            if(session()->check['OBSOLETE']) || $_SESSION['OBSOLETE'] == true)
//                    return;

        // Set current session to expire in 10 seconds
        session()->set('OBSOLETE' ,true);
        session()->set('EXPIRES', time() + 10);

        // Create new session without destroying the old one
        session_regenerate_id(false);

        // Grab current session ID and close both sessions to allow other scripts to use them
        $newSession = session_id();
        session_write_close();

        // Set session ID to the new one, and start it back up again
        session_id($newSession);
        session_start();

        // Now we unset the obsolete and expiration values for the session we want to keep
        session()->delete('OBSOLETE');
        session()->set('EXPIRES');
    }

    private function validateSession()
    {
        if( session()->check('OBSOLETE') && !session()->check('EXPIRES') )
                return false;

        if(session()->check('EXPIRES') && session()->get('EXPIRES') < time())
                return false;

        return true;
    }
	
    public function get($key)
    {
        return parent::get($key);
    }
	
    public function set($key, $value = null)
    {
        parent::set($key, $value);
        $_SESSION = $this->elements;
        return $this;
    }
    
    public function check($key)
    {
        return parent::check($key);
    }
    
    public function delete($key)
    {        
        parent::delete($key);
        $_SESSION = $this->elements;
    }
    	
}
