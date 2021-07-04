<?php

namespace App\Models;

use App\Auth;
use App\Extra;
use Core\Model;

class Product extends Model
{

    /**
     * Product is available
     */
    public const PRODUCT_AVAILABLE ='Y';

    /**
     * Product is not available
     */
    public const PRODUCT_NOT_AVAILABLE = 'N';
    
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
     * Saves the product object to database
     * 
     * @param array $_FILES superglobal
     * @return boolean 
     */
    public function save($FILES){
        $db_next_array = static::getNextId("product_id_seq");
        $pdo = $db_next_array['db'];

        $discount_default = isset($this->discount) ? $this->discount : 0;

        $product_id = $db_next_array['next_id'];
        $product_name = filter_var($this->product_name, FILTER_SANITIZE_STRING);
        $price = filter_var($this->price, FILTER_SANITIZE_STRING);
        $quantity = filter_var($this->quantity, FILTER_SANITIZE_NUMBER_INT);
        $availability = isset($this->availability) ? Product::PRODUCT_AVAILABLE : Product::PRODUCT_NOT_AVAILABLE;
        $description = filter_var($this->description, FILTER_SANITIZE_STRING);
        $allergy_information = filter_var($this->allergy_information, FILTER_SANITIZE_STRING);
        $discount = filter_var($discount_default, FILTER_SANITIZE_STRING);
        $shop_id = filter_var($this->shop_id, FILTER_SANITIZE_NUMBER_INT);
        $category_id = filter_var($this->category_id, FILTER_SANITIZE_NUMBER_INT);
        $added_date = Extra::getCurrentDateTime();

        $images_path = Image::save($FILES, "images", "products");

        $sql_query = "INSERT INTO 
            PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
            VALUES (:product_id, :product_name, :price, :quantity, :availability, :description, :allergy_information, :discount, :shop_id, :category_id, TO_DATE(:added_date, 'YYYY-MM-DD HH24:MI:SS'))";

        $data = [
            ':product_id' => $product_id,
            ':product_name' => $product_name,
            ':price' => $price,
            ':quantity' => $quantity,
            ':availability' => $availability,
            ':description' => $description,
            ':allergy_information' => $allergy_information,
            ':discount' => $discount,
            ':shop_id' => $shop_id,
            ':category_id' => $category_id,
            ':added_date' => $added_date,
        ];

        $pdo->prepare($sql_query)->execute($data);

        $productImage = new ProductImage([
            'product_id' => $product_id,
            'image_names' => $images_path
        ]);
        return $productImage->save();
    }

    /**
     * Update attributes on table
     * 
     * @param array data key value pare of to be updated data
     * @return boolean
     */
    public function update($data)
    {
        $pdo = static::getDB();

        $product_id = isset($this->product_id) ? $this->product_id : $this->PRODUCT_ID;

        $query = 'UPDATE products SET';
        $values = array();
        $data['availability'] = isset($data['availability']) ? Product::PRODUCT_AVAILABLE : Product::PRODUCT_NOT_AVAILABLE;

        foreach ($data as $name => $value) {
            $query .= ' '.$name.' = :'.$name.','; 
            if(in_array($name, ['price', 'quantity', 'discount'])){
                $values[':'.$name] = filter_var($value, FILTER_SANITIZE_STRING);
            } 
            else {
                $values[':'.$name] = filter_var($value, FILTER_SANITIZE_STRING); 
            }
        }
        $query = substr($query, 0, -1).''; 
        $query .= " WHERE product_id = '$product_id'";

        $sth = $pdo->prepare($query);
        return $sth->execute($values);
    }

    /**
     * Delete product row from database
     * 
     * @return boolean
     */
    public function delete()
    {
        $pdo = static::getDB();

        $sql = "DELETE FROM PRODUCTS WHERE product_id = :product_id";

        $result = $pdo->prepare($sql);

        return $result->execute([$this->product_id]);
    }

     /**
     * Get all products from the table
     * 
     * @param int from index
     * @param int upto
     * @return array
     */
    public static function getAllProducts()
    {
        $pdo = static::getDB();

        $sql = "SELECT * FROM products order by added_date DESC";

        $result = $pdo->prepare($sql);
        
        $result->execute();

        return $result->fetchAll(\PDO::FETCH_CLASS, self::class); 
    }

    /**
     * Return single product object from product id
     * 
     * @param int product_id
     * @return mixed object, false
     */
    public static function getProductObjectById($product_id)
    {
        $pdo = static::getDB();

        $sql = "SELECT * FROM products WHERE product_id = :product_id";

        $result = $pdo->prepare($sql);
        
        $result->execute([$product_id]);
        $result->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $row = $result->fetch();
        return $row;
    }

    public static function getProductObjectForFormById($product_id)
    {
        $pdo = static::getDB();

        $sql = "SELECT * FROM products WHERE product_id = :product_id";

        $result = $pdo->prepare($sql);
        
        $result->execute([$product_id]);
        $row = $result->fetch();
        if($row) {
            return new Product($row);
        }
        return false;
    }

    /**
     * Get all products from the table
     * 
     * @param int traderId
     * @param int from index
     * @param int upto
     * @return array
     */
    public static function getAllProductsByTrader($traderId)
    {
        $pdo = static::getDB();

        $sql = "SELECT p.* FROM products p, shops s, users u
                WHERE p.shop_id = s.shop_id AND s.trader_id = u.user_id AND u.user_id = :traderId
                ORDER BY added_date DESC";

        $result = $pdo->prepare($sql);
        
        $result->execute([$traderId]);

        return $result->fetchAll(\PDO::FETCH_CLASS, self::class); 
    }

