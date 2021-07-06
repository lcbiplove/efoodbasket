<?php

namespace App;

use App\Models\Product;
use Core\Model;

/**
 * Search class
 * For the extra functions
 * 
 * PHP 7.3
 */
class Search extends Model
{
    public const ORDER_BY_LATEST = 'latest';

    public const ORDER_BY_PRICE_LOW = "price_low";

    public const ORDER_BY_PRICE_HIGH = "price_high";

    public const ORDER_BY_RATING_HIGH = "rating_high";

    public const ORDER_BY_RATING_LOW = "rating_low";


    /**
     * Search results
     * 
     * @return array
     */
    public static function search($key, $order_by, $category_id, $rating, $minPrice, $maxPrice)
    {
        $pdo = static::getDB();

        $sql = "SELECT * FROM products p WHERE upper(product_name) LIKE :key";

        $priceSql = "";
        $ratingSql = "";
        $leftOrInner =  " LEFT ";

        if(filter_var($category_id, FILTER_VALIDATE_INT)){
            $sql = "SELECT p.* FROM products p, PRODUCT_CATEGORIES pc
                    WHERE p.category_id = pc.category_id AND p.category_id = '$category_id' AND upper(product_name) LIKE :key ";
        }

        if(filter_var($rating, FILTER_VALIDATE_INT)) {
            $leftOrInner  = " INNER ";
            $ratingSql = " HAVING avg(rating) >= '$rating' ";
            $sql = "
            SELECT p.* FROM products p, (
                SELECT product_id, avg(rating) as average from reviews group by product_id $ratingSql
            ) r 
            WHERE p.product_id = r.product_id AND upper(product_name) LIKE :key 
            ";
        }


        if((filter_var($minPrice, FILTER_VALIDATE_INT) || $minPrice == 0) && filter_var($maxPrice, FILTER_VALIDATE_INT)){
            $priceSql = " and price BETWEEN $minPrice AND $maxPrice";
            $sql .= $priceSql;
        }
        
        if($order_by == static::ORDER_BY_PRICE_LOW){
            $sql .= " order by price";
        }
        else if($order_by == static::ORDER_BY_PRICE_HIGH){
            $sql .= " order by price DESC";
        }
        else if($order_by == static::ORDER_BY_RATING_HIGH){
            $sql = "
            SELECT p.* FROM products p 
            $leftOrInner JOIN (
                            SELECT product_id, avg(rating) as average from reviews group by product_id $ratingSql
            ) r 
            ON p.product_id = r.product_id 
            WHERE upper(product_name) LIKE :key 
            ";
            $sql .= $priceSql;
            $sql .= " ORDER BY r.average DESC NULLS LAST";
        }
        else if($order_by == static::ORDER_BY_RATING_LOW){
            $sql = "
            SELECT p.* FROM products p 
            $leftOrInner JOIN (
                SELECT product_id, avg(rating) as average from reviews group by product_id $ratingSql
            ) r 
            ON p.product_id = r.product_id 
            WHERE upper(product_name) LIKE :key
            ";
            $sql .= $priceSql;
            $sql .= " ORDER BY r.average ASC NULLS FIRST";
        }
        else {
            $sql .= " order by added_date DESC";
        }

        $result = $pdo->prepare($sql);
        
        $result->execute([
            ':key' => "%".strtoupper($key)."%"
        ]);

        return $result->fetchAll(\PDO::FETCH_CLASS, 'App\Models\Product'); 
    }

    /**
     * Array of sorting options
     * 
     * @return array
     */
    public static function sortsBy()
    {
        $sortsBy = [];

        $sortsBy[0] = [
            "value" => static::ORDER_BY_LATEST,
            "display" => "Latest",
        ];
        $sortsBy[1] = [
            "value" => static::ORDER_BY_PRICE_LOW,
            "display" => "Price: Low to High",
        ];
        $sortsBy[2] = [
            "value" => static::ORDER_BY_PRICE_HIGH,
            "display" => "Price: High to Low",
        ];
        $sortsBy[3] = [
            "value" => static::ORDER_BY_RATING_LOW,
            "display" => "Rating: Low to High",
        ];
        $sortsBy[4] = [
            "value" => static::ORDER_BY_RATING_HIGH,
            "display" => "Rating: High to Low",
        ];
        return $sortsBy;
    }
}
