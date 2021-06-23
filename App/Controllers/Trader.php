<?php

namespace App\Controllers;

use App\Auth;
use App\Extra;
use App\Models\Shop;
use App\Models\Validation\ShopValidation;
use Core\View;

/**
 * Home controller
 *
 * PHP version 7.3
 */
class Trader extends \Core\Controller
{
    /**
     * Page to show all shops
     * 
     * @return void
     */
    public function shopsAction()
    {
        $this->requireTrader();

        $shops = Shop::getTraderShops(Auth::getUserId());
        View::renderTemplate("Trader/shops.html", [
            "shops" => $shops,
            "showAddShop" => true
        ]);
    }

    /**
     * Public shops for any user by trader id
     * 
     * @return void
     */
    public function shopsByTraderAction()
    {
        $trader_id = $this->route_params['id'];
        $shops = Shop::getTraderShops($trader_id);

        View::renderTemplate("Trader/shops.html", [
            "shops" => $shops
        ]);
    }


    /**
     * Each shop details by trader id and shop id
     * 
     * @return void
     */
    public function eachShopAction()
    {
        $shop_id = $this->route_params['shop_id'];
        $shop = Shop::getShopObject($shop_id);

        if(!$shop){
            $this->show404();
        }

        View::renderTemplate("Trader/each-shop.html", [
            "shop" => $shop
        ]);
    }
}
