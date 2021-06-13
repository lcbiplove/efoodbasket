<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{
    /**
     * DB name
     * @var string
     */
    const DB = 'oci:dbname=//localhost:1521/XEPDB1';
    
    /**
     * Database user
     * @var string
     */
    const DB_USER = 'efoodbasket';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'pm..2021';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;
}
