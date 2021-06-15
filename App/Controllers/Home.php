<?php

namespace App\Controllers;

use \Core\View;
use \App\Extra;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $messagesArray = Extra::getMessageCookie();
        $message = $messagesArray['message'];
        $messageType = $messagesArray['type'];

        Extra::deleteMessageCookie();
        
        View::renderTemplate('Home/index.html', [
            'message'=> $message,
            'messageType' => $messageType
        ]);
    }

    /**
     * Show the check page
     *
     * @return void
     */
    public function checkAction()
    {
        View::renderTemplate('Home/check.html');
    }
}
