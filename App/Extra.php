<?php

namespace App;

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\SMTP;

use App\Config;
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
     * Sends the email to the clients 
     * 
     * @param string toEmail - email of recipient
     * @param string subject - subject of email
     * @param string body - html string of main body
     * @param string altBody - body without html
     * 
     * @return boolean true if sent, false otherwise
     */
    public static function sendMail($toEmail, $subject, $body, $altBody)
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
        $mail->addAddress($toEmail);     
        $mail->addReplyTo('efoodbasket@noreply.com', 'No Reply');


        $mail->isHTML(true);                                  
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody;

        return $mail->send();
    }

}