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
$router->add('signup-trader/', ['controller' => 'User', 'action' => 'signupTrader']);
$router->add('login/', ['controller' => 'User', 'action' => 'login']);
$router->add('logout/', ['controller' => 'User', 'action' => 'logout']);


$router->add('user/reset-password/{id:\d+}/{token:[a-z0-9A-Z]+}/', ['controller' => 'User', 'action' => 'resetPassword']);
$router->add('user/verify-email/', ['controller' => 'User', 'action' => 'verifyNotice']);
$router->add('user/forgot-password/', ['controller' => 'User', 'action' => 'forgotPassword']);

$router->add('admin/trader-requests/{id:\d+}/', ['controller' => 'Admin', 'action' => 'traderRequest']);

$router->add('trader/add-shop/', ['controller' => 'Shop', 'action' => 'addShop']);
$router->add('trader/shops/{id:\d+}/edit/', ['controller' => 'Shop', 'action' => 'editShop']);
$router->add('trader/shops/{id:\d+}/delete/', ['controller' => 'Shop', 'action' => 'deleteShop']);
$router->add('trader/ajax/add-shop/', ['controller' => 'Shop', 'action' => 'ajaxAddShop']);

$router->add('trader/shops/', ['controller' => 'Trader', 'action' => 'shops']);
$router->add('trader/{id:\d+}/shops/{shop_id:\d+}/', ['controller' => 'Trader', 'action' => 'eachShop']);
$router->add('trader/{id:\d+}/shops/', ['controller' => 'Trader', 'action' => 'shopsByTrader']);

$router->add("manage-account/", ['controller' => 'LoggedUser', 'action' => 'manageAccount']);
$router->add("change-password/", ['controller' => 'LoggedUser', 'action' => 'changePassword']);

$router->add('trader/add-product/', ['controller' => 'Product', 'action' => 'addProduct']);
$router->add("products/{product_id:\d+}/", ['controller' => 'Product', 'action' => 'product']);
$router->add("trader/products/{product_id:\d+}/edit/", ['controller' => 'Product', 'action' => 'editProduct']);
$router->add("trader/products/{product_id:\d+}/delete/", ['controller' => 'Product', 'action' => 'deleteProduct']);

$router->add("trader/manage-products/", ['controller' => 'Product', 'action' => 'manageProducts']);


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

