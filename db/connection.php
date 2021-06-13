<?php 

define('DB_USERNAME', 'efoodbasket');
define('DB_PASSWORD', 'pm..2021');

$connection = oci_connect (DB_USERNAME, DB_PASSWORD, "//localhost:1521/XEPDB1") or die(oci_error());
