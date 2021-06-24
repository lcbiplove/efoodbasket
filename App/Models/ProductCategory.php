<?php

namespace App\Models;

use Core\Model;

class ProductCategory extends Model
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
     * Return all categories from database
     * 
     * @return mixed array if present, false otherwise
     */
    public static function getAllCategories()
    {
        $pdo = static::getDB();

        $sql = "select * from PRODUCT_CATEGORIES";

        $result = $pdo->prepare($sql);
        
        $result->execute();

        $user_array = $result->fetchAll(); 

        return $user_array;
    }

    /**
     * Return product category if present
     * 
     * @return mixed ProductCategory object if true, false otherwise
     */
    public static function getProductCategoryById($id)
    {
        $pdo = static::getDB();

        $sql = "select * from PRODUCT_CATEGORIES where category_id = :category_id";

        $result = $pdo->prepare($sql);
        
        $result->execute([$id]);

        $user_array = $result->fetch(); 

        try {
            return new ProductCategory($user_array);
        } catch (\Throwable $th) {
            return false;
        }
    }

}