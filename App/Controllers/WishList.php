<?php

namespace App\Controllers;

use App\Auth;
use App\Models;
use App\Models\Notification;
use App\Models\Product;
use Core\View;

/**
 * Query controller
 *
 * PHP version 7.3
 */
class WishList extends \Core\Controller
{
    /**
     * Before filter to restrict to logged in user
     * 
     * @return void
     */
    protected function before()
    {
        $this->requireLogin();

        if(Auth::isTraderAuthenticated()){
            $this->redirect("/");
        }
    }

    /**
     * Show all wishlists
     * 
     * @return void
     */
    public function indexAction()
    {
        $rating = isset($_GET['rating']) ? $_GET['rating'] : "";
        $minPrice = isset($_GET['minPrice']) ? $_GET['minPrice'] : 0;
        $maxPrice = isset($_GET['maxPrice']) ? $_GET['maxPrice'] : 200;

        $products = Models\WishList::search(Auth::getUserId(), $rating, $minPrice, $maxPrice);
        View::renderTemplate("WishList/wishlists.html", [
            'products' => $products,
            'selected_rating' => $rating,
            'selected_minPrice' => $minPrice,
            'selected_maxPrice' => $maxPrice
        ]);
    }

    /**
     * Add wishlist
     * 
     * @return void
     */
    public function addAction()
    {
        $data = [];
        $product_id = $_POST['product_id'];
        $user_id = Auth::getUserId();

        $wishlist = new Models\WishList([
            "user_id" => $user_id,
            "product_id" => $product_id,
        ]);
        if($wishlist->save()){
            $data['success'] = "OK";
        } 
        else {
            $data['error'] = "Unable to add to wishlist. Try reloading page.";
        }
        echo json_encode($data);
    }

    /**
     * Delete wishlist
     * 
     * @return void
     */
    public function deleteAction()
    {
        $data = [];
        $product_id = $_POST['product_id'];
        $user_id = Auth::getUserId();

        $wishlist = new Models\WishList([
            "user_id" => $user_id,
            "product_id" => $product_id,
        ]);
        if($wishlist->delete()){
            $data['success'] = "OK";
        } 
        else {
            $data['error'] = "Unable to delete to wishlist. Try reloading page.";
        }

        echo json_encode($data);
    }

}
