<?php
namespace Rocker\GoogleLogin;


use Rocker\Cache\Cache;

/**
 * @package Rocker\FacebookLogin
 * @author Victor Jonsson (http://victorjonsson.se)
 * @license MIT license (http://opensource.org/licenses/MIT)
 */
class Utils {

    /**
     * @param $email
     * @param $password
     * @return bool
     */
    public static function gmailAuthenticate($email, $password)
    {
        $cache = Cache::instance();
        $cacheKey = hash('md4', $email.$password);
        if( $auth = $cache->fetch('google-login-'.$cacheKey) ) {
            return $auth['status'];
        } else {

            $auth = array();
            try {
                self::doAuthenticate($email, $password);
                $auth['status'] = true;
            } catch(LoginException $e) {
                $auth['status'] = false;
            }

            $cache->store('google-login-'.$cacheKey, $auth, 36000);
            return $auth['status'];
        }
    }

    /**
     * @param $email
     * @param $password
     * @throws LoginException
     * @return void
     */
    private static function doAuthenticate($email, $password)
    {
        // No empty args
        if(empty($email) || empty($password))
            throw new LoginException('Neither e-mail nor password can be empty');

        // check if we have curl or not
        if( !function_exists('curl_init') )
            throw new LoginException('curl needs to be installed in order to use this function');

        $curl = curl_init('https://mail.google.com/mail/feed/atom');

        $safe_mode = ini_get('safe_mode');
        $open_basedir = ini_get('open_basedir');
        if( (!$safe_mode || strtolower($safe_mode) == 'off') &&
            (!$open_basedir || strtolower($open_basedir) == 'off') ) {
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERPWD, $email.':'.$password);
        curl_exec($curl);

        $error = curl_error($curl);
        if( $error !== '' ) {
            $err_num = curl_errno($curl);
            curl_close($curl);
            throw new LoginException($error, $err_num);
        }
        else {
            // failed authentication
            $status_code = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if($status_code !== 200) {
                throw new LoginException('Google responded with status '.$status_code);
            }
        }
    }
}