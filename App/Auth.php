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
     * Checks if the user email password is correct or not
     * 
     * @param string email - email of requested user
     * @param string password - password of reqeusted user
     * @return mixed user object if authenticated, false otherwise
     */
    public static function authenticate($email, $password)
    {
        $user = User::getUserObjectFromEmail($email);

        if($user){
            $user_password = $user->password;

            if(password_verify($password, $user_password)){
                return $user;
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

        if($user->isAdmin()){
            $_SESSION['is_admin'] = "IS_ADMIN";
        }
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

    /**
     * Check if user is logged in
     * 
     * @return boolean true if authenticated, false otherwise
     */
    public static function isAuthenticated()
    {
        return isset($_SESSION['logged_user_id']);
    }

    /**
     * Check if user is logged in is admin
     * 
     * @return boolean true if authenticated, false otherwise
     */
    public static function isAdminAuthenticated()
    {
        return isset($_SESSION['is_admin']);
    }
    
}