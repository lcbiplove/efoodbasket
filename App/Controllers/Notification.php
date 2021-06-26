<?php

namespace App\Controllers;

use App\Auth;
use Core\View;
use App\Models;
use Core\Model;

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

    public function restrictToOwner($notif_id) 
    {
        $notification = Models\Notification::getNotificationFromId($notif_id);

        if(!$notification) {
            $this->show404();
        }

        if($notification->USER_ID != Auth::getUserId()){
            $this->redirect("/");
        }
    }

    public function sampleNotif()
    {
        $data = [];
        $data['title'] = "Welcome to efoodbasket";
        $data['body'] = "Hi we are happy to see you in our platform.";
        $data['image_link'] = "https://www.essayroyal.com/HelloDjango/media/blog/posters/23569861-sample-grunge-red-round-stamp_TMfcsJz.jpg";
        $data['sender_text'] = "By admin";
        $data['main_link'] = "#";
        $data['user_id'] = 2;
        $data['is_seen'] = Models\Notification::IS_NOT_SEEN;
        $notif = new Models\Notification($data);
        $notif->save();
    }

    /**
     * Home of notification
     *
     * @return void
     */
    public function indexAction()
    {
        $user_id = Auth::getUserId();
        $notifications = Models\Notification::getNotificationsFromUserId($user_id);
        $unseenCount = Models\Notification::getUnseenCounFromUserId($user_id);
        View::renderTemplate("Notification/notifications.html", [
            'notifications' => $notifications,
            'unseenCount' => $unseenCount
        ]);
    }

    /**
     * To make notification seen. Handle AJAX request
     * 
     * @return void
     */
    public function makeSeenAction()
    {
        $notif_id = $this->route_params['id'];

        $data = [];

        $notification = Models\Notification::getNotificationFromId($notif_id);

        if(!$notification) {
            $data['code'] = 404;
        } else if($notification->USER_ID != Auth::getUserId()){
            $data['code'] = 401;
        } else if($notification->isSeen()){
            $data['code'] = 409;
        } else if($notification->makeSeen()){
            $data['code'] = 200;
        }

        echo json_encode($data);
    }
}
