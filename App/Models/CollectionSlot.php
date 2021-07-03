<?php

namespace App\Models;

use App\Extra;
use Core\Model;
use DateTime;

class CollectionSlot extends Model
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
            $this->$key = $value;
        };
    }

    /**
     * Get all slot time from day
     * 
     * @return array
     */
    public function slots()
    {
        $pdo = static::getDB();
        $sql_query = "SELECT * FROM COLLECTION_SLOTS WHERE DAY = :day";

        $result = $pdo->prepare($sql_query);
        $result->execute([$this->DAY]);
        return $result->fetchAll(\PDO::FETCH_CLASS, self::class);
    }
    

    /**
     * Get all collection Days
     * 
     * @return array days
     */
    public static function getCollectionDays()
    {
        $pdo = static::getDB();
        $sql_query = "SELECT DAY
                    FROM COLLECTION_SLOTS
                    GROUP BY DAY
                    HAVING COUNT(DAY) > 1
                    ORDER BY DECODE(DAY, 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY') DESC";
        $result = $pdo->prepare($sql_query);

        $result->execute();
        return $result->fetchAll(\PDO::FETCH_CLASS, self::class);
    }

    /**
     * Get array of all collection slots
     * 
     * @return array of collection slot object
     */
    public static function getCollectionSlots() 
    {
        $pdo = static::getDB();
        $sql_query = "SELECT * FROM COLLECTION_SLOTS";
        $result = $pdo->prepare($sql_query);

        $result->execute();
        return $result->fetchAll(\PDO::FETCH_CLASS, self::class);
    }

    /**
     * Chech if day is available
     * 
     * @return boolean
     */
    public function isDayClosed()
    {
        date_default_timezone_set('Asia/Kathmandu');
        $current_datetime = new DateTime();
        $current_day = strtoupper($current_datetime->format('l'));
        
        $collection_day = $this->DAY;
        $collection_datetime = \DateTime::createFromFormat("l", $collection_day);

        return $current_datetime <= $collection_datetime || $current_day == $collection_day;
    }

    /**
     * Check if time slot is closed or not
     * 
     * @return boolean
     */
    public function isSlotClosed()
    {
        $shift = $this->SHIFT;
        $shifts_array = explode(" - ", $shift);

        date_default_timezone_set('Asia/Kathmandu');
        $current_datetime = new DateTime();

        $from = $this->DAY. " ". $shifts_array[0];
        $from_date = \DateTime::createFromFormat("l H:i", $from);

        $tomorrow_datetime = $current_datetime->modify('+1 day');

        return $tomorrow_datetime > $from_date;
    }
}

