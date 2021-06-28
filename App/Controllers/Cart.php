<?php

namespace App\Controllers;

use App\Auth;
use App\Extra;
use App\Models;
use App\Models\ProductCart;
use App\Models\ProductCategory;
use App\Models\Shop;
use App\Models\Validation\ProductValidation;
use Core\View;

/**
 * Cart controller
 *
 * PHP version 7.3
 */
class Cart extends \Core\Controller
{
    /**
     * Before filter
     */
    protected function before()
    {
        $this->requireLogin();

        if(Auth::isTraderAuthenticated()){
            $this->redirect("/");
        }
    }

    /**
     * Carts page
     * 
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate("Cart/cart.html");
    }

    /**
     * Add product item to cart
     * 
     * @return void
     */
    public function addAction() 
    {
        $data = [];
        if(empty($_POST)){
            $data['message'] = "Unable to handler request.";
            $data['type'] = "fail";
        }
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $cartObj = Models\Cart::getCartObject(Auth::getUserId());

        $productCart = new ProductCart([
            'quantity' => $quantity,
            'product_id' => $product_id,
            'cart_id' => $cartObj->CART_ID
        ]);

        $updatableCartProdObj = $cartObj->isProductInCart($product_id);

        if(($cartObj->cartItemsCount() + $quantity) > Models\Cart::MAX_CART_ITEMS_COUNT){
            $data['message'] = "Your cart is already full!!! <a class='blue-text' href='/cart/'>Edit Cart</a>";
            $data['type'] = "info" ;
        } else {
            if($updatableCartProdObj){
                $updatableCartProdObj->update($quantity);
                $data['message'] = "Product added to cart successfully. <a class='light-text' href='/cart/'>View Cart</a>";
                $data['type'] = "success" ;
            }
            else {
                if($productCart->save()){
                    $data['message'] = "Product added to cart successfully. <a class='light-text' href='/cart/'>View Cart</a>";
                    $data['type'] = "success" ;
                } else {
                    $data['message'] = "Unable to add to cart. Try reloading page.";
                    $data['type'] = "fail" ;
                }
            }
        }
        echo json_encode($data);
    }
}
