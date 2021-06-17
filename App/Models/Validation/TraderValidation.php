<?php

namespace App\Models\Validation;

use App\Models\Trader;
use App\Models\Validation\UserValidation;
use App\Models\Image;

class TraderValidation extends Trader
{
    private $errors = [];

    function __construct($data)
    {
        foreach ($data as $key => $value) {
            $lowerKey = strtolower($key);
            $this->$lowerKey = $value;
        };

        // INSERT INTO traders (pan, product_type, product_details, documents_path, requested_date, user_id) VALUES ('AWSPS1234Z', 'Food', 'jsklfsjfs', 'sjflkds', CURRENT_DATE, 1);
    }

    /**
     * Returns error of the validated data
     * 
     * @return array errors 
     */
    public function getErrors($FILES){
        // product details
        // documents path
        $user_validation = new UserValidation($this);
        $this->errors = $user_validation->getBasicErrors();
        $this->validatePAN();
        $this->validateProductType();
        $this->validateDetails();
        $this->validateDocuments($FILES);

        return $this->errors;
    }

    /**
     * Validates pan number
     * 
     * https://stackoverflow.com/a/30476146/10860596
     * https://stackoverflow.com/a/17684637/10860596
     * @return void
     */
    public function validatePAN()
    {
        $pan_pattern = '/[A-Z]{5}[0-9]{4}[A-Z]{1}/';
        if(preg_match($pan_pattern, $this->pan)){
            $pdo = static::getDB();
            $sql = "select count(*) from traders where pan = :pan";
            $result = $pdo->prepare($sql);
            $result->execute([$this->pan]);

            $rowsCount = $result->fetchColumn(); 

            if($rowsCount >= 1){
                return $this->errors['pan'] = "Please enter another PAN number, this one already exist.";
            }

            return true;
        }
        return $this->errors['pan'] = "Please enter the valid PAN number";
    }


     /**
     * Validates Product Type
     * 
     * @return void
     */
    public function validateProductType()
    {
        if(strlen($this->type) >= 2){
            $pdo = static::getDB();
            $sql = "select count(*) from traders where lower(product_type) = :type";
            $result = $pdo->prepare($sql);
            $result->execute([strtolower($this->type)]);

            $rowsCount = $result->fetchColumn(); 

            if($rowsCount >= 1){
                return $this->errors['type'] = "Please choose another product, this one already exist.";
            }
            return true;
        }
        return $this->errors['type'] = "Please enter the valid product type";
    }

    public function validateDetails()
    {
        if(strlen($this->details) > 100){
            return true;
        }
        return $this->errors['details'] = "Please explain about your product choice with more than 100 characters at least.";
    }

    public function validateDocuments($FILES)
    {
        $valid_document_or_error = Image::validateImage($FILES, "documents", 3);
        if($valid_document_or_error === true){
            return true;
        }
        return $this->errors['documents'] = $valid_document_or_error;
    }
}