<?php

namespace App\Controllers;

use \Core\View;
use App\Models;
use App\Models\UserValidation;

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
        if(!empty($_POST)){
            $user = new Models\User($_POST);
            $validation = new Models\UserValidation($_POST);
            $errors = $validation->getErrors();

            if(empty($errors)){
                if($user->save('CUSTOMER')){
                    // login user
                }

                return;
            }

            View::renderTemplate('User/signup.html', [
                'valid_data' => $user,
                'errors' => $errors
            ]);

            return;
        }
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
