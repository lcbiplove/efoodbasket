<?php

namespace App\Models\Validation;

use App\Models\Shop;

class ShopValidation extends Shop
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
    public function getErrors(){
        $this->validateShopName();
        $this->validateAddress();
        $this->validateContact();

        return $this->errors;
    }
    
    /**
     * Check if username is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    public function validateShopName(){
        $namePattern = '/^[a-zA-Z]+(?:\s[a-zA-Z]+)+$/';

        if(preg_match($namePattern, $this->shop_name)){
            if($this->validateByName){
                return true;
            }

            $pdo = static::getDB();
            $sql = "select count(*) from shops where lower(shop_name) = :shop_name AND trader_id = :trader_id";
            $result = $pdo->prepare($sql);
            $result->execute([
                ':shop_name' => strtolower($this->shop_name),
                ':trader_id' => $this->trader_id
            ]);

            $rowsCount = $result->fetchColumn(); 

            if($rowsCount >= 1){
                return $this->errors['shop_name'] = "Please choose another shop name, this one already exist.";
            }
            return true;
        }
        return $this->errors['shop_name'] = "Please enter the valid shop name";
    }

    /**
     * Check if address is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    public function validateAddress(){
        if(strlen($this->address) > 4){
            return true;
        }
        return $this->errors['address'] = "Please enter the valid shop address";
    }

    /**
     * Check if contact is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    public function validateContact(){
        $numbers = preg_replace('/[^0-9]/', '', $this->contact);

        if (strlen($numbers) == 11) $numbers = preg_replace('/^1/', '',$numbers);

        //if we have 10 digits left, it's probably valid.
        if (strlen($numbers) == 10){
            return true;
        } 
        return $this->errors['contact'] = "Please enter the valid phone number";
    }
}