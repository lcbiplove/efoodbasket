<?php

namespace App;

use App\Models\Product;

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
        setCookie('message', $message, 0, "/", NULL, NULL, true);
        setCookie('messageType', $messageType, 0, "/", NULL, NULL, true);
    }

    /**
     * Retursn cookie message
     * 
     * @return array if get cookie, false otherwise
     */
    public static function getMessageCookie(){
        if (isset($_COOKIE['message'])) {
            $message = urldecode($_COOKIE['message']);
            $messageType = $_COOKIE['messageType'];

            return [
                'message' => $message,
                'type' => $messageType,
                'delete' => static::deleteMessageCookie()
            ];
        }
        return false;
    }

    /**
     * Removes cookie message 
     * 
     * @return boolean true if deleted, false otherwise
     */
    public static function deleteMessageCookie()
    {
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
     * Get phone number easily readable 
     * 
     * @return string beautified number
     */
    public static function getBeautifulPhone($phone_number)
    {
        $num = '('.substr($phone_number, 0, 3).') '.substr($phone_number, 3, 3).'-'.substr($phone_number,6);
        return $num;
    }

    /**
     * Returns current datetime of London
     * 
     * @return string datetime
     */
    public static function getCurrentDateTime()
    {
        date_default_timezone_set('Europe/London');
        $current = date('Y-m-d H:i:s',time());
        return $current;
    }

    /**
     * Return the complete image url
     * 
     * @param string image_name
     * @return string url
     */
    public static function productImageUrl($image_name)
    {
        return "/media/products/". $image_name;
    }

    /**
     * Get first image url of product id
     * 
     * @param int product_id
     * @return string
     */
    public static function getFirstImageUrl($product_id)
    {
        return "/media/products/". Product::getFirstProductImage($product_id);
    }

    /**
     * Get the owner id of product 
     * 
     * @param int product_id
     * @return mixed string, boolean
     */
    public static function getProductOwnerId($product_id)
    {
        return Product::getOwnerId($product_id);
    }


}