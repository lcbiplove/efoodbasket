<?php

namespace App\Controllers;

use Core\View;

/**
 * Notification controller
 *
 * PHP version 7.3
 */
class Notification extends \Core\Controller
{
    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
        $this->requireLogin();
    }
    
    /**
     * Home of notification
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate("Notification/notifications.html");
    }

}
