<?php

namespace App\Models;

use App\Extra;
use Core\Model;

class Voucher extends Model
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
     * Get voucher object
     * 
     * @param int voucher_id
     * @return object voucher object
     */
    public static function getVoucherById($voucher_id)
    {
        $pdo = static::getDB();

        $sql = "SELECT * FROM VOUCHERS WHERE voucher_id = :voucher_id";

        $result = $pdo->prepare($sql);
        $result->execute([$voucher_id]);
        $result->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $row = $result->fetch();
        return $row;
    }

    /**
     * Check if code is in table
     * 
     * @param string code
     * @return mixed object, false
     */
    public static function checkCode($code)
    {
        $pdo = static::getDB();
        $sql = "select VOUCHER_ID, CODE, DISCOUNT, to_char(valid_till, 'YYYY-MM-DD HH24:MI:SS') as VALID_TILL
                from vouchers where code = :code";
        $result = $pdo->prepare($sql);
        $result->execute([$code]);
        try {
            $result->setFetchMode(\PDO::FETCH_CLASS, self::class);
            $rows = $result->fetch();
            return $rows;

        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Check if code is expired
     * 
     * @return boolean
     */
    public function isExpired()
    {
        $currentTime = strtotime(Extra::getCurrentDateTime());
        $valid_till = strtotime($this->VALID_TILL);

        return $currentTime - $valid_till > 0;
    }
}

