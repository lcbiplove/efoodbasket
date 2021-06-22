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
     * Update attributes on table
     * 
     * @param array data key value pare of to be updated data
     * @return boolean
     */
    public function update($data)
    {
        $pdo = static::getDB();

        $shop_id = $this->shop_id;

        $query = 'UPDATE shops SET';
        $values = array();
        foreach ($data as $name => $value) {
            $query .= ' '.$name.' = :'.$name.','; 
            if($name == 'contact'){
                $values[':'.$name] = preg_replace('/[^0-9]/', '', $value);
            } else {
                $values[':'.$name] = filter_var($value, FILTER_SANITIZE_STRING); 
            }
        }
        $query = substr($query, 0, -1).''; 
        $query .= " WHERE shop_id = '$shop_id'";

        $sth = $pdo->prepare($query);
        return $sth->execute($values);
    }

    /**
     * Returns shop from shop id
     * 
     * @param int id
     * @return mixed shop object if exist, false otherwise
     */
    public static function getShopObject($id)
    {
        $pdo = static::getDB();
        $sql = "select * from shops where shop_id = :shop_id";
        $result = $pdo->prepare($sql);
        $result->execute([$id]);

        $rows = $result->fetch(); 

        if($rows){
            return new Shop($rows);
        }
        return false;
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
     * Get all the shops owned by trader
     * 
     * @return array 
     */
    public static function getTraderShops($trader_id)
    {
        $pdo = static::getDB();
        $sql = "select * from shops where trader_id = :trader_id";
        $result = $pdo->prepare($sql);
        $result->execute([$trader_id]);

        $rows = $result->fetchAll(); 

        return $rows;
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