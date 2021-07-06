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

    /**
     * Search with options for the wishlist
     * 
     * @return array
     */
    public static function search($user_id, $rating, $minPrice, $maxPrice)
    {
        $pdo = static::getDB();

        $priceSql = "";
        $ratingSql = "";
        $orderBy = " ORDER BY w.added_date DESC ";

        $sql = "SELECT w.wishlist_id, p.* FROM WISHLISTS w, products p WHERE w.product_id = p.product_id AND user_id = '$user_id'";

        if(filter_var($rating, FILTER_VALIDATE_INT)) {
            $ratingSql = " HAVING avg(rating) >= '$rating' ";
            $sql = "
            SELECT p.* FROM products p, (
                SELECT product_id, avg(rating) as average from reviews group by product_id $ratingSql
            ) r, WISHLISTS w where
            r.product_id = p.product_id AND w.product_id = p.product_id AND user_id = '$user_id'
            ";
            $orderBy .= " , r.average DESC NULLS LAST";
        }
        if((filter_var($minPrice, FILTER_VALIDATE_INT) || $minPrice == 0) && filter_var($maxPrice, FILTER_VALIDATE_INT)){
            $priceSql = " and price BETWEEN $minPrice AND $maxPrice";
            $sql .= $priceSql;
            $orderBy .= " , price DESC";
        }
        $sql .= $orderBy;

        $result = $pdo->prepare($sql);
        
        $result->execute();

        return $result->fetchAll(\PDO::FETCH_CLASS, 'App\Models\Product');
    }
    
}