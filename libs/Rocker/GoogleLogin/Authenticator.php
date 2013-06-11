<?php
namespace Rocker\GoogleLogin;

use Rocker\Server;
use Rocker\REST\Authenticator as RockerAuthenticator;


/**
 * Authentication class for PHP-Rocker that can authorize clients using
 * Google user credentials. This authentication method will be executed
 * when requesting the API with a authorization header
 * like "Authorization: google [google-email]:[google-password]"
 *
 *  Authorization: google shawn1980@gmail.com:shawns-google-password
 *
 * Change the authentication class in config.php to \\Rocker\\GoogleLogin\\Authenticator
 *
 * @package Rocker\GoogleLogin
 * @author Victor Jonsson (http://victorjonsson.se)
 * @license MIT license (http://opensource.org/licenses/MIT)
 */
class Authenticator extends RockerAuthenticator {

    /**
     * @param $data
     * @param \Rocker\Server $server
     * @return \Rocker\Object\User\UserInterface|null
     */
    public function googleAuth($data, Server $server)
    {
        error_log('In here');
        $config = $server->config('google.login');
        list($email, $pass) = explode(':', $data);

        // restricted domain
        if( !$this->isAllowedEmail($email, $config) ) {
            return null;
        }

        $user = null;
        if( Utils::gmailAuthenticate($email, $pass) ) {

            // Load user
            $user = $this->userFactory->load($email);

            // Create user if not existing
            if( !$user ) {
                $user = $this->userFactory->createUser(
                    $email,
                    $this->extractUserNameFromEmail($email),
                    $this->makeSuperHardPassword()
                );
            }
        }

        return $user;
    }

    /**
     * @param string $email
     * @param array $config
     * @return bool
     */
    private function isAllowedEmail($email, $config)
    {
        if( !empty($config) && !empty($config['allowed_domains'])) {
            $domain = current( array_slice(explode('@', $email), 1,1) );
            return in_array($domain, explode(',', $config['allowed_domains']));
        }
        return true;
    }

    /**
     * @param string $email
     * @return string
     */
    private function extractUserNameFromEmail($email)
    {
        $name = ucfirst(current(explode('@', $email)));
        return ucwords(str_replace(array('.','-'), ' ', $name));
    }

    /**
     * @return string
     */
    private function makeSuperHardPassword()
    {
        $str = 'qwertyuiopasdfghjklzxcvbnm.,0987654321!#%&';
        $len = strlen($str);
        $pass = '';
        for($i=0;$i<26;$i++) {
            $pass .= substr($str, mt_rand(0,$len), 1);
        }
        return $pass;
    }

    /**
     * @inheritdoc
     */
    public function rc4Auth($data, $server)
    {
        $conf = $server->config('google.login');
        if( !$conf || empty($conf['disabled_auth_mechanisms']) || !in_array('rc4', explode(',', $conf['disabled_auth_mechanisms']))) {
            return parent::rc4Auth($data, $server);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function basicAuth($data, $server)
    {
        $conf = $server->config('google.login');
        if( !$conf || empty($conf['disabled_auth_mechanisms']) || !in_array('basic', explode(',', $conf['disabled_auth_mechanisms']))) {
            return parent::basicAuth($data, $server);
        }
        return null;
    }
}