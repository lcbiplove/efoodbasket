<?php

namespace App\Controllers; 

use App\Auth;
/**
 * Home controller
 *
 * PHP version 7.3
 */
class Admin extends \Core\Controller
{
    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
        // $this->requireAdmin();
    }

    /**
     * Home of notification
     *
     * @return void
     */
    public function traderRequestAction($id=null)
    {
        echo "HI";
    }

    public function traderRequestsAction()
    {
        echo "HI";
    }

}
