<?php

namespace App\Controllers;

use App\Auth;
use App\Extra;
use App\Models;
use App\Models\Validation\ShopValidation;
use Core\View;

/**
 * Home controller
 *
 * PHP version 7.3
 */
class Shop extends \Core\Controller
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
     * CHeck if the shop id belongs to that trader
     * and redirect if not belongs to
     * 
     * @param int trader_id
     * @param int shop_id
     * @return void
     */
    public function restrictToThatTraderOnly($trader_id, $shop_id)
    {
        $shops_owned_by_trader = Models\Shop::getTraderShops($trader_id);

        $shop_id_owned_by_trader = [];
    
        foreach ($shops_owned_by_trader as $value) {
            array_push($shop_id_owned_by_trader, $value['SHOP_ID']);
        }
        if(!in_array($shop_id, $shop_id_owned_by_trader)){
            $this->redirect("/");
        }
    }

    /**
     * Page to add shop
     * 
     * @return void
     */
    public function addShopAction()
    {
        $trader_id = Auth::getUserId();
        $count = Models\Shop::getShopCountByTraderId($trader_id);

        if($count >= 2){
            Extra::setMessageCookie("You have reached the maximum shops limit.", Extra::COOKIE_MESSAGE_INFO);
        }

        View::renderTemplate('Shop/add-shop.html');
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

        $this->restrictToThatTraderOnly($trader_id, $shop_id);
        
        $errors = [];

        $shop = Models\Shop::getShopObject($shop_id);

        if(!empty($_POST)){
            $data = $_POST;
            $data['trader_id'] = $trader_id;
            
            $shopValidation = new ShopValidation($data, ['shop_name', 'address', 'contact']);

            $errors = $shopValidation->getNamedErrors();

            if(empty($errors)){    
                Extra::setMessageCookie("Shop details updated succesfully.");
                
                $shop->update($_POST);

                $this->redirect($_SERVER['REQUEST_URI']);
            }

            $shop = new Models\Shop($_POST);
        }

        View::renderTemplate("Shop/edit-shop.html", [
            'shop' => $shop,
            'errors' => $errors
        ]);
    }

    /**
     * Delete shop data from database
     * 
     * @return void
     */
    public function deleteShopAction()
    {
        if(empty($_POST)){
            $this->redirect('/trader/shops/');
        }

        $trader_id = Auth::getUserId();
        $shop_id = $this->route_params['id'];

        $this->restrictToThatTraderOnly($trader_id, $shop_id);
        
        $shop = Models\Shop::getShopObject($shop_id);

        $shop->delete();

        Extra::setMessageCookie("Shop deleted successfully.");

        $this->redirect("/trader/shops/");
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
        $shop = new Models\Shop($data);
        
        $validation = new ShopValidation($data);
        $errors = $validation->getErrors();

        $rowsCount = $shop->getTraderShopCount();

        if($rowsCount >= 2){
            $errors['count'] = $rowsCount;
        }

        if(empty($errors)){
            $shop->save($trader_id);
            if(isset($_GET['next'])){
                $data['redirectTo'] = $_GET['next'];
            }
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
