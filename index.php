<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__FILE__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Session start
 */
session_start();

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);

// Login related routes
$router->add('signup/', ['controller' => 'User', 'action' => 'signup']);
$router->add('login/', ['controller' => 'User', 'action' => 'login']);
$router->add('user/verify-email/', ['controller' => 'User', 'action' => 'verifyNotice']);
$router->add('signup-trader/', ['controller' => 'User', 'action' => 'signupTrader']);

$router->add('{controller}/{action}/');

$url = $_SERVER['QUERY_STRING'];
if (!preg_match('/[&]/', $url) && $url !== "") {
    $len = strlen($url);
    if ($url[$len-1] !== "/") {
        $url = "/". $url . "/";
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }
}

$router->dispatch($_SERVER['QUERY_STRING']);

