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

    /**
     * Get all orders of user
     * 
     * @param int user_id
     * @return mixed boolean, array
     */
    public static function getOrdersByUser($user_id)
    {
        $pdo = static::getDB();

        $sql = "SELECT ORDER_ID, COLLECTION_SLOT_ID, COLLECTION_DATE, P.PAYMENT_ID, VOUCHER_ID,
                to_char(ORDERED_DATE, 'YYYY-MM-DD HH24:MI:SS') AS ORDERED_DATE
                FROM ORDERS O, PAYMENTS P
                WHERE O.payment_id = P.payment_id AND p.user_id = :user_id
                ORDER BY ORDERED_DATE DESC";

        $result = $pdo->prepare($sql);
        $result->execute([$user_id]);
        $rows = $result->fetchAll(\PDO::FETCH_CLASS, self::class);
        return $rows;
    }

    /**
     * Get voucher object 
     * 
     * @return object
     */
    public function voucher()
    {
        return Voucher::getVoucherById($this->VOUCHER_ID);
    }

     /**
     * Get payment object 
     * 
     * @return object
     */
    public function payment()
    {
        return Payment::getPaymentById($this->PAYMENT_ID);
    }

    /**
     * Get collection slot object
     * 
     * @return object
     */
    public function collection_slot()
    {
        return CollectionSlot::getCollectionSlotById($this->COLLECTION_SLOT_ID);
    }

    /**
     * Get items count for that order
     * 
     * @return int
     */
    public function itemsCount()
    {
        $items = $this->getOrderItems();

        $count = 0;
        foreach ($items as $value) {
            $count += $value->QUANTITY;
        }
        return $count;
    }

    /**
     * Get all notifications from user id
     * 
     * @param int user_id
     * @return mixed boolean, array
     */
    public function getOrderItems()
    {
        $pdo = static::getDB();

        $sql = "SELECT OC.*
                FROM ORDERS O, ORDER_PRODUCTS OC, PRODUCTS p
                WHERE O.order_id = OC.order_id AND OC.product_id = p.product_id AND O.order_id = :order_id
                ORDER BY p.DISCOUNT DESC, OC.QUANTITY * p.price DESC, OC.QUANTITY DESC ";

        $result = $pdo->prepare($sql);
        $result->execute([$this->ORDER_ID]);

        return $result->fetchAll(\PDO::FETCH_CLASS, 'App\Models\OrderProduct');
    }

    /**
     * Order image
     * 
     * @return string
     */
    public function orderThumbnail()
    {
        $pdo = static::getDB();

        $sql = "SELECT PP.image_name AS IMAGE_NAME FROM ORDERS O, ORDER_PRODUCTS OP, PRODUCTS P, PRODUCT_IMAGES PP
                WHERE O.order_id = OP.order_id AND OP.product_id = P.product_id AND P.product_id = PP.product_id AND O.order_id = :order_id";

        $result = $pdo->prepare($sql);
        $result->execute([$this->ORDER_ID]);
        $rows = $result->fetch();
        return "/media/products/".$rows['IMAGE_NAME'];   
    }

    /**
     * Get time difference btween now and notified date
     * 
     * @return string
     */
    public function agoDate()
    {
        return Extra::timeAgo($this->ORDERED_DATE);
    }
}