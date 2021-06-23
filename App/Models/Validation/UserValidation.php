<?php

namespace App\Models\Validation;

use App\Models\User;

class UserValidation extends User
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
                $func = "validate". ucfirst($value);
                $this->$func();
            }
            return $this->errors;
        }
        return false;
    }

    /**
     * Returns most basic error data
     * 
     * @return array errors
     */
    public function getBasicErrors()
    {
        $this->validateFullname();
        $this->validateEmail();
        $this->validateAddress();
        $this->validateContact();
        $this->validateTerms();

        return $this->errors;
    }


    /**
     * Returns error of the validated data
     * 
     * @return array errors 
     */
    public function getErrors(){
                
        $this->getBasicErrors();
        $this->validatePassword();

        return $this->errors;
    }
    
    /**
     * Check if username is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    public function validateFullname(){
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
    public function validateEmail(){
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
    public function validateAddress(){
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
    public function validateContact(){
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
    public function validatePassword(){
        $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\w\W]{8,}$/';

        if(!preg_match($passwordPattern, $this->password) || $this->password !== $this->confpass ){
            if(!preg_match($passwordPattern, $this->password)){
                $this->errors['password'] = "Please enter the password having at least one uppercase, one lowercase letter and a number with at least 8 characters";
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
    public function validateTerms(){
        if(isset($this->terms)){
            return true;
        }
        return $this->errors['terms'] = "Please agree with our terms and conditions.";
    }
}