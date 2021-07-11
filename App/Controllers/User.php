<?php

namespace App\Controllers;

use App\Auth;
use \Core\View;
use App\Models;
use App\Models\Validation\UserValidation;
use App\Models\Validation\TraderValidation;
use App\Extra;
use App\Email;
use App\Models\Cart;
use App\Models\Notification;
use App\Models\ProductCart;
use App\Models\Trader;

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
        $this->restrictForAuthenticated();
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
        $this->requireLogin();
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
        $this->restrictForAuthenticated();
        $nextRoute = isset($_GET['next']) ? "?next=" . $_GET['next'] : "";
        $checkLoadCart = $nextRoute == "?next=/cart/" ? true : false;

        if(!empty($_POST)){
            $email = $_POST['email'];
            $password = $_POST['password'];

            $logged_user = Auth::authenticate($email, $password);

            if($checkLoadCart) {
                $response = [];

                if($logged_user){
                    if($logged_user->canLogin()){   

                        Auth::login($logged_user);

                        if($logged_user->isTrader()) {
                            $response['error'] = true;
                        } 
                        else {
                            $cartsArray = json_decode($_POST['cart']);
                            $cartItemsCount = $_POST['cartItemsCount'];
                            $cartObj = Cart::getCartObject($logged_user->user_id);

                            $totalCartItemsCount = $cartObj->cartItemsCount() + $cartItemsCount;

                            Extra::setMessageCookie("Logged in successfully.");

                            if($totalCartItemsCount > Cart::MAX_CART_ITEMS_COUNT) {
                                Extra::setMessageCookie("Cart items that you added before logging in cannot be loaded because cart items exceeded the maximum limit of 20.", Extra::COOKIE_MESSAGE_INFO);
                            } 
                            else {
                                foreach ($cartsArray as $value) {
                                    $productCart = new ProductCart([
                                        'quantity' => $value->quantity,
                                        'product_id' => $value->product_id,
                                        'cart_id' => $cartObj->CART_ID
                                    ]);

                                    $updatableCartProdObj = $cartObj->isProductInCart($value->product_id);

                                    if($updatableCartProdObj) {
                                        $updatableCartProdObj->update($value->quantity, false);
                                    } else {
                                        $productCart->save();
                                    }
                                }
                                $response['clearLocal'] = true;
                            }
                            $response['success'] = true;
                        }
                    }
                    $response['error'] = "Verify your email.";
                } 
                else {
                    Extra::setMessageCookie("Incorrect email or password.", Extra::COOKIE_MESSAGE_FAIL);
                    $response['error'] = "Incorrect email or password.";
                }

                echo json_encode($response);
                exit();
            }

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
            'checkLoadCart' => $checkLoadCart
        ]);
    }

    /**
     * Show the signup for trader page
     *
     * @return void
     */
    public function signupTraderAction()
    {
        $this->restrictForAuthenticated();
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
     * Show the forgot password page
     *
     * @return void
     */
    public function forgotPassword()
    {
        $this->restrictForAuthenticated();
        $errors = [];
        $email = "";

        if(!empty($_POST)){
            $email = $_POST['email'];

            $user = Models\User::getUserObjectFromEmail($email);
            
            if($user){
                $token = $user->createUpdateToken();
                Email::sendPasswordReset($user, $token);
                Extra::setMessageCookie("Password reset link is sent to your email.", Extra::COOKIE_MESSAGE_INFO);
                $this->redirect("/login/");
            }
            else {
                $errors['email'] = "We could not find any email of that name. Please check your email again.";
            }
        }

        View::renderTemplate('User/forgot-password.html', [
            'errors' => $errors,
            'email' => $email
        ]);
    }

    /**
     * Password reset page
     * 
     * @return void
     */
    public function resetPassword()
    {
        $this->restrictForAuthenticated();
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
        $this->restrictForAuthenticated();
        

        $resendEmail = false;
        if(isset($_POST['email'])){
            $resendEmail = true;
            $_SESSION['verify_email'] = $_POST['email'];
        }
        $email = isset($_SESSION['verify_email']) ? $_SESSION['verify_email'] : "";
        

        $errors = [];

        if(!$email){
            $this->show404();
        }

        $user = Models\User::getUserObjectFromEmail($email);

        if($resendEmail){
            Email::sendVerifyCode($user);
            $this->redirect('/user/verify-email/');
        }

        if(!empty($_POST)){
            $code = isset($_POST['code']) ? $_POST['code'] : "";

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
                    } else {
                        $cart = new Cart(['user_id' => $user->user_id]);
                        $cart->create();

                        $notification = new Notification([
                            'title' => "Welcome to efoodbasket",
                            'body' => "Hi, we are happy to see you in our platform. We hope you have a great experience with us. We will be looking for your feedback.",
                            'image_link' => "/public/images/efoodbasket-logo.png",
                            'sender_text' => "From efoodbasket",
                            'main_link' => "#",
                            'user_id' => $user->user_id,
                            'is_seen' => Models\Notification::IS_NOT_SEEN
                        ]);
                        $notification->save();
                    }
                    
                    $this->redirect("/login/");
                }
                $errors['message'] = "Your code is invalid or may have expired.";
            }
        }
        
        View::renderTemplate('User/verify-notice.html', [
            'errors' => $errors
        ]);
    }
}
