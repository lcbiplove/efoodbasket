<?php

namespace App\Models;

use Core\Model;

class OrderProduct extends Model
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
     * Save order product in table
     * 
     * @return boolean
     */
    public function save()
    {
        $pdo = static::getDB();
        $sql_query = "INSERT INTO ORDER_PRODUCTS (quantity, order_id, product_id) VALUES (:quantity, :order_id, :product_id)";

        return $pdo->prepare($sql_query)->execute([
            ':quantity' => $this->quantity,
            ':order_id' => $this->order_id,
            ':product_id' => $this->product_id
        ]);
    }

    /**
     * Product for the order product
     * 
     * @return object
     */
    public function product()
    {
        return Product::getProductObjectById($this->PRODUCT_ID);
    }
}