<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
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
            $this->$key = $value;
        };
    }
}

class UserValidation 
{
    private $errors = [];

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
            $this->$key = $value;
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

        if(preg_match($fullNamePattern, $this->fullName)){
            return true;
        }
        return $this->errors['fullName'] = "Please enter the valid full name";
    }

    /**
     * Check if email is valid or not
     * 
     * @return string Error message if invalid, true otherwise
     */
    private function validateEmail(){
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
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

        if(!preg_match($passwordPattern, $this->password) || $this->password !== $this->confPass ){
            if(!preg_match($passwordPattern, $this->password)){
                $this->errors['password'] = "Please enter the password with at least 8 characters with letters and numbers.";
            }
            if($this->password !== $this->confPass){
                $this->errors['confPass'] = "Please retype, your password does not match.";
            }
            return;
        }
        return true;
    }

    private function validateTerms(){
        if(isset($this->terms)){
            return true;
        }
        return $this->errors['terms'] = "Please agree with our terms and conditions.";
    }
}