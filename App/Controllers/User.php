<?php

namespace App\Controllers;

use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class User extends \Core\Controller
{

    /**
     * Show the signup page
     *
     * @return void
     */
    public function signupAction()
    {
        View::renderTemplate('User/signup.html');
    }


    /**
     * Show the login page
     *
     * @return void
     */
    public function loginAction()
    {
        View::renderTemplate('User/login.html');
    }

    /**
     * Show the signup for trader page
     *
     * @return void
     */
    public function signupTraderAction()
    {
        View::renderTemplate('User/signup-trader.html');
    }
}
