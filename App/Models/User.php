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

    /**
     * Save / Insert data into user table
     * 
     * @return boolean true if success, false otherwise
     */
    public function save($role){
        $connection = static::getDB();

        $fullname = filter_var($this->fullName, FILTER_SANITIZE_STRING);
        $email = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        $address = filter_var($this->address, FILTER_SANITIZE_STRING);
        $contact = filter_var($this->contact, FILTER_SANITIZE_STRING);

        $password = password_hash($this->password, PASSWORD_BCRYPT);
        $user_role = $role;

        date_default_timezone_set('Europe/London');
        $joined_date = date('y-m-d h:i:s');

        $sql_query = "INSERT INTO USERS (email, fullname, address, password, contact, user_role, joined_date) VALUES (:email, :fullname, :address, :password, :contact, :user_role, TO_DATE(:joined_date, 'YYYY-MM-DD HH24:MI:SS'))";

        $data = [
            ':email' => $email,
            ':fullname' => $fullname,
            ':address' => $address,
            ':password' => $password,
            ':contact' => $contact,
            ':user_role' => $user_role,
            ':joined_date' => $joined_date
        ];

        return $connection->prepare($sql_query)->execute($data);
    }

    // public function login($username, $password) {
    //     $passwordHash = md5($password);
    //     $query = "select * FROM registered_users WHERE user_name = ? AND password = ?";
    //     $paramType = "ss";
    //     $paramArray = array($username, $passwordHash);
    //     $memberResult = $this->ds->select($query, $paramType, $paramArray);
    //     if(!empty($memberResult)) {
    //         $_SESSION["userId"] = $memberResult[0]["id"];
    //         return true;
    //     }
    // }
}

class UserValidation extends User
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