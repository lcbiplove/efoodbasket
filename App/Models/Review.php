<?php

namespace App\Models;

use App\Extra;
use Core\Model;

class Review extends Model
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
     * Insert data into reviews table
     * 
     * @return boolean
     */
    public function save()
    {
        $pdo = static::getDB();

        $sql = "INSERT INTO REVIEWS (rating, review_text, review_date, user_id, product_id) 
                VALUES (:rating, :review_text, TO_DATE(:review_date, 'YYYY-MM-DD HH24:MI:SS'), :user_id, :product_id)";

        $data = [
            ':rating' => $this->rating,
            ':review_text' => strlen($this->review_text) == 0 ? null : $this->review_text,
            ':review_date' => Extra::getCurrentDateTime(),
            ':user_id' => $this->user_id,
            ':product_id' => $this->product_id
        ];
        return $pdo->prepare($sql)->execute($data);
    }

    /**
     * Update review
     * 
     * @param string rating
     * @param string review_text
     * @return boolean
     */
    public function update($rating, $review_text)
    {
        $pdo = static::getDB();

        $sql = "UPDATE REVIEWS SET rating = :rating, review_text = :review_text WHERE product_id = :product_id AND user_id = :user_id";

        $data = [
            ':rating' => $rating,
            ':review_text' => strlen($review_text) == 0 ? null : $review_text,
            ':user_id' => $this->USER_ID,
            ':product_id' => $this->PRODUCT_ID
        ];
        return $pdo->prepare($sql)->execute($data);
    }

    /**
     * Delete row from the table
     * 
     * @return boolean
     */
    public function delete()
    {
        $pdo = static::getDB();

        $sql = "DELETE FROM REVIEWS WHERE product_id = :product_id AND user_id = :user_id";

        $data = [
            ':user_id' => $this->USER_ID,
            ':product_id' => $this->PRODUCT_ID
        ];
        return $pdo->prepare($sql)->execute($data);
    }

    /**
     * Get user name of reviewer
     * 
     * @return string
     */
    public function userName()
    {
        $user = User::getUserObjectFromId($this->USER_ID);

        return $user->fullname;
    }

    /**
     * Get review object of user for that product
     * 
     * @param int user_id
     * @param int product_id
     * 
     * @return mixed boolean, object
     */
    public static function getReviewByUserOfProduct($user_id, $product_id)
    {
        $pdo = static::getDB();

        $sql = "SELECT * FROM REVIEWS WHERE user_id = :user_id and product_id = :product_id";

        $result = $pdo->prepare($sql);
        $result->execute([
            'user_id' => $user_id,
            'product_id' => $product_id
        ]);
        $result->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $row = $result->fetch();
        return $row;
    }
}