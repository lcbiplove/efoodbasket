<?php

namespace App\Models\Validation;

use App\Auth;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Shop;
use App\Models\Image;

class ProductValidation extends Product
{
    private $errors = [];

    function __construct($data, $validateByName = false)
    {
        foreach ($data as $key => $value) {
            $lowerKey = strtolower($key);
            $this->$lowerKey = $value;
        };
        $this->validateByName = $validateByName;
    }

    /**
     * Returns errors of only specified attributes
     * 
     * @return mixed array if errors, false if namedErrors is not set true
     */
    public function getNamedErrors()
    {
        if($this->validateByName){
            foreach ($this->validateByName as $value) {
                if($value == "availability") continue;

                $str = str_replace('_', '', ucwords($value, '-'));

                $func = "validate". $str;
                $this->$func();
            }
            return $this->errors;
        }
        return false;
    }

    /**
     * Returns error of the validated data
     * 
     * @return array errors 
     */
    public function getErrors($FILES){
        $this->validateProductName();
        $this->validatePrice();
        $this->validateQuantity();
        $this->validateDiscount();
        $this->validateDescription();
        $this->validateAllergyInformation();
        $this->validateShopId();
        $this->validateCategoryId();
        $this->validateImages($FILES);

        return $this->errors;
    }

    /**
     * Check if product name is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    public function validateProductName(){
        if(strlen($this->product_name) > 2){
            return true;
        }
        return $this->errors['product_name'] = "Please enter a valid product name.";
    }

    /**
     * Check if price is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    public function validatePrice(){
        $filter_options = array( 
            'options' => array( 'min_range' => 0) 
        );
        if(filter_var($this->price, FILTER_VALIDATE_FLOAT, $filter_options ) !== FALSE) {
           return true;
        }
        return $this->errors['price'] = "Please enter a valid price.";
    }

    /**
     * Check if quantity is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    public function validateQuantity(){
        if (is_numeric( $this->quantity ) && strpos( $this->quantity, '.' ) === false ){
            return true;
        }
        return $this->errors['quantity'] = "Please enter a valid quantity.";
    }

    /**
     * Check if quantity is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    public function validateDiscount(){
        $filter_options = array( 
            'options' => array('min_range' => 0, 'max_range' => 100) 
        );
        if(filter_var($this->discount, FILTER_VALIDATE_INT, $filter_options ) !== FALSE) {
           return true;
        }
        return $this->errors['discount'] = "Please enter a valid discount.";
    }

    /**
     * Check if description is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    public function validateDescription(){
        if(strlen($this->description) > 100){
            return true;
        }
        return $this->errors['description'] = "Please enter description with at least 100 characters.";
    }

     /**
     * Check if allergy information is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    public function validateAllergyInformation(){
        if(strlen($this->allergy_information) > 50){
            return true;
        }
        return $this->errors['allergy_information'] = "Please enter allergy information with at least 50 characters.";
    }

    /**
     * Check if the shop id entered by user is owned by him
     * 
     * @return string Error message if invalid, true otherwise
     */
    public function validateShopId()
    {
        if(strlen($this->shop_id) > 0){
            $trader_id = Auth::getUserId();
            $trader_shops = Shop::getTraderShops($trader_id);
            $shop_id_owned_by_trader = [];
            foreach ($trader_shops as $value) {
                array_push($shop_id_owned_by_trader, $value['SHOP_ID']);
            }
            if(!in_array($this->shop_id, $shop_id_owned_by_trader)){
                return $this->errors['shop_id'] = "You are not authorized to use this shop or shop does not exist.";
            }
            return true;
        }
        return $this->errors['shop_id'] = "Please select a shop name.";
    }

    /**
     * Check if category exists 
     * 
     * @return mixed error message if error, true otherwise
     */
    public function validateCategoryId()
    {
        if(strlen($this->category_id) > 0){
            if(ProductCategory::getProductCategoryById($this->category_id)){
                return true;
            }
            return $this->errors['category_id'] = "Please select category from the select option only.";
        }
        return $this->errors['category_id'] = "Please select a product category.";
    }

    /**
     * Check for the document files
     * 
     * @return void
     */
    public function validateImages($FILES)
    {
        $valid_image_or_error = Image::validateImage($FILES, "images", 6);
        if($valid_image_or_error === true){
            return true;
        }
        return $this->errors['images'] = $valid_image_or_error;
    }
}