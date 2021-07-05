<?php

namespace App;

use App\Models\Product;
use App\Models\ProductCategory;

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
     * Give time ago like expression
     * 
     * @return string
     */
    public static function timeAgo($datetime, $full = false)
    {
        date_default_timezone_set('Europe/London');
        $now = new \DateTime;
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);
    
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
    
        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }
    
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    /**
     * All product categories for search
     * 
     * @return array
     */
    public static function getCategories()
    {
        return ProductCategory::getAllCategories();
    }
}
