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
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
        $this->requireTrader();
    }

    /**
     * Page to show all shops
     * 
     * @return void
     */
    public function shopsAction()
    {
        $shops = Shop::getTraderShops(Auth::getUserId());
        View::renderTemplate("Trader/shops.html", [
            "shops" => $shops
        ]);
    }

    /**
     * Page to add shop
     * 
     * @return void
     */
    public function addShopAction()
    {
        $trader_id = Auth::getUserId();
        $count = Shop::getShopCountByTraderId($trader_id);

        if($count >= 2){
            Extra::setMessageCookie("You have reached the maximum shops limit.", Extra::COOKIE_MESSAGE_INFO);
        }

        View::renderTemplate('Trader/add-shop.html');
    }

    /**
     * Edit shop
     * 
     * @return void
     */
    public function editShopAction()
    {
        $trader_id = Auth::getUserId();
        $shop_id = $this->route_params['id'];
        $errors = [];

        $shop = Shop::getShopObject($shop_id);

        if(!empty($_POST)){
            $data = $_POST;
            $data['trader_id'] = $trader_id;
            
            $shopValidation = new ShopValidation($data, ['shop_name', 'address', 'contact']);

            $errors = $shopValidation->getNamedErrors();


            if(empty($errors)){
                $shops_owned_by_trader = Shop::getTraderShops($trader_id);

                $shop_id_owned_by_trader = [];
    
                foreach ($shops_owned_by_trader as $value) {
                    array_push($shop_id_owned_by_trader, $value['SHOP_ID']);
                }
    
                if(!in_array($shop_id, $shop_id_owned_by_trader)){
                    $this->redirect("/");
                }
    
                Extra::setMessageCookie("Shop details updated succesfully.");
                
                $shop->update($_POST);

                $this->redirect($_SERVER['REQUEST_URI']);
            }

            $shop = new Shop($_POST);
        }

        View::renderTemplate("Trader/edit-shop.html", [
            'shop' => $shop,
            'errors' => $errors
        ]);
    }


    /**
     * Ajax handler for add shop post request
     * 
     * @return void
     */
    public function ajaxAddShopAction()
    {
        if(empty($_POST)){
            $this->show404();
            return;
        }
        $trader_id = Auth::getUserId();

        $data = $_POST;
        $data['trader_id'] = $trader_id;
        $shop = new Shop($data);
        
        $validation = new ShopValidation($data);
        $errors = $validation->getErrors();

        $rowsCount = $shop->getTraderShopCount();

        if($rowsCount >= 2){
            $errors['count'] = $rowsCount;
        }

        if(empty($errors)){
            $shop->save($trader_id);
            $data['success'] = 1;
            if($rowsCount == 1){
                $rowsCount = 2;
            }
            $data['count'] = $rowsCount;
            echo json_encode($data);
            exit();
        }

        $errors['error'] = 1;
        echo json_encode($errors);
    }
}
