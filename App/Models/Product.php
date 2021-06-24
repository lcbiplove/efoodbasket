<?php

namespace App\Models;

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
        $price = filter_var($this->price, FILTER_SANITIZE_NUMBER_FLOAT);
        $quantity = filter_var($this->quantity, FILTER_SANITIZE_NUMBER_INT);
        $availability = isset($this->availability) ? Product::PRODUCT_AVAILABLE : Product::PRODUCT_NOT_AVAILABLE;
        // var_dump($availability);
        // exit();
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