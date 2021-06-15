<?php

namespace App;

use \App\Models\User;
/**
 * Class Auth
 * 
 * PHP 7.3
 */
class Auth
{

    public static function authenticate($email, $password)
    {
        $user = User::getUserObjectFromEmail($email);

        if($user){
            $user_password = $user->password;

            if(password_verify($password, $user_password)){
                Auth::login($user);
                return true;
            }
        }
        return false;
    }

    /**
     * Login controller
     * Set session after login
     * 
     * @param object $user 
     * @return void
     */
    public static function login($user)
    {
        session_regenerate_id();
                
        $_SESSION['logged_user_id'] = $user->user_id;
    }

    /**
     * Get user from session
     * 
     * @return object $user
     */
    public static function getUser()
    {
        if (isset($_SESSION['logged_user_id'])) {

            $logged_user_id = $_SESSION['logged_user_id'];

            return User::getUserObjectFromId($logged_user_id);
        }
        return false;
    }


    /**
     * Logout the user
     *
     * @return void
     */
    public static function logout()
    {
        // Unset all of the session variables
        unset($_SESSION);

        // Delete the session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Finally destroy the session
        session_destroy();
    }
}