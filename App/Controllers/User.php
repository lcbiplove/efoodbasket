<?php

namespace App\Controllers;

use App\Auth;
use \Core\View;
use App\Models;
use App\Models\Validation\UserValidation;
use App\Models\Validation\TraderValidation;
use App\Extra;
use App\Email;
use App\Models\Trader;
use Core\Model;

/**
 * Home controller
 *
 * PHP version 7.3
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
            $errors = $validation->getErrors();

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
     * Logs user out 
     * 
     * @return void
     */
    public function logoutAction()
    {
        if(!empty($_POST)){
            $logout = isset($_POST['logout']) ? true : false;

            if($logout){
                Auth::logout();
                Extra::setMessageCookie("Logged out successfully!!!");
                $this->redirect('/login/');
            }
        }
    }

    /**
     * Show the login page
     *
     * @return void
     */
    public function loginAction()
    {
        $nextRoute = isset($_GET['next']) ? "?next=" . $_GET['next'] : "";

        if(!empty($_POST)){
            $email = $_POST['email'];
            $password = $_POST['password'];

            $logged_user = Auth::authenticate($email, $password);

            if($logged_user){
                if($logged_user->canLogin()){
                    Extra::setMessageCookie("Logged in successfully.");

                    Auth::login($logged_user);

                    $next = $nextRoute ? $_GET['next'] : "";

                    $this->redirect($next);
                }
                $this->redirect('/login/'.$nextRoute);
            }

            Extra::setMessageCookie("Incorrect email or password.", Extra::COOKIE_MESSAGE_FAIL);

            $this->redirect("/login/".$nextRoute);
        }

        View::renderTemplate('User/login.html', [
            'nextRoute' => $nextRoute,
        ]);
    }

    /**
     * Show the signup for trader page
     *
     * @return void
     */
    public function signupTraderAction()
    {
        $user = [];
        $errors = [];

        if(!empty($_POST)){
            $user = new Trader($_POST);
            $validation = new TraderValidation($_POST);
            $errors = $validation->getErrors($_FILES);

            if(empty($errors)){
                $user->save($_FILES);

                $_SESSION['verify_email'] = $user->email;

                $user = Models\User::getUserObjectFromEmail($user->email);

                Email::sendVerifyCode($user);

                $this->redirect('/user/verify-email/');
            }
        }
        View::renderTemplate('User/signup-trader.html', [
            'valid_data' => $user,
            'errors' => $errors
        ]);
    }

    /**
     * Password reset page
     * 
     * @return void
     */
    public function resetPassword()
    {
        $id = $this->route_params['id'];
        $token = $this->route_params['token'];

        if(!empty($_POST)){
            $user = Models\User::getUserObjectFromId($id);

            if($user){
                $validation = new UserValidation($_POST, ['password']);
                $errors = $validation->getNamedErrors();

                $route = $_SERVER['REQUEST_URI'];


                if(!$user->isTokenValid($token)){
                    Extra::setMessageCookie("The link you entered is not valid or is expired!!!", EXTRA::COOKIE_MESSAGE_FAIL);
                    $this->redirect($route);
                }

                if(empty($errors)){
                    $password = $_POST['password'];

                    $user->changePassword($password);

                    Extra::setMessageCookie("Password updated successfully!!!");

                    $this->redirect("/login/");
                }
                View::renderTemplate('User/password-reset.html', [
                    'errors'=>$errors
                ]);
            }
            $this->show404();
        }
        View::renderTemplate('User/password-reset.html');
    }

    /**
     * Show notice page to ask for email verification
     * 
     * @return void
     */
    public function verifyNoticeAction()
    {
        $_SESSION['verify_email'] = "hostingbips@gmail.com";
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

                    if($user->isTrader()){
                        // TODO: add to notification
                        Email::sendTraderRequestToAdmin($user);
                        
                        Extra::setMessageCookie("Your email is verified successfully. You will be notified once your documents are reviewed.", Extra::COOKIE_MESSAGE_INFO);
                    }
                    
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
