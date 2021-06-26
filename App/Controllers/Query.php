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

        if(Auth::isTraderAuthenticated()){
            $data['error'] = "Traders cannot query about the product.";
        }

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

                $stripped_ques = substr($inserted_query->QUESTION, 0, 50) . (strlen($inserted_query->QUESTION) > 50 ? "..." : "");

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

    /**
     * Add answer from the trader
     * 
     * @return void
     */
    public function addAnswerAction()
    {
        $product_id = $this->route_params['product_id'];
        $query_id = $this->route_params['query_id'];

        if(empty($_POST)){
            $this->redirect("/products/$product_id/");
        }

        $product = Product::getProductObjectById($product_id);

        $data = [];

        $answer = $_POST['answer'];
        $user_id = Auth::getUserId();

        if($product->shop()->TRADER_ID != $user_id){
            $data['error'] = "You are not authorized to answer the query.";
        }

        if(strlen($answer) < 10){
            $data['error'] = "Your answer must contain at least 10 characters.";
        } else {
            $query = Models\Query::getQueryById($query_id);
            
            $query = $query->answer($answer);
            if($query){
                $data['data'] = $query;
                
                $data['data']->AGO_QUESTION = $query->agoQuestion();
                $data['data']->AGO_ANSWER = $query->agoAnswer();
                $data['data']->QUESTION_BY = $query->questionBy();

                $stripped_ans = substr($query->ANSWER, 0, 50) . (strlen($query->ANSWER) > 50 ? "..." : "");

                $fullname = Auth::getUser()->fullname;
                $notification = new Notification([
                    'title' => "Answer about product",
                    'body' => "Your query about a product is answerd: \"$stripped_ans\"'",
                    'image_link' => "/public/images/efoodbasket-logo.png",
                    'sender_text' => "From {$fullname}",
                    'main_link' => "/products/{$product_id}/",
                    'user_id' => $query->USER_ID,
                    'is_seen' => Notification::IS_NOT_SEEN
                ]);
                $notification->save();
            } else {
                $data['error'] = "Unable to answer the query. Try reloading the page.";
            }
        }
        echo json_encode($data);
    }

    /**
     * Delete query action
     * 
     * @return void
     */
    public function deleteQueryAction()
    {
        $product_id = $this->route_params['product_id'];
        $query_id = $this->route_params['query_id'];

        if(empty($_POST)){
            $this->redirect("/products/$product_id/");
        }

        $query = Models\Query::getQueryById($query_id);

        $data = [];
        if($query->USER_ID != Auth::getUserId()){
            $data['error'] = "You are not authorized to perform this action";
        } else {
            $data = [];

            $query->delete();
            if($query->delete()) {
                $data['success'] = "OK";
            } else {
                $data['error'] = "Unable to delete query. Try reloading the page.";
            }
        }
        
        echo json_encode($data);
    }
}
