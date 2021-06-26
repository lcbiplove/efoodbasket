<?php

namespace Core;

use PDO;
use App\Config;

/**
 * Base model
 *
 * PHP version 7.0
 */
abstract class Model
{

    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        static $db = null;

        if ($db === null) {
            $db = new PDO(Config::DB, Config::DB_USER, Config::DB_PASSWORD);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }

    /**
     * Get the next id from the sequence to add to database
     * 
     * @param string name of sequence
     * @return array "db" => gives connection,  "next_id" => gives next to be inserted id
     */
    protected static function getNextId($sequence_name)
    {
        $db = static::getDB();
        $statement = $db->prepare("SELECT $sequence_name.NEXTVAL AS nextId FROM DUAL");
        $statement->execute();
        $nextId = $statement->fetchColumn(0);

        return [
            "db" => $db,
            "next_id" => $nextId
        ];
    }
}


