<?php

namespace App\Models\Validation;

use App\Models\User;

class UserValidation extends User
{
    private $errors = [];

    function __construct($data)
    {
        foreach ($data as $key => $value) {
            $lowerKey = strtolower($key);
            $this->$lowerKey = $value;
        };
    }


    /**
     * Returns error of the validated data
     * 
     * @return array errors 
     */
    public function getErrors(){
        $this->validateFullname();
        $this->validateEmail();
        $this->validateAddress();
        $this->validateContact();
        $this->validatePassword();
        $this->validateTerms();

        return $this->errors;
    }
    
    /**
     * Check if username is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    private function validateFullname(){
        $fullNamePattern = '/^[a-zA-Z]+(?:\s[a-zA-Z]+)+$/';

        if(preg_match($fullNamePattern, $this->fullname)){
            return true;
        }
        return $this->errors['fullname'] = "Please enter the valid full name";
    }

    /**
     * Check if email is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    private function validateEmail(){
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $pdo = static::getDB();
            $sql = "select count(*) from users where email = :email";
            $result = $pdo->prepare($sql);
            $result->execute([$this->email]);

            $rowsCount = $result->fetchColumn(); 

            if($rowsCount >= 1){
                return $this->errors['email'] = "Please enter another email, this email already exist.";
            }
            return true;
        }
        return $this->errors['email'] = "Please enter the valid email";
    }

    /**
     * Check if address is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    private function validateAddress(){
        if(strlen($this->address) > 4){
            return true;
        }
        return $this->errors['address'] = "Please enter the valid address";
    }

    /**
     * Check if contact is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    private function validateContact(){
        $numbers = preg_replace('/[^0-9]/', '', $this->contact);

        if (strlen($numbers) == 11) $numbers = preg_replace('/^1/', '',$numbers);

        //if we have 10 digits left, it's probably valid.
        if (strlen($numbers) == 10){
            return true;
        } 
        return $this->errors['contact'] = "Please enter the valid phone number";
    }

    /**
     * Check if password is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    private function validatePassword(){
        $passwordPattern = '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/';

        if(!preg_match($passwordPattern, $this->password) || $this->password !== $this->confpass ){
            if(!preg_match($passwordPattern, $this->password)){
                $this->errors['password'] = "Please enter the password with at least 8 characters with letters and numbers.";
            }
            if($this->password !== $this->confpass){
                $this->errors['confpass'] = "Please retype, your password does not match.";
            }
            return;
        }
        return true;
    }

    /**
     * Check if terms is accepted
     * 
     * @return string Error message if invalid, true otherwise
     */
    private function validateTerms(){
        if(isset($this->terms)){
            return true;
        }
        return $this->errors['terms'] = "Please agree with our terms and conditions.";
    }
}