<?php

namespace App;

use \App\Models\User;
use \App\Models\Title;
/**
 * Extra class
 * For the extra functions
 * 
 * PHP 7.3
 */
class Extra
{
    /**
     * Get the router params
     * 
     * @param $path path_id e.g- user_name
     * @return mixed string if true, false otherwise
     */
    public static function routerPath($path = 'user_name') {

        global $router;

        if (array_key_exists($path, $router->getParams())) {

            return $router->getParams()[$path];
        }
        return false;        
    }

    /**
     * Get user object by path
     * 
     * @return object
     */
    public static function getPathUser($path = 'user_name')
    {
        $username = static::routerPath($path);

        return User::findByUsername($username);
    }

    /**
     * Get user id by path
     * 
     * @return object
     */
    public static function getPathTitle($path = 'title_id')
    {
        $title_id = static::routerPath($path);

        return Title::findByTitleId($title_id);
    }

    /**
     * Show private
     * 
     * @return boolean
     */
    public static function canShowPrivate()
    {
        if (Auth::getUser()) {
            if (static::getPathUser()->username == Auth::getUser()->username) {
                return true;
            }
        }
        return false;
    }
}