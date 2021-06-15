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
     * Cookie success message type
     * @var string
     */
    public const COOKIE_MESSAGE_SUCCESS = 'success';

    /**
     * Cookie information message type
     * @var string
     */
    public const COOKIE_MESSAGE_INFO = 'info';

    /**
     * Cookie fail message type
     * @var string
     */
    public const COOKIE_MESSAGE_FAIL = 'fail';

    /**
     * Sets cookie message 
     * 
     * @param string message
     * @param string messageType
     */
    public static function setMessageCookie($message, $messageType = Extra::COOKIE_MESSAGE_SUCCESS){
        setCookie('message', $message, 0, "/");
        setCookie('messageType', $messageType, 0, "/");
    }

    /**
     * Retursn cookie message
     * 
     * @return array if get cookie, false otherwise
     */
    public static function getMessageCookie(){
        if (isset($_COOKIE['message'])) {
            $message = $_COOKIE['message'];
            $messageType = $_COOKIE['messageType'];

            return [
                'message' => $message,
                'type' => $messageType
            ];
        }
        return false;
    }

    /**
     * Removes cookie message 
     * 
     * @return boolean true if deleted, false otherwise
     */
    public static function deleteMessageCookie(){
        if (isset($_COOKIE['message'])) {
            unset($_COOKIE['message']); 
            unset($_COOKIE['messageType']); 
            setcookie('message', null, -1, '/'); 
            setcookie('messageType', null, -1, '/'); 
            return true;
        } 
        return false;
    }

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