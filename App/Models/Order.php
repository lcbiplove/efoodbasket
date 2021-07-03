<?php

namespace App\Models;

use App\Extra;
use Core\Model;

class Order extends Model
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
     * Save order into the table
     * 
     * @return mixed boolean, object
     */
    public function save()
    {
        $db = static::getNextId("order_id_seq");

        $pdo = $db['db'];
        $next_id = $db['next_id'];

        $sql_query = "INSERT INTO ORDERS (order_id, ordered_date, collection_date, collection_slot_id, payment_id, voucher_id) 
                    VALUES (:order_id, TO_DATE(:ordered_date, 'YYYY-MM-DD HH24:MI:SS'), TO_DATE(:collection_date, 'YYYY-MM-DD HH24:MI:SS'), :collection_slot_id, :payment_id, :voucher_id)";

        $status = $pdo->prepare($sql_query)->execute([
            ':order_id' => $next_id,
            ':ordered_date' => Extra::getCurrentDateTime(),
            ':collection_date' => $this->collection_date,
            ':collection_slot_id' => $this->collection_slot_id,
            ':payment_id' => $this->payment_id,
            ':voucher_id' => $this->voucher_id == "null" ? null : $this->voucher_id,
        ]);

        if($status){
            $orderObj = Order::getOrderObject($next_id);
            return $orderObj;
        }
        return false;
    }

    /**
     * Get order object from id
     * 
     * @return mixed false, object
     */
    public static function getOrderObject($order_id)
    {
        $pdo = static::getDB();

        $sql = "SELECT * FROM ORDERS WHERE order_id = :order_id";

        $result = $pdo->prepare($sql);
        $result->execute([$order_id]);
        $result->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $row = $result->fetch();
        return $row;
    }
}