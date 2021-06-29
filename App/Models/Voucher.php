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

