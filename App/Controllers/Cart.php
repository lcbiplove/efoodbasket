<?php

namespace App\Controllers;

use App\Auth;
use App\Models;
use App\Models\ProductCart;
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
        $replace = isset($_POST['replace']) ? TRUE : FALSE;
        $cartObj = Models\Cart::getCartObject(Auth::getUserId());

        $productCart = new ProductCart([
            'quantity' => $quantity,
            'product_id' => $product_id,
            'cart_id' => $cartObj->CART_ID
        ]);

        $data['replace'] = $replace;
        $updatableCartProdObj = $cartObj->isProductInCart($product_id);

        if(($cartObj->cartItemsCount() + $quantity) > Models\Cart::MAX_CART_ITEMS_COUNT && !$replace){
            $data['message'] = "Your cart is already full!!! <a class='blue-text' href='/cart/'>Edit Cart</a>";
            $data['type'] = "info" ;
        } else {
            if($updatableCartProdObj){
                $updatableCartProdObj->update($quantity, $replace);
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

    /**
     * Delete action for product item
     * 
     * @return void
     */
    public function deleteAction()
    {
        $data = [];
        if(empty($_POST)){
            $data['message'] = "Unable to handler request.";
            $data['type'] = "fail";
        }
        $product_id = $_POST['product_id'];
        $user_id = Auth::getUserId();
        
        if(ProductCart::delete($user_id, $product_id)){
            $data['message'] = "Cart item deleted successfully.";
            $data['type'] = "success";
        } else {
            $data['message'] = "Unable to delete cart items. Try reloading page.";
            $data['type'] = "fail";
        }
        echo json_encode($data);
    }
}
