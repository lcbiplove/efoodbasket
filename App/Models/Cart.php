<?php

namespace App\Models;

use App\Auth;
use App\Extra;
use Core\Model;

class Cart extends Model
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
     * Returns if cart is empty or not
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->PRODUCT_COUNT == 0;
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
}