<?php

namespace App\Models;

use Core\Model;

class Cart extends Model
{
    /**
     * Maximum items that can be placed inside cart
     * @var int
     */
    public const MAX_CART_ITEMS_COUNT = 20;

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $lowerKey = strtolower($key);
            $this->$lowerKey = $value;
        };
    }

    /**
     * Create cart for the user
     * 
     * @return boolean
     */
    public function create()
    {
        $pdo = static::getDB();
        $sql_query = "INSERT INTO CARTS (user_id) VALUES (:user_id)";

        return $pdo->prepare($sql_query)->execute([$this->user_id]);
    }


    /**
     * Total price of the cart
     * 
     * @return int
     */
    public function totalPrice()
    {
        $cartItems = $this->getCartItems();

        $total = 0;
        foreach ($cartItems as $value) {
            $total += $value->totalPrice();
        }
        return $total;
    }

    /**
     * Get all notifications from user id
     * 
     * @param int user_id
     * @return mixed boolean, array
     */
    public function getCartItems()
    {
        $pdo = static::getDB();

        $sql = "SELECT pc.*
                FROM CARTS c, PRODUCT_CARTS pc WHERE c.cart_id = pc.cart_id AND c.cart_id = :cart_id";

        $result = $pdo->prepare($sql);
        $result->execute([$this->CART_ID]);

        return $result->fetchAll(\PDO::FETCH_CLASS, 'App\Models\ProductCart');
    }

    /**
     * Check if product is already in the cart or not
     * 
     * @param int product_id
     * @return mixed product cart obj if true, false otherwise
     */
    public function isProductInCart($product_id)
    {
        $cartItems = $this->getCartItems();

        foreach ($cartItems as $value) {
            if($value->PRODUCT_ID == $product_id) {
                return ProductCart::getProductCartObject($value->PRODUCT_CART_ID);
            }
        }
        return false;
    }


    /**
     * Returns number of items inside cart
     * 
     * @return int
     */
    public function cartItemsCount()
    {
        $items = $this->getCartItems();

        $count = 0;
        foreach ($items as $value) {
            $count += $value->QUANTITY;
        }
        return $count;
    }

    /**
     * Check if cart is full
     * 
     * @return boolean
     */
    public function isCartFull()
    {
        return $this->cartItemsCount() >= static::MAX_CART_ITEMS_COUNT;
    }

    /**
     * Returns if cart is empty or not
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->TOTAL_ITEMS == 0;
    }

    /**
     * Cart object from user id
     * 
     * @param int user_id
     * @return mixed boolean, object
     */
    public static function getCartObject($user_id)
    {
        $pdo = static::getDB();
        $sql = "SELECT * FROM CARTS WHERE user_id = :user_id";

        $result = $pdo->prepare($sql);
        $result->execute([$user_id]);
        $result->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $row = $result->fetch();
        return $row;
    }
}

