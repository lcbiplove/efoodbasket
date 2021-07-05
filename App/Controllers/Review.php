<?php

namespace App\Controllers;

use App\Auth;
use App\Models;
use App\Models\Product;

/**
 * Query controller
 *
 * PHP version 7.3
 */
class Review extends \Core\Controller
{
    /**
     * Before filter to restrict to logged in user
     * 
     * @return void
     */
    protected function before()
    {
        $this->requireLogin();


    }

    /**
     * Add review
     * 
     * @return void
     */
    public function add()
    {
        $product_id = $this->route_params['product_id'];

        $product = Product::getProductObjectById($product_id);

        if(!$product->hasUserOrderedProduct()){
            $this->redirect("/products/$product_id/");
        }

        if(empty($_POST)){
            $this->redirect("/products/$product_id/");
        }

        $data = [];

        $rating = $_POST['rating'];
        $review_text = isset($_POST['review_text']) ? $_POST['review_text'] : "";

        if(strlen($review_text) > 0 && strlen($review_text) < 10) {
            $data['error'] = "Please enter review with more than 10 characters."; 
        } 
        else {
            $review = Models\Review::getReviewByUserOfProduct(Auth::getUserId(), $product_id);

            if($review) {
                $review->update($rating, strlen($review_text) == 0 ? $review->REVIEW_TEXT : $review_text);
                $data['success'] = true;
            } 
            else {
                $reviewObj = new Models\Review([
                    'rating' => $rating,
                    'review_text' => $review_text,
                    'user_id' => Auth::getUserId(),
                    'product_id' => $product_id
                ]);
                $reviewObj->save();
                $data['success'] = true;
            }
        }
        echo json_encode($data);
    }

    /**
     * Delete review 
     * 
     * @return void
     */
    public function deleteAction()
    {
        $product_id = $this->route_params['product_id'];

        $product = Product::getProductObjectById($product_id);

        if(!$product->hasUserOrderedProduct()){
            $this->redirect("/products/$product_id/");
        }
        
        if(empty($_POST)){
            $this->redirect("/products/$product_id/");
        }

        $data = [];
        $review = Models\Review::getReviewByUserOfProduct(Auth::getUserId(), $product_id);
        $review->delete();
        $data['success'] = true;
        json_encode($data);
    }
}
