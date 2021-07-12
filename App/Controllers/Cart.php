<?php

namespace App\Controllers;

use App\Auth;
use App\Models;
use App\Models\ProductCart;
use App\Models\CollectionSlot;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Payment;
use App\Models\Voucher;
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
    public function index()
    {
        if(!Auth::isAuthenticated()){
            View::renderTemplate("Cart/cart-local.html");
            return;
        }
        
        $collection_slots = CollectionSlot::getCollectionSlots();
        $collection_days = CollectionSlot::getCollectionDays();
        View::renderTemplate("Cart/cart.html", [
            'collection_slots' => $collection_slots,
            'collection_days' => $collection_days
        ]);
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
        $data['totalItems'] = $cartObj->cartItemsCount();

        if(($cartObj->cartItemsCount() + $quantity) > Models\Cart::MAX_CART_ITEMS_COUNT && !$replace){
            $data['message'] = "Your cart is already full!!! <a class='blue-text' href='/cart/'>Edit Cart</a>";
            $data['type'] = "info" ;
        } else {
            if($updatableCartProdObj){
                $newProductCart = $updatableCartProdObj->update($quantity, $replace);
                $data['message'] = "Product added to cart successfully. <a class='light-text' href='/cart/'>View Cart</a>";
                $data['type'] = "success" ;
                $data['totalItems'] = $newProductCart;
            }
            else {
                $newProductCart = $productCart->save();
                if($newProductCart){
                    $data['message'] = "Product added to cart successfully. <a class='light-text' href='/cart/'>View Cart</a>";
                    $data['type'] = "success" ;
                    $data['totalItems'] = $newProductCart;
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

    /**
     * Delete multiple items
     * 
     * @return void
     */
    public function deleteMultiple()
    {
        $data = [];
        if(empty($_POST)){
            $data['message'] = "Unable to handler request.";
            $data['type'] = "fail";
        }
        $product_ids = $_POST['product_ids'];
        $user_id = Auth::getUserId();

        if(ProductCart::deleteMultiple($user_id, $product_ids)){
            $data['message'] = "Cart item deleted successfully.";
            $data['type'] = "success";
        } else {
            $data['message'] = "Unable to delete cart items. Try reloading page.";
            $data['type'] = "fail";
        }
        echo json_encode($data);
    }

    /**
     * Check for vouchers
     * 
     * @return void
     */
    public function voucherAction()
    {
        $data = [];
        if(empty($_POST)){
            $data['message'] = "Unable to handle request.";
            $data['type'] = "fail";
        }
        $code = $_POST['code'];

        $voucher = Voucher::checkCode($code);
        if($voucher) {
            if($voucher->isExpired()){
                $data['message'] = "Code is already expired.";
                $data['type'] = "fail";
            } else {
                $data['message'] = "Code used successfully.";
                $data['type'] = "success";
                $data['data'] = $voucher;
            }
        } else {
            $data['message'] = "Code is invalid.";
            $data['type'] = "fail";
        }
        echo json_encode($data);
    }

    /**
     * Add payment of the cart
     * 
     * @return void
     */
    public function paymentAction()
    {
        $data = [];
        if(empty($_POST)){
            $data['message'] = "Unable to handle request.";
            $data['type'] = "fail";
        }

        $args = [];
        $args['amount'] = $_POST['amount'];
        $args['user_id'] = Auth::getUserId();
        $args['paypal_order_id'] = $_POST['paypal_order_id'];
        $args['paypal_payer_id'] = $_POST['paypal_payer_id'];

        $payment = new Payment($args);
        $new_payment = $payment->save();

        $args = [];

        $args['collection_date'] = $_POST['collection_date'];
        $args['collection_slot_id'] = $_POST['collection_slot_id'];
        $args['voucher_id'] = $_POST['voucher_id'];
        $args['payment_id'] = $new_payment->PAYMENT_ID;

        $order = new Order($args);
        $new_order = $order->save();

        $args = [];
        $args['order_id'] = $new_order->ORDER_ID;

        $cartObj = Models\Cart::getCartObject(Auth::getUserId());
        $cartItems = $cartObj->getCartItems();

        $orderProductArray = [];
        foreach ($cartItems as $key => $value) {
            $array = [
                'quantity' => $value->QUANTITY,
                'product_id' => $value->PRODUCT_ID,
                'order_id' => $new_order->ORDER_ID,
                'product_name' => $value->product()->PRODUCT_NAME,
                'price' => $value->product()->PRICE,
                'discount' => $value->product()->DISCOUNT,
                'image' => $value->product()->thumbnailUrl(),
            ];
            $orderProduct = new OrderProduct($array);
            $orderProduct->save();
            array_push($orderProductArray, $array);
        } 

        $cartItemsCount = $cartObj->cartItemsCount();

        $notification = new Notification([
            'title' => "Order Placed",
            'body' => "Your order of $cartItemsCount ".($cartItemsCount > 1 ? "items" : "item")." is placed. You will be able to collect them from collection slot after {$_POST['collection_date']}.",
            'image_link' => "/public/images/notif-order.png",
            'sender_text' => "From efoodbasket",
            'main_link' => "/orders/{$new_order->ORDER_ID}/",
            'user_id' => Auth::getUserId(),
            'is_seen' => Notification::IS_NOT_SEEN
        ]);
        $notification->save();

        foreach ($cartItems as $key => $value) {
            $notification = new Notification([
                'title' => "Order Received",
                'body' => "Customer has ordered {$value->QUANTITY} ". ($value->QUANTITY > 1 ? "items" : "item") ." of ".$value->product()->PRODUCT_NAME." from your shop.",
                'image_link' => "/public/images/notif-order.png",
                'sender_text' => "From efoodbasket",
                'main_link' => "/products/{$value->PRODUCT_ID}/",
                'user_id' => $value->product()->getOwnerId(),
                'is_seen' => Notification::IS_NOT_SEEN
            ]);
            $notification->save();

            $value->product()->update(['quantity' => $value->product()->QUANTITY - $value->QUANTITY, 'availability' => true ]);
        }

        ProductCart::deleteAll(Auth::getUserId());

        $data['payment'] = $new_payment;
        $data['order'] = $new_order;
        $data['orderProduct'] = $orderProductArray;
        $data['collectionDate'] = date("d M", strtotime($_POST['collection_date'])); 

        echo json_encode($data);
    }
}
