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

        Email::sendMail([$email], $subject, $body, $altBody);
    }


    public static function sendTraderRequestToAdmin($user)
    {
        $websiteUrl = Config::WEBSITE_NAME;
        $logoUrl = "https://raw.githubusercontent.com/lcbiplove/efood-ecommerce/main/images/efoodbasket-logo.png?token=AJXACGOHR6VDJGV5Y6ISEETA2GBIA";
        
        $link = "$websiteUrl/admin/trader-requests/$user->user_id/";

        $emails = User::getAllAdminEmails();

        $subject = "Review trader request";
        $altBody = "
        ---------------------------
        |        efoodBasket      |     
        ---------------------------

        Hi Admin, 

        A new trader is requesting to join our platform. They are waiting for your response
        to start working with us. Please review the user and make your decision.:


                            $link         


        Thanks 
        eFoodBasket

        -----------------------------------------------------------
        eFoodBasket - 98 Neville Street - Cleckhuderfax, UK - 56789
        ";

        

        $body = "
        <!DOCTYPE html><html><head><title></title><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><meta name='viewport' content='width=device-width, initial-scale=1'><meta http-equiv='X-UA-Compatible' content='IE=edge' /><style type='text/css'> /* CLIENT-SPECIFIC STYLES */ body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; } img { -ms-interpolation-mode: bicubic; } /* RESET STYLES */ img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; } table { border-collapse: collapse !important; } body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; } /* iOS BLUE LINKS */ a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; } /* MOBILE STYLES */ @media screen and (max-width:600px){ h1 { font-size: 32px !important; line-height: 32px !important; } } /* ANDROID CENTER FIX */ div[style*='margin: 16px 0;'] { margin: 0 !important; }</style><style type='text/css'></style></head><body style='background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;'><div style='display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;'> Review trader request</div><table border='0' cellpadding='0' cellspacing='0' width='100%'> <tr> <td bgcolor='#f4f4f4' align='center'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;' > <tr> <td align='center' valign='top' style='padding: 40px 10px 10px 10px;'> <a href='$websiteUrl' target='_blank'> <img alt='Logo' src='$logoUrl' width='100' height='40' style='display: block; width: 100px; max-width: 100px; min-width: 100px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; font-size: 18px;' border='0'> </a> </td> </tr> </table> </td> </tr> <tr> <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;' > <tr> <td bgcolor='#ffffff' align='center' valign='top' style='padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;'> <h1 style='font-size: 28px; font-weight: 400; margin: 0; letter-spacing: 0px;'>Review trader request</h1> </td> </tr> </table> </td> </tr> <tr> <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;' > <tr> <td bgcolor='#ffffff' align='left' style='padding: 20px 30px 40px 30px; color: #666666; font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;' > <p style='margin: 0;'>A new trader is requesting to join our platform. They are waiting for your response to start working with us. Please review the user and make your decision:</p> </td> </tr> <tr> <td bgcolor='#ffffff' align='left'> <table width='100%' border='0' cellspacing='0' cellpadding='0'> <tr> <td bgcolor='#ffffff' align='center' style='padding: 20px 30px 60px 30px;'> <table border='0' cellspacing='0' cellpadding='0'> <tr> <td align='center' style='border-radius: 3px;' bgcolor='#7FCB0A'><a href='$link' target='_blank' style='letter-spacing: 5px; font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #7FCB0A; display: inline-block;'>Link</a></td> </tr> </table> </td> </tr> </table> </td> </tr> <tr> <td bgcolor='#ffffff' align='left' style='padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #666666; font-family: Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;' > <p style='margin: 0;'>Thanks,<br>eFoodBasket </p> </td> </tr> </table> </td> </tr> <tr> <td bgcolor='#f4f4f4' align='center' style='padding: 0px 10px 0px 10px;'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;' > <tr> <td bgcolor='#f4f4f4' align='left' style='padding: 30px 30px 30px 30px; color: #666666; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;' > <p style='margin: 0;'> <a href='$websiteUrl' target='_blank' style='color: #111111; font-weight: 700;'>Home</a> - <a href='$websiteUrl/signup/' target='_blank' style='color: #111111; font-weight: 700;'>Login</a> - <a href='$websiteUrl/login/' target='_blank' style='color: #111111; font-weight: 700;'>Signup</a> </p> </td> </tr> <tr> <td bgcolor='#f4f4f4' align='left' style='padding: 0px 30px 30px 30px; color: #666666; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;' > <p style='margin: 0;'>eFoodBasket - 98 Neville Street - Cleckhuderfax, UK - 56789</p> </td> </tr> </table> </td> </tr></table></body></html>
        ";

        Email::sendMail($emails, $subject, $body, $altBody);
    }
}