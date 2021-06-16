<?php

namespace App\Controllers;

use App\Auth;
use \Core\View;
use App\Models;
use App\Models\Validation\UserValidation;
use App\Extra;
use App\Email;

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
        $user = [];
        $errors = [];
        if(!empty($_POST)){
            $user = new Models\User($_POST);
            $validation = new UserValidation($_POST);
            $errors = $validation->getErrors($_POST);

            if(empty($errors)){
                $user_role = Models\User::ROLE_CUSTOMER;

                if($user->save($user_role)){
                    $_SESSION['verify_email'] = $user->email;

                    $user = Models\User::getUserObjectFromEmail($user->email);

                    Email::sendVerifyCode($user);

                    $this->redirect('/user/verify-email/');
                }
                return;
            }
        }
        View::renderTemplate('User/signup.html', [
            'valid_data' => $user,
            'errors' => $errors
        ]);
    }


    /**
     * Show the login page
     *
     * @return void
     */
    public function loginAction()
    {
        $messagesArray = Extra::getMessageCookie();
        $message = $messagesArray['message'];
        $messageType = $messagesArray['type'];

        if(!empty($_POST)){
            $email = $_POST['email'];
            $password = $_POST['password'];

            $logged_user = Auth::authenticate($email, $password);

            if($logged_user){
                if($logged_user->canLogin()){
                    Extra::setMessageCookie("Logged in successfully.");

                    Auth::login($logged_user);

                    $this->redirect('/');
                }
                $this->redirect('/login/');
            }

            Extra::setMessageCookie("Incorrect email or password.", Extra::COOKIE_MESSAGE_FAIL);

            $this->redirect("/login/");
        }

        Extra::deleteMessageCookie();

        View::renderTemplate('User/login.html', [
            'message'=> $message,
            'messageType' => $messageType
        ]);
    }

    /**
     * Show the signup for trader page
     *
     * @return void
     */
    public function signupTraderAction()
    {
        if(!empty($_POST)){
            var_dump($_POST);
            var_dump($_FILES);
            exit();
        }
        View::renderTemplate('User/signup-trader.html');
    }

    /**
     * Show notice page to ask for email verification
     * 
     * @return void
     */
    public function verifyNoticeAction()
    {
        $email = isset($_SESSION['verify_email']) ? $_SESSION['verify_email'] : "";
        $errors = [];

        if(!$email){
            $this->show404();
        }

        $user = Models\User::getUserObjectFromEmail($email);

        if(!empty($_POST)){
            $code = isset($_POST['code']) ? $_POST['code'] : "";
            $resend = isset($_POST['resend']) ? "OK" : "";

            if($code){
                if($user->isEmailVerified()){
                    $this->redirect("/");
                }

                $verified = $user->verifyEmail($code);

                if($verified){
                    unset($_SESSION['verify_email']);

                    Extra::setMessageCookie("Your email is verified successfully. You can now login!!!");
                    
                    $this->redirect("/login/");
                }
                $errors['message'] = "Your code is invalid or may have expired.";
            }
            else if($resend){
                Email::sendVerifyCode($user);
                $this->redirect('/user/verify-email/');
            }
        }
        
        View::renderTemplate('User/verify-notice.html', [
            'errors' => $errors
        ]);
    }
}
