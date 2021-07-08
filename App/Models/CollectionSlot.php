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
     * Collection slot object 
     * 
     * @param int id
     * @return object
     */
    public static function getCollectionSlotById($id)
    {
        $pdo = static::getDB();

        $sql = "SELECT * FROM COLLECTION_SLOTS WHERE COLLECTION_SLOT_ID = :id";

        $result = $pdo->prepare($sql);
        $result->execute([$id]);
        $result->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $row = $result->fetch();
        return $row;
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
        $current_datetime = strtotime("now");
        $collection_day = $this->DAY;
        $collection_datetime = strtotime("$collection_day this week");

        return $current_datetime >= $collection_datetime;
    }

    /**
     * Get date of the collection slot
     * 
     * @return string
     */
    public function getDate()
    {
        date_default_timezone_set('Asia/Kathmandu');
        $day = $this->DAY;
        return date('Y-m-d', strtotime("$day this week"));
    }

    /**
     * Get next week date of the collection slot
     * 
     * @return string
     */
    public function getNextDate()
    {
        date_default_timezone_set('Asia/Kathmandu');
        $day = $this->DAY;
        return date('Y-m-d', strtotime("$day next week"))  ;
    }

    /**
     * Check if time slot is closed or not
     * 
     * @return boolean
     */
    public function isSlotClosed()
    {
        date_default_timezone_set('Asia/Kathmandu');
        
        $collection_day = $this->DAY;
        $collection_datetime = \DateTime::createFromFormat("l", $collection_day);

        $pdo = static::getDB();
        $sql = "select count(*) from orders 
                where collection_date = TO_DATE(:collection_date, 'YYYY-MM-DD HH24:MI:SS') AND collection_slot_id = :collection_slot_id";
        $result = $pdo->prepare($sql);
        $result->execute([
            ':collection_date' => $collection_datetime->format("Y-m-d"),
            ':collection_slot_id' => $this->COLLECTION_SLOT_ID
        ]);

        $rowsCount = $result->fetchColumn(); 

        if($rowsCount >= 20){
            return true;
        }

        $shift = $this->SHIFT;
        $shifts_array = explode(" - ", $shift);

        date_default_timezone_set('Asia/Kathmandu');
        $current_datetime = new DateTime();

        $from = $this->DAY. " ". $shifts_array[0];
        $from_date = \DateTime::createFromFormat("l H:i", $from);

        $tomorrow_datetime = $current_datetime->modify('+1 day');

        return $tomorrow_datetime > $from_date;
    }

    /**
     * Check if time slot is closed or not
     * 
     * @return boolean
     */
    public function isNextSlotClosed()
    {
        date_default_timezone_set('Asia/Kathmandu');
        
        $collection_day = $this->DAY;
        $collection_datetime = \DateTime::createFromFormat("l", $collection_day);

        $tomorrow_datetime = $collection_datetime->modify('+7 day');

        $pdo = static::getDB();
        $sql = "select count(*) from orders 
                where collection_date = TO_DATE(:collection_date, 'YYYY-MM-DD HH24:MI:SS') AND collection_slot_id = :collection_slot_id";
        $result = $pdo->prepare($sql);
        $result->execute([
            ':collection_date' => $tomorrow_datetime->format("Y-m-d"),
            ':collection_slot_id' => $this->COLLECTION_SLOT_ID
        ]);

        $rowsCount = $result->fetchColumn(); 

        if($rowsCount >= 20){
            return true;
        }
        return false;
    }
}

