<?php

namespace App\Controllers;

use App\Auth;
use App\Models;
use App\Models\Notification;
use App\Models\Product;

/**
 * Query controller
 *
 * PHP version 7.3
 */
class Query extends \Core\Controller
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
     * Add query from the customer
     * 
     * @return void
     */
    public function addQueryAction()
    {
        $product_id = $this->route_params['product_id'];

        if(empty($_POST)){
            $this->redirect("/products/$product_id/");
        }

        $data = [];

        $question = $_POST['question'];
        $user_id = Auth::getUserId();

        if(strlen($question) < 10){
            $data['error'] = "Your question must contain at least 10 characters.";
        } else {
            $query = new Models\Query([
                "question" => $question,
                "product_id" => $product_id,
                "user_id" => $user_id
            ]);
            $inserted_query = $query->save();

            if(!$inserted_query) {
                $data['error'] = "Unable to add query. Reload the page.";
            } else {
                $data['data'] = $inserted_query;
                
                $data['data']->AGO_QUESTION = $inserted_query->agoQuestion();
                $data['data']->AGO_ANSWER = $inserted_query->agoAnswer();
                $data['data']->QUESTION_BY = $inserted_query->questionBy();

                $stripped_ques = substr($inserted_query->QUESTION, 0, 50) . strlen($inserted_query->QUESTION > 50) ? "..." : "";

                $notification = new Notification([
                    'title' => "Query about product",
                    'body' => "You have recieved a query on your product: \"$stripped_ques\"'",
                    'image_link' => "/public/images/efoodbasket-logo.png",
                    'sender_text' => "From {$inserted_query->QUESTION_BY}",
                    'main_link' => "/products/{$product_id}/",
                    'user_id' => Product::getProductObjectById($product_id)->shop()->TRADER_ID,
                    'is_seen' => Notification::IS_NOT_SEEN
                ]);
                $notification->save();
            }
        }
        echo json_encode($data);
    }
}
