<?php

namespace App\Models;

use PDO;
use App\Models\User;

/**
 * Trader model
 *
 * PHP version 7.3
 */
class Trader extends User
{
    /**
     * Trader request status, accepted
     * @var string
     */
    public const REQUEST_STATUS_YES = "Y";

    /**
     * Trader request status, requested
     * @var string
     */
    public const REQUEST_STATUS_REQUESTED = "R";

    /**
     * Trader request status, rejected
     * @var string
     */
    public const REQUEST_STATUS_REJECTED = "N";

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
     * Saves the trader object to database
     * 
     * @param array $_FILES superglobal
     * @return boolean 
     */
    public function save($FILES){
        $connection = static::getDB();
        
        $user = new User($this);
        $user_id = $user->save(User::ROLE_TRADER);

        $pan = filter_var($this->pan, FILTER_SANITIZE_STRING);
        $product_type = filter_var($this->type, FILTER_SANITIZE_STRING);
        $product_details = filter_var($this->details, FILTER_SANITIZE_STRING);
        $documents_path = Image::save($FILES, "documents", "documents");

        $sql_query = "INSERT INTO TRADERS (pan, product_type, product_details, documents_path, user_id) VALUES (:pan, :product_type, :product_details, :documents_path, :user_id)";

        $data = [
            ':pan' => $pan,
            ':product_type' => $product_type,
            ':product_details' => $product_details,
            ':documents_path' => $documents_path,
            ':user_id' => $user_id
        ];

        return $connection->prepare($sql_query)->execute($data);
    }
}
