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
                
        $_SESSION['id'] = $user->id;
    }

    /**
     * Get user by session
     * 
     * @return object $user
     */
    public static function getUser()
    {
        if (isset($_SESSION['id'])) {

            $user_id = $_SESSION['id'];

            return User::findByUserId($user_id);
        }
        return false;
    }

    /**
     * Twig to check session
     * 
     * @return string
     */
    public static function twigSession()
    {
        if (isset($_SESSION['id'])) {

            return "logged";
        }
        return "not-logged";
    }

    /**
     * Logout the user
     *
     * @return void
     */
    public static function logout()
    {
        // Unset all of the session variables
        $_SESSION = [];

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