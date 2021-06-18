<?php

namespace App\Models;

use PDO;
use App\Extra;

/**
 * Example notification model
 *
 * PHP version 7.3
 */
class Notification extends \Core\Model
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

}

