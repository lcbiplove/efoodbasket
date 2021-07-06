<?php

namespace App\Controllers;

use App\Auth;
use App\Models;
use Core\View;

/**
 * Order controller
 *
 * PHP version 7.3
 */
class Order extends \Core\Controller
{
    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
        $this->requireLogin();

        if(Auth::isTraderAuthenticated()){
            $this->redirect("/");
        }
    }

    /**
     * Home of order
     *
     * @return void
     */
    public function indexAction()
    {
        $orders = Models\Order::getOrdersByUser(Auth::getUserId());
        View::renderTemplate("Order/orders.html", [
            'orders' => $orders
        ]);
    }

    /**
     * Each order item
     * 
     * @return void
     */
    public function detailsAction()
    {
        $order_id = $this->route_params['order_id'];

        $order = Models\Order::getOrderObject($order_id);

        if($order->payment()->USER_ID != Auth::getUserId()) {
            $this->redirect("/");
        }

        View::renderTemplate("Order/order.html", [
            'order' => $order
        ]);
    }
}
