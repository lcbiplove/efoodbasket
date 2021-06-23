<?php

namespace App;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\SMTP;

use App\Config;
use App\Models\User;

/**
 * Email class
 * For the email functions
 * 
 * PHP 7.3
 */
class Email
{
    /**
     * Sends the email to the clients 
     * 
     * @param array toEmails - email of recipients array
     * @param string subject - subject of email
     * @param string body - html string of main body
     * @param string altBody - body without html
     * 
     * @return boolean true if sent, false otherwise
     */
    private static function sendMail($toEmails, $subject, $body, $altBody)
    {
        $mail = new PHPMailer(true);

        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      
        $mail->isSMTP();                                            
        $mail->Host       = Config::SMTP_HOST;                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = Config::MAIL_USERNAME;                     
        $mail->Password   = Config::MAIL_PASSWORD;                               
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         
        $mail->Port       = 587;      


        $mail->setFrom('efoodbasket@gmail.com', 'efoodBasket');
        foreach ($toEmails as $email) {
            $mail->addAddress($email);     
        }
        $mail->addReplyTo('efoodbasket@noreply.com', 'No Reply');


        $mail->isHTML(true);                                  
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody;

        return $mail->send();
    }

    public static function getEmailBody($title, $linkOrCodeHTML, $body_text)
    {
        $websiteUrl = Config::WEBSITE_NAME;
        $logoUrl = "https://raw.githubusercontent.com/lcbiplove/e-food-basket/main/images/efoodbasket-name.png?token=AJXACGND7OJ5PN5A2RY7KUTA3LZBE";

        $body = "
        <!DOCTYPE html><html><head><title></title><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><meta name='viewport' content='width=device-width, initial-scale=1'><meta http-equiv='X-UA-Compatible' content='IE=edge' /><style type='text/css'> /* CLIENT-SPECIFIC STYLES */ body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; } img { -ms-interpolation-mode: bicubic; } /* RESET STYLES */ img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; } table { border-collapse: collapse !important; } body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; } /* iOS BLUE LINKS */ a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; } /* MOBILE STYLES */ @media screen and (max-width:600px){ h1 { font-size: 32px !important; line-height: 32px !important; } } /* ANDROID CENTER FIX */ div[style*='margin: 16px 0;'] { margin: 0 !important; }</style><style type='text/css'></style></head><body style='background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;'><div style='display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;'> $title</div><table border='0' cellpadding='0' cellspacing='0' width='100%'> <tr> <td bgcolor='#f4f4f4' align='center'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;' > <tr> <td align='center' valign='top' style='padding: 40px 10px 10px 10px;'> <a href='$websiteUrl' target='_blank'> <img alt='Logo' src='$logoUrl' style='display: block; width: 200px; margin: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; font-size: 18px;' border='0'> </a> </td> </tr> </table> </td> </tr> <tr> <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;' > <tr> <td bgcolor='#ffffff' align='center' valign='top' style='padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;'> <h1 style='font-size: 28px; font-weight: 400; margin: 0; letter-spacing: 0px;'>$title</h1> </td> </tr> </table> </td> </tr> <tr> <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;' > <tr> <td bgcolor='#ffffff' align='left' style='padding: 20px 30px 40px 30px; color: #666666; font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;' > <p style='margin: 0;'>$body_text</p> </td> </tr> <tr> <td bgcolor='#ffffff' align='left'> <table width='100%' border='0' cellspacing='0' cellpadding='0'> <tr> <td bgcolor='#ffffff' align='center' style='padding: 20px 30px 60px 30px;'> <table border='0' cellspacing='0' cellpadding='0'> <tr> <td align='center' style='border-radius: 3px;' bgcolor='#7FCB0A'>$linkOrCodeHTML</td> </tr> </table> </td> </tr> </table> </td> </tr> <tr> <td bgcolor='#ffffff' align='left' style='padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #666666; font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;' > <p style='margin: 0;'>Thanks,<br>eFoodBasket </p> </td> </tr> </table> </td> </tr> <tr> <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;' > <tr> <td bgcolor='#f4f4f4' align='left' style='padding: 30px 30px 30px 30px; color: #666666; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;' > <p style='margin: 0;'> <a href='$websiteUrl' target='_blank' style='color: #111111; font-weight: 700;'>Home</a> - <a href='$websiteUrl/signup/' target='_blank' style='color: #111111; font-weight: 700;'>Login</a> - <a href='$websiteUrl/login/' target='_blank' style='color: #111111; font-weight: 700;'>Signup</a> </p> </td> </tr> <tr> <td bgcolor='#f4f4f4' align='left' style='padding: 0px 30px 30px 30px; color: #666666; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;' > <p style='margin: 0;'>eFoodBasket - 98 Neville Street - Cleckhuderfax, UK - 56789</p> </td> </tr> </table> </td> </tr></table></body></html>
        ";
        return $body;
    }

    public static function getAltBody($codeOrLink, $body_text)
    {

        $altBody = "
        ---------------------------
        |        efoodBasket      |     
        ---------------------------

        Hi, 

        $body_text


                            $codeOrLink           


        Thanks 
        eFoodBasket

        -----------------------------------------------------------
        eFoodBasket - 98 Neville Street - Cleckhuderfax, UK - 56789
        ";
        return $altBody;
    }

    /**
     * Sends email with verification code
     * 
     * @param object to be sent user
     * @return void
     */
    public static function sendVerifyCode($user) 
    {
        if($user->isEmailVerified()){
            return;
        }

        $email = $user->email;

        $websiteUrl = Config::WEBSITE_NAME;
        $logoUrl = "https://raw.githubusercontent.com/lcbiplove/e-food-basket/main/images/efoodbasket-name.png?token=AJXACGND7OJ5PN5A2RY7KUTA3LZBE";
        
        $code = $user->getValidOtp();

        $user->updateOtpCode($code);

        $title = "Email Verification Code";

        $codeHTML = "<span style='letter-spacing: 5px; font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #7FCB0A; display: inline-block;'>$code</span>";

        $body_text = "We're excited to have you get started. First, you need to confirm your account.
        Below is the code to verify your account:";

        $subject = "Your eFoodBasket verification code";
        
        $body = static::getEmailBody($title, $codeHTML, $body_text);

        $altBody = static::getAltBody($code, $body_text);

        Email::sendMail([$email], $subject, $body, $altBody);
    }


    /**
     * Sends password reset link to the user
     * 
     * @return void
     */
    public static function sendPasswordReset($user, $token)
    {
        $websiteUrl = Config::WEBSITE_NAME;
        
        $link = "$websiteUrl/user/reset-password/{$user->user_id}/$token/";

        $emails = [$user->email];

        $title = "Password Reset Link";

        $linkOrCodeHTML = "<a href='$link' target='_blank' style='font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #7FCB0A; display: inline-block;'>Reset Password</a>";

        $body_text = "We have recently received a request to reset forgotten password for your efoodbasket account. To change your efoodbasket account password, please click the link below:";

        $subject = "Password Reset";
        $body = static::getEmailBody($title, $linkOrCodeHTML, $body_text);
        $altBody = static::getAltBody($link, $body_text);

        Email::sendMail($emails, $subject, $body, $altBody);
    }

    /**
     * Send trader requests to all admins
     * 
     * @param object user - Trader who have requested
     * @return void
     */
    public static function sendTraderRequestToAdmin($user)
    {
        $websiteUrl = Config::WEBSITE_NAME;
        
        $link = "$websiteUrl/admin/trader-requests/$user->user_id/";

        $emails = User::getAllAdminEmails();

        $title = "Review Trader Form";

        $linkOrCodeHTML = "<a href='$link' target='_blank' style='letter-spacing: 5px; font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #7FCB0A; display: inline-block;'>Link</a>";

        $body_text = "A new trader is requesting to join our platform. They are waiting for your response
        to start working with us. Please review the user and make your decision:";

        $subject = "Review trader request";
        $body = static::getEmailBody($title, $linkOrCodeHTML, $body_text);
        $altBody = static::getAltBody($link, $body_text);

        Email::sendMail($emails, $subject, $body, $altBody);
    }

    /**
     * Send email to user to notify their form failure
     * 
     * @param object - user recipient
     */
    public static function sendTraderRejected($user)
    {
        $websiteUrl = Config::WEBSITE_NAME;
        
        $link = "$websiteUrl/signup-trader/";

        $emails = [$user->email];

        $title = "Request to become trader";

        $linkOrCodeHTML = "<a href='$link' target='_blank' style='letter-spacing: 5px; font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #7FCB0A; display: inline-block;'>Signup</a>";

        $body_text = "We are sorry that we could not accept your request of joining efoodbasket as trader. Please signup again with 
        appropriate and real documents to work with us.";

        $subject = "Request to become trader";
        $body = static::getEmailBody($title, $linkOrCodeHTML, $body_text);
        $altBody = static::getAltBody($link, $body_text);

        Email::sendMail($emails, $subject, $body, $altBody);
    }


    /**
     * Send email to user about accepted 
     * 
     * @return void
     */
    public static function sendTraderAccepted($user, $token)
    {
        $websiteUrl = Config::WEBSITE_NAME;
        
        $link = "$websiteUrl/user/reset-password/{$user->user_id}/$token/";

        $emails = [$user->email];

        $title = "Request to become trader";

        $linkOrCodeHTML = "<a href='$link' target='_blank' style='letter-spacing: 5px; font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #7FCB0A; display: inline-block;'>Set Password</a>";

        $body_text = "Welcome to efoodbasket for traders!!! We are so happy to inform you that you can now use our trader features. You can 
        now login to efoodbasket as trader once you have set your password:";

        $subject = "Request to become trader";
        $body = static::getEmailBody($title, $linkOrCodeHTML, $body_text);
        $altBody = static::getAltBody($link, $body_text);

        Email::sendMail($emails, $subject, $body, $altBody);
    }
}