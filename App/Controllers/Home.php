<?php

namespace App\Controllers;

use \Core\View;
use \App\Extra;
use App\Models\Product;
use App\Search;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
{

    public function after()
    {
        Extra::deleteMessageCookie();
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $products = Product::getAllProducts();
        $page = isset($this->route_params['page']) ? $this->route_params['page'] : 1;

        View::renderTemplate('Home/index.html', [
            'products' => $products,
            'page' => $page
        ]);
    }

    /**
     * Show the check page
     *
     * @return void
     */
    public function searchAction()
    {
        $product_key = isset($_GET['q']) ? $_GET['q'] : "";
        $order_by = isset($_GET['orderBy']) ? $_GET['orderBy'] : "latest";
        $category_id = isset($_GET['searchBy']) ? $_GET['searchBy'] : "";
        $rating = isset($_GET['rating']) ? $_GET['rating'] : "";
        $minPrice = isset($_GET['minPrice']) ? $_GET['minPrice'] : 0;
        $maxPrice = isset($_GET['maxPrice']) ? $_GET['maxPrice'] : 200;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        $products = Search::search($product_key, $order_by, $category_id, $rating, $minPrice, $maxPrice);
        $sortsBy = Search::sortsBy();

        View::renderTemplate('Home/search.html', [
            'products' => $products,
            'sortsBy' => $sortsBy,
            'search_key' => $product_key,
            'orderBy' => $order_by,
            'selected_category' => $category_id,
            'selected_rating' => $rating,
            'selected_minPrice' => $minPrice,
            'selected_maxPrice' => $maxPrice,
            'page' => $page
        ]);
    }

    /**
     * Dashboard page
     *
     * @return void
     */
    public function dashboardAction()
    {
        header('Location: http://localhost:8080/apex/f?p=110:LOGIN_DESKTOP::::::');
        exit();
    }
}
