<?php 

define('DB_USERNAME', 'c##efoodbasket');
define('DB_PASSWORD', 'pm..2021');

$connection = oci_connect (DB_USERNAME, DB_PASSWORD, "localhost/XE") or die(oci_error());
