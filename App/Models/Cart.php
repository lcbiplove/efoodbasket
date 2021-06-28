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