    /**
     * Get the Product product owner Id from id
     * 
     * @return mixed string if found, false otherwise
     */
    public function getOwnerId()
    {
        $pdo = static::getDB();

        $sql = "SELECT u.user_id FROM users u, products p, shops s
                WHERE p.shop_id = s.shop_id AND s.trader_id = u.user_id AND p.product_id = :product_id";

        $result = $pdo->prepare($sql);
        
        $product_id = isset($this->PRODUCT_ID) ? $this->PRODUCT_ID : $this->product_id;
        $result->execute([$product_id]);

        $row = $result->fetch();

        if($row){
            return $row['USER_ID']; 
        }
        return false;
    }

    /**
     * All product images of the product
     * 
     * @return mixed array, false
     */
    public function getProductImages()
    {
        $pdo = static::getDB();

        $sql = "SELECT image_name FROM product_images WHERE product_id = :product_id";

        $result = $pdo->prepare($sql);
        
        $result->execute([$this->PRODUCT_ID]);

        return $result->fetchAll(\PDO::FETCH_COLUMN, 0); 
    }

    /**
     * All queries of the product
     * 
     * @return array
     */
    public function getQueries()
    {
        $pdo = static::getDB();

        $sql = "SELECT q.QUERY_ID, QUESTION, ANSWER, Q.PRODUCT_ID, Q.USER_ID, 
                to_char(QUESTION_DATE, 'YYYY-MM-DD HH24:MI:SS') as QUESTION_DATE,
                to_char(ANSWER_DATE, 'YYYY-MM-DD HH24:MI:SS') as ANSWER_DATE
                FROM queries q, products p 
                WHERE q.product_id = p.product_id AND p.product_id = :product_id 
                ORDER BY q.answer NULLS LAST, answer_date, question_date DESC";

        $result = $pdo->prepare($sql);
        
        $result->execute([$this->PRODUCT_ID]);

        return $result->fetchAll(\PDO::FETCH_CLASS, 'App\Models\Query');
    }

    /**
     * Get maximum 4 rows of similar products
     * 
     * @return mixed boolean, array
     */
    public function getSimilarProducts()
    {
        $category_id = $this->CATEGORY_ID;
        $product_id = $this->PRODUCT_ID;

        $pdo = static::getDB();

        $sql = "
            SELECT p.* 
            FROM products p, PRODUCT_CATEGORIES pc 
            WHERE p.category_id = pc.category_id AND pc.category_id = :category_id AND p.product_id <> :product_id
            AND ROWNUM <= 4
        ";

        $result = $pdo->prepare($sql);
        
        $result->execute([
            ':category_id' => $category_id,
            ':product_id' => $product_id
        ]);

        return $result->fetchAll(\PDO::FETCH_CLASS, self::class); 
    }

    /**
     * Shop of the product
     * 
     * @return mixed object, boolean
     */
    public function shop()
    {
        $pdo = static::getDB();

        $sql = "SELECT s.* FROM shops s, products p WHERE s.shop_id = p.shop_id AND p.product_id = :product_id";
        $result = $pdo->prepare($sql);

        $result->execute([$this->PRODUCT_ID]);
        return $result->fetchObject();
    }

    /**
     * Check if product is available 
     * 
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->AVAILABILITY == static::PRODUCT_AVAILABLE && $this->QUANTITY > 0;
    }

    /**
     * Get the first thumnail image of product images
     * 
     * @return string
     */
    public function thumbnailUrl()
    {
        $pdo = static::getDB();

        $sql = "SELECT image_name FROM product_images WHERE product_id = :product_id";

        $result = $pdo->prepare($sql);
        
        $result->execute([$this->PRODUCT_ID]);

        $image_row = $result->fetch();

        if($image_row){
            return "/media/products/" . $image_row['IMAGE_NAME']; 
        }
        return false;
    }

    /**
     * If product is also in user's wishlist
     * 
     * @return boolean
     */
    public function isInWishList()
    {
        $pdo = static::getDB();
        $sql = "select count(*) from wishlists w, products p where w.product_id = p.product_id and w.user_id = :user_id and p.product_id = :product_id";
        $result = $pdo->prepare($sql);
        $result->execute([
            'user_id' => Auth::getUserId(),
            'product_id' => $this->PRODUCT_ID
        ]);

        $rowsCount = $result->fetchColumn(); 

        if($rowsCount >= 1){
            return true;
        }
        return false;
    }

    /**
     * Returns trader name of the product
     * 
     * @return string
     */
    public function traderName()
    {
        $owner = User::getUserObjectFromId($this->getOwnerId());
        return $owner->fullname;
    }
}

class ProductImage extends Model
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
     * Save image data in table
     * 
     * @return boolean
     */
    public function save()
    {
        $pdo = static::getDB();

        $images = explode("|" ,$this->image_names);
        $product_id = $this->product_id;

        $query = "INSERT ALL "; 
        $qPart = array_fill(0, count($images), "INTO product_images (image_name, product_id) VALUES (?, ?)");
        $query .=  implode(" ",$qPart);
        $query .= " SELECT * FROM dual";
        $stmt = $pdo->prepare($query); 
        $i = 1;
        foreach($images as $item) { 
            $stmt->bindValue($i++, $item);
            $stmt->bindValue($i++, $product_id);
        }

        return $stmt->execute();
    }
}