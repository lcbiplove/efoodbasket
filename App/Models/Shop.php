<?php

namespace App\Models;

use Core\Model;

class Shop extends Model
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
     * Saves the shop object to database
     * 
     * @param integer trader id
     * @return boolean 
     */
    public function save(){
        $connection = static::getDB();
        
        $shop_name = filter_var($this->shop_name, FILTER_SANITIZE_STRING);
        $address = filter_var($this->address, FILTER_SANITIZE_STRING);
        $contact = filter_var($this->contact, FILTER_SANITIZE_STRING);

        $sql_query = "INSERT INTO SHOPS (shop_name, address, contact, trader_id) VALUES (:shop_name, :address, :contact, :trader_id)";

        $data = [
            ':shop_name' => $shop_name,
            ':address' => $address,
            ':contact' => $contact,
            ':trader_id' => $this->trader_id
        ];

        return $connection->prepare($sql_query)->execute($data);
    }

    /**
     * Count the number of shops owned by trader
     * 
     * @return int shop count
     */
    public static function getShopCountByTraderId($trader_id)
    {
        $pdo = static::getDB();
        $sql = "select count(*) from shops where trader_id = :trader_id";
        $result = $pdo->prepare($sql);
        $result->execute([$trader_id]);

        $rowsCount = $result->fetchColumn(); 

        return $rowsCount;
    }


    /**
     * Count the shops owned by trader
     * 
     * @return int shop count
     */
    public function getTraderShopCount()
    {
        $pdo = static::getDB();
        $sql = "select count(*) from shops where trader_id = :trader_id";
        $result = $pdo->prepare($sql);
        $result->execute([$this->trader_id]);

        $rowsCount = $result->fetchColumn(); 

        return $rowsCount;
    }
}