<?php

namespace App\Models;

use App\Extra;
use Core\Model;

class Payment extends Model
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
     * Save payment into the table
     * 
     * @return mixed boolean, object
     */
    public function save()
    {
        $db = static::getNextId("payment_id_seq");

        $pdo = $db['db'];
        $next_id = $db['next_id'];

        $sql_query = "INSERT INTO PAYMENTS (payment_id, amount, payment_date, user_id, paypal_order_id, paypal_payer_id) 
                    VALUES (:payment_id, :amount, TO_DATE(:payment_date, 'YYYY-MM-DD HH24:MI:SS'), :user_id, :paypal_order_id, :paypal_payer_id)";

        $status = $pdo->prepare($sql_query)->execute([
            ':payment_id' => $next_id,
            ':amount' => $this->amount,
            ':payment_date' => Extra::getCurrentDateTime(),
            ':user_id' => $this->user_id,
            ':paypal_order_id' => $this->paypal_order_id,
            ':paypal_payer_id' => $this->paypal_payer_id
        ]);

        if($status){
            $paymentObj = Payment::getPaymentObject($next_id);
            return $paymentObj;
        }
        return false;
    }

    /**
     * Get payment object from id
     * 
     * @return mixed false, object
     */
    public static function getPaymentObject($payment_id)
    {
        $pdo = static::getDB();

        $sql = "SELECT * FROM PAYMENTS WHERE payment_id = :payment_id";

        $result = $pdo->prepare($sql);
        $result->execute([$payment_id]);
        $result->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $row = $result->fetch();
        return $row;
    }
    
    /**
     * Get voucher object
     * 
     * @param int payment_id
     * @return object payment object
     */
    public static function getPaymentById($payment_id)
    {
        $pdo = static::getDB();

        $sql = "SELECT * FROM PAYMENTS WHERE payment_id = :payment_id";

        $result = $pdo->prepare($sql);
        $result->execute([$payment_id]);
        $result->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $row = $result->fetch();
        return $row;
    }
}