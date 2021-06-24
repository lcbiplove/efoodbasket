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
     * Website name
     * @var string
     */
    const WEBSITE_NAME = 'http://localhost';

    /**
     * DB name
     * @var string
     */
    const DB = 'oci:dbname=//localhost/xe';
    
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

    /**
     * Host name of mail server
     * @var string
     */
    const SMTP_HOST = 'tls://smtp.gmail.com';

    /**
     * Mailing username
     * @var string
     */
    const MAIL_USERNAME   = 'bisheshpanta12@gmail.com';                     
        
    /**
     * Mailing password
     * @var string
     */
    const MAIL_PASSWORD   = '12bisheshpanta'; 
}
