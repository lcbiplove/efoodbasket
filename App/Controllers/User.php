<?php

namespace App\Controllers;

use App\Config;
use \Core\View;
use App\Models;
use App\Models\UserValidation;
use App\Extra;



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
                if($user->save('CUSTOMER')){
                    unset($_SESSION['verify_email']);
                    $_SESSION['verify_email'] = $user->email;

                    $user = Models\User::getUserObjectFromEmail($user->email);

                    $this->sendVerifyCode($user);

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
                $this->sendVerifyCode($user);
                $this->redirect('/user/verify-email/');
            }
        }
        
        View::renderTemplate('User/verify-notice.html', [
            'errors' => $errors
        ]);
    }

    /**
     * Sends email with verification code
     * 
     * @param object to be sent user
     * @return void
     */
    public function sendVerifyCode($user)
    {
        if($user->isEmailVerified()){
            $this->redirect('/');
        }

        $email = $user->email;

        $websiteUrl = Config::WEBSITE_NAME;
        $logoUrl = "https://raw.githubusercontent.com/lcbiplove/efood-ecommerce/main/images/efoodbasket-logo.png?token=AJXACGOHR6VDJGV5Y6ISEETA2GBIA";
        
        $code = $user->getValidOtp();

        $user->updateOtpCode($code);

        $subject = "Your eFoodBasket verification code";
        $altBody = "
        ---------------------------
        |        efoodBasket      |     
        ---------------------------

        Hi, 

        We're excited to have you get started. First, you need to confirm your account.
        Below is the code to verify your account:


                            $code            


        Thanks 
        eFoodBasket

        -----------------------------------------------------------
        eFoodBasket - 98 Neville Street - Cleckhuderfax, UK - 56789
        ";

        

        $body = "
        <!DOCTYPE html><html><head><title></title><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><meta name='viewport' content='width=device-width, initial-scale=1'><meta http-equiv='X-UA-Compatible' content='IE=edge' /><style type='text/css'> /* CLIENT-SPECIFIC STYLES */ body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; } img { -ms-interpolation-mode: bicubic; } /* RESET STYLES */ img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; } table { border-collapse: collapse !important; } body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; } /* iOS BLUE LINKS */ a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; } /* MOBILE STYLES */ @media screen and (max-width:600px){ h1 { font-size: 32px !important; line-height: 32px !important; } } /* ANDROID CENTER FIX */ div[style*='margin: 16px 0;'] { margin: 0 !important; }</style><style type='text/css'></style></head><body style='background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;'><div style='display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;'> Email Verification Code</div><table border='0' cellpadding='0' cellspacing='0' width='100%'> <tr> <td bgcolor='#f4f4f4' align='center'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;' > <tr> <td align='center' valign='top' style='padding: 40px 10px 10px 10px;'> <a href='$websiteUrl' target='_blank'> <img alt='Logo' src='$logoUrl' width='100' height='40' style='display: block; width: 100px; max-width: 100px; min-width: 100px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; font-size: 18px;' border='0'> </a> </td> </tr> </table> </td> </tr> <tr> <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;' > <tr> <td bgcolor='#ffffff' align='center' valign='top' style='padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;'> <h1 style='font-size: 28px; font-weight: 400; margin: 0; letter-spacing: 0px;'>Email Verification Code</h1> </td> </tr> </table> </td> </tr> <tr> <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;' > <tr> <td bgcolor='#ffffff' align='left' style='padding: 20px 30px 40px 30px; color: #666666; font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;' > <p style='margin: 0;'>We're excited to have you get started. First, you need to confirm your account. Below is the code to verify your account:</p> </td> </tr> <tr> <td bgcolor='#ffffff' align='left'> <table width='100%' border='0' cellspacing='0' cellpadding='0'> <tr> <td bgcolor='#ffffff' align='center' style='padding: 20px 30px 60px 30px;'> <table border='0' cellspacing='0' cellpadding='0'> <tr> <td align='center' style='border-radius: 3px;' bgcolor='#7FCB0A'><span style='letter-spacing: 5px; font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #7FCB0A; display: inline-block;'>$code</a></td> </tr> </table> </td> </tr> </table> </td> </tr> <tr> <td bgcolor='#ffffff' align='left' style='padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #666666; font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;' > <p style='margin: 0;'>Thanks,<br>eFoodBasket </p> </td> </tr> </table> </td> </tr> <tr> <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;' > <tr> <td bgcolor='#f4f4f4' align='left' style='padding: 30px 30px 30px 30px; color: #666666; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;' > <p style='margin: 0;'> <a href='$websiteUrl' target='_blank' style='color: #111111; font-weight: 700;'>Home</a> - <a href='$websiteUrl/signup/' target='_blank' style='color: #111111; font-weight: 700;'>Login</a> - <a href='$websiteUrl/login/' target='_blank' style='color: #111111; font-weight: 700;'>Signup</a> </p> </td> </tr> <tr> <td bgcolor='#f4f4f4' align='left' style='padding: 0px 30px 30px 30px; color: #666666; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;' > <p style='margin: 0;'>eFoodBasket - 98 Neville Street - Cleckhuderfax, UK - 56789</p> </td> </tr> </table> </td> </tr></table></body></html>
        ";

        Extra::sendMail($email, $subject, $body, $altBody);
    }
}
