<?php

namespace App\Models;

use App\Auth;
use Core\Model;

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
     * @return mixed cart items count if true, false otherwise
     */
    public function save()
    {
        $pdo = static::getDB();
        $sql_query = "INSERT INTO PRODUCT_CARTS (quantity, cart_id, product_id) VALUES (:quantity, :cart_id, :product_id)";

        $status = $pdo->prepare($sql_query)->execute([
            ':quantity' => $this->quantity,
            ':cart_id' => $this->cart_id,
            ':product_id' => $this->product_id
        ]);
        if($status){
            $cartObj = Cart::getCartObject(Auth::getUserId());
            return $cartObj->cartItemsCount();
        }
        return false;
    }

    /**
     * Update product cart object
     * 
     * @param int quantity
     * @return boolean
     */
    public function update($quantity, $replace)
    {
        $pdo = static::getDB();

        $sql = "UPDATE PRODUCT_CARTS SET quantity = :quantity + quantity WHERE product_cart_id = :product_cart_id";

        if($replace === TRUE) {
            $sql = "UPDATE PRODUCT_CARTS SET quantity = :quantity WHERE product_cart_id = :product_cart_id";
        }
        
        $result = $pdo->prepare($sql);

        $status = $result->execute([
            ':quantity' => $quantity,
            ':product_cart_id' => $this->PRODUCT_CART_ID
        ]);
        if($status){
            $cartObj = Cart::getCartObject(Auth::getUserId());
            return $cartObj->cartItemsCount();
        }
        return false;
    }

    /**
     * Delete product cart object
     * 
     * @param int user_id
     * @param int product_id
     * @return boolean
     */
    public static function delete($user_id, $product_id)
    {
        $pdo = static::getDB();

        $sql = "DELETE (SELECT *
                FROM PRODUCT_CARTS pc
                INNER JOIN CARTS c
                ON c.cart_id = pc.cart_id
                WHERE c.user_id = :user_id AND pc.product_id = :product_id)";

        $result = $pdo->prepare($sql);

        return $result->execute([
            ':product_id' => $product_id,
            ':user_id' => $user_id
        ]);
    }

    /**
     * Delete multiple cart items from the cart
     * 
     * @param int user_id
     * @param string product ids separated by comma: 1, 2, 3, 4
     * @return boolean
     */
    public static function deleteMultiple($user_id, $product_ids)
    {
        $pdo = static::getDB();

        $sql = "DELETE (SELECT *
            FROM PRODUCT_CARTS pc
            INNER JOIN CARTS c
            ON c.cart_id = pc.cart_id
            WHERE c.user_id = '$user_id' AND
            pc.product_id in ($product_ids))";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute();
    }

    /**
     * Delete all cart product of user
     * 
     * @param int user_id
     * @return boolean
     */
    public static function deleteAll($user_id)
    {
        $pdo = static::getDB();

        $sql = "DELETE (SELECT *
        FROM PRODUCT_CARTS pc
        INNER JOIN CARTS c
        ON c.cart_id = pc.cart_id
        WHERE c.user_id = '$user_id')";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute();
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