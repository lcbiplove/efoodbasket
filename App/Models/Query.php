<?php

namespace App\Models;

use App\Extra;
use Core\Model;

class Query extends Model
{
    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $lowerKey = strtolower($key);
            $this->$lowerKey = $value;
        };
    }

    /**
     * Saves the shop object to database
     * 
     * @param integer trader id
     * @return mixed query_id, false 
     */
    public function save(){
        $db_next_array = static::getNextId("query_id_seq");
        $pdo = $db_next_array['db'];
        $query_id = $db_next_array['next_id'];

        $question = filter_var($this->question, FILTER_SANITIZE_STRING);
        $question_date = Extra::getCurrentDateTime();
        $product_id = $this->product_id;
        $user_id = $this->user_id;

        $sql_query = "INSERT INTO QUERIES (query_id, question, question_date, product_id, user_id) 
                      VALUES (:query_id, :question, TO_DATE(:question_date, 'YYYY-MM-DD HH24:MI:SS'), :product_id, :user_id)";

        $data = [
            ':query_id' => $query_id,
            ':question' => $question,
            ':question_date' => $question_date,
            ':product_id' => $product_id,
            ':user_id' => $user_id
        ];

        $prepared = $pdo->prepare($sql_query);
        if($prepared->execute($data)){
            return static::getQueryById($query_id);
        }
        return false;
    }

    /**
     * Answer the query
     * 
     * @param string answer text
     * @return mixed object, boolean
     */
    public function answer($answer)
    {
        $pdo = static::getDB();
        $sql_query = "UPDATE QUERIES SET answer = :answer, answer_date = TO_DATE(:answer_date, 'YYYY-MM-DD HH24:MI:SS')
                    WHERE query_id = :query_id";

        $prepared = $pdo->prepare($sql_query);
        $data = [
            ':answer' => $answer,
            ':answer_date' => Extra::getCurrentDateTime(),
            ':query_id' => $this->QUERY_ID
        ];
        if($prepared->execute($data)){
            return static::getQueryById($this->QUERY_ID);
        }
        return false;
    }

    /**
     * Delete query object from table
     * 
     * @return boolean
     */
    public function delete()
    {
        $pdo = static::getDB();

        $sql_query = "DELETE FROM QUERIES WHERE query_id = :query_id";
        $prepared = $pdo->prepare($sql_query);
        return $prepared->execute([$this->QUERY_ID]);
    }

    /**
     * Delete answer object from table
     * 
     * @return boolean
     */
    public function deleteAnswer()
    {
        $pdo = static::getDB();

        $sql_query = "UPDATE QUERIES SET answer = NULL, answer_date = NULL WHERE query_id = :query_id";
        $prepared = $pdo->prepare($sql_query);
        return $prepared->execute([$this->QUERY_ID]);
    }

    /**
     * Return if query is answered or not
     * 
     * @return boolean
     */
    public function isAnswered()
    {
        return $this->ANSWER ? true : false;
    }

    /**
     * Return questionaire name
     * 
     * @return string 
     */
    public function questionBy()
    {
        $pdo = static::getDB();

        $sql_query = "SELECT u.fullname from users u, queries q where u.user_id = q.user_id and u.user_id = :user_id";

        $prepared = $pdo->prepare($sql_query);
        $prepared->execute([$this->USER_ID]);

        $firstName = $prepared->fetchColumn();

        return $firstName;
    }

    /**
     * Return time difference in question
     * 
     * @return string 
     */
    public function agoQuestion()
    {
        return Extra::timeAgo($this->QUESTION_DATE);
    }

    /**
     * Return time difference in answer
     * 
     * @return string 
     */
    public function agoAnswer()
    {
        return Extra::timeAgo($this->ANSWER_DATE);
    }


    /**
     * Returns query object from id
     * 
     * @param int query_id
     * @return mixed obj, boolean
     */
    public static function getQueryById($query_id)
    {
        $pdo = static::getDB();

        $query = "SELECT 
                QUERY_ID, QUESTION, ANSWER, PRODUCT_ID, USER_ID, 
                to_char(QUESTION_DATE, 'YYYY-MM-DD HH24:MI:SS') as QUESTION_DATE,
                to_char(ANSWER_DATE, 'YYYY-MM-DD HH24:MI:SS') as ANSWER_DATE
             FROM QUERIES WHERE query_id = :query_id";

        $result = $pdo->prepare($query);
        $result->execute([$query_id]);
        $result->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $row = $result->fetch();
        return $row;
    }

}