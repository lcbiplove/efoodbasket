<?php

namespace App\Models;

use App\Extra;
use Core\Model;

class WishList extends Model
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
     * Save wishlist object 
     * 
     * @return boolean
     */
    public function save()
    {
        $pdo = static::getDB();

        $sql_query = "INSERT INTO WISHLISTS (user_id, product_id, added_date) VALUES (:user_id, :product_id, TO_DATE(:added_date, 'YYYY-MM-DD HH24:MI:SS'))";
        $result = $pdo->prepare($sql_query);
        return $result->execute([
            ':user_id' => $this->user_id,
            ':product_id' => $this->product_id,
            ':added_date' => Extra::getCurrentDateTime()
        ]);
    }

    /**
     * Delete wishlist object 
     * 
     * @return boolean
     */
    public function delete()
    {
        $pdo = static::getDB();

        $sql_query = "DELETE FROM WISHLISTS WHERE user_id = :user_id AND product_id = :product_id";
        $result = $pdo->prepare($sql_query);
        return $result->execute([
            ':user_id' => $this->user_id,
            ':product_id' => $this->product_id
        ]);
    }

    /**
     * Return wishlist object from user_id
     * 
     * @param int user_id
     * @return mixed boolean, obj
     */
    public static function getProductsByUserId($user_id)
    {
        $pdo = static::getDB();

        $sql_query = "SELECT w.wishlist_id, p.* FROM WISHLISTS w, products p WHERE w.product_id = p.product_id AND user_id = :user_id";
        $result = $pdo->prepare($sql_query);
        $result->execute([$user_id]);
        return $result->fetchAll(\PDO::FETCH_CLASS, 'App\Models\Product');
    }
    
}