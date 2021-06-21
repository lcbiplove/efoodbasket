<?php

namespace App\Controllers;

use App\Auth;
use App\Extra;
use Core\View;
use App\Models\Trader;
use App\Models\User;
use App\Models\Validation\UserValidation;

/**
 * Home controller
 *
 * PHP version 7.3
 */
class LoggedUser extends \Core\Controller
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
     * Show manage account page
     * 
     * @return void
     */
    public function manageAccountAction()
    {
        $errors = [];
        $preserved = [];
        if(!empty($_POST)){
            $userValidation = new UserValidation($_POST, ['fullname', 'address', 'contact']);
            $errors = $userValidation->getNamedErrors();

            $preserved = $_POST;

            if(empty($errors)){
                $user = Auth::getUser();
                $user->update($_POST);
                Extra::setMessageCookie("Profile updated successfully.");
                $this->redirect("/manage-account/");
            }
        }
        View::renderTemplate("LoggedUser/manage-account.html", [
            'errors' => $errors,
            'preserved' => $preserved
        ]);
    }

}
