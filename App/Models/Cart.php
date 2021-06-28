<?php

namespace App\Models;

use App\Auth;
use App\Extra;
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

class ProductCart extends Model
{
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
     * Create cart item for the cart
     * 
     * @return boolean
     */
    public function save()
    {
        $pdo = static::getDB();
        $sql_query = "INSERT INTO PRODUCT_CARTS (quantity, cart_id, product_id) VALUES (:quantity, :cart_id, :product_id)";

        return $pdo->prepare($sql_query)->execute([
            ':quantity' => $this->quantity,
            ':cart_id' => $this->cart_id,
            ':product_id' => $this->product_id
        ]);
    }

    /**
     * Update product cart object
     * 
     * @param int quantity
     * @return boolean
     */
    public function update($quantity)
    {
        $pdo = static::getDB();

        $sql = "UPDATE PRODUCT_CARTS SET quantity = :quantity + quantity WHERE product_cart_id = :product_cart_id";

        $result = $pdo->prepare($sql);

        return $result->execute([
            ':quantity' => $quantity,
            ':product_cart_id' => $this->PRODUCT_CART_ID
        ]);
    }

    /**
     * Get product object
     * 
     * @return object 
     */
    public function product()
    {
        return Product::getProductObjectById($this->PRODUCT_ID);
    }

    /**
     * Total price
     * 
     * @return int
     */
    public function totalPrice()
    {
        $product = $this->product();
        $price = $product->PRICE;
        $quantity = $this->QUANTITY;
        $discount = $product->DISCOUNT;
        $totalPrice = $price * (100-$discount) / 100 * $quantity;
        return $totalPrice;
    }

    /**
     * Returns product cart object by id
     * 
     * @param int product_cart_id
     * @return mixed boolean, object
     */
    public static function getProductCartObject($product_cart_id)
    {
        $pdo = static::getDB();
        $sql = "SELECT * FROM PRODUCT_CARTS WHERE product_cart_id = :product_cart_id";

        $result = $pdo->prepare($sql);
        $result->execute([$product_cart_id]);
        $result->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $row = $result->fetch();
        return $row;
    }
}