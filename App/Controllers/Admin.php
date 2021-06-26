<?php

namespace App\Controllers; 

use App\Auth;
use App\Email;
use Core\View;
use App\Models\Trader;
use App\Models\User;
use App\Models\Notification;

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
        $this->requireAdmin();
    }

    public function traderRequestsAction()
    {
        View::renderTemplate('Admin/trader-requests.html');
    }

    /**
     * Each trader request action
     *
     * @return void
     */
    public function traderRequestAction()
    {
        $id = $this->route_params['id'];
        $trader = User::getTraderObjectFromId($id);

        if(!$trader || $trader->hasTraderGotNotice()){
            $this->show404();
        }

        if(!empty($_POST)){
            $accept = isset($_POST['accept']) ? true : false;
            $reject = isset($_POST['reject']) ? true : false;

            if($accept) {
                $trader->updateTraderApproval(Trader::REQUEST_STATUS_YES);
                $token = $trader->createUpdateToken();
                Email::sendTraderAccepted($trader, $token);
                $notification = new Notification([
                    'title' => "Welcome to efoodbasket",
                    'body' => "Hi, we are happy to see you here. We hope to collaborate with you forever. We will be looking for your feedback and support.",
                    'image_link' => "/public/images/efoodbasket-logo.png",
                    'sender_text' => "From efoodbasket",
                    'main_link' => "#",
                    'user_id' => $trader->user_id,
                    'is_seen' => Notification::IS_NOT_SEEN
                ]);
                $notification->save();
            }

            if($reject) {
                $trader->updateTraderApproval(Trader::REQUEST_STATUS_REJECTED);
                Email::sendTraderRejected($trader);
                $trader->delete();
            }
            
            $this->redirect('/admin/trader-requests/');
        }

        View::renderTemplate('Admin/trader-request.html', [
            "trader" => $trader
        ]);
    }
}
