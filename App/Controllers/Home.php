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

    public function after()
    {
        Extra::deleteMessageCookie();
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('Home/index.html');
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
