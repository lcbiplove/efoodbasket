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
            $lowerKey = strtolower($key);
            $this->$lowerKey = $value;
        };
    }

    /**
     * Save / Insert data into user table
     * 
     * @return boolean true if success, false otherwise
     */
    public function save($role){
        $connection = static::getDB();

        $fullname = filter_var($this->fullname, FILTER_SANITIZE_STRING);
        $email = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        $address = filter_var($this->address, FILTER_SANITIZE_STRING);
        $contact = filter_var($this->contact, FILTER_SANITIZE_STRING);

        $password = password_hash($this->password, PASSWORD_BCRYPT);
        $user_role = $role;

        $joined_date = $this->getCurrentDateTime();

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

    /**
     * Get user object from email
     * 
     * @return object 
     */
    public static function getUserObjectFromEmail($email)
    {
        $user_data = static::getUserArrayFromEmail($email);

        return new User($user_data);
    }

    /**
     * Get user array from email
     * 
     * @return object user except password
     */
    public static function getUserArrayFromEmail($email)
    {
        $pdo = static::getDB();

        $sql = "select user_id, email, fullname, address, user_role, contact, joined_date, otp, otp_last_date, is_verified from users where email = :email";

        $result = $pdo->prepare($sql);
        
        $result->execute([$email]);

        return $result->fetch(); 
    }


    /**
     * Returns if email is verified or not
     * 
     * @return boolean
     */
    public function isEmailVerified()
    {
        return $this->is_verified === "Y";
    }


    /**
     * Returns current datetime of London
     * 
     * @return string datetime
     */
    public function getCurrentDateTime()
    {
        date_default_timezone_set('Europe/London');
        $current = date('Y-m-d H:i:s',time());
        return $current;
    }


    /**
     * Update the new otp code to the table
     * 
     * @param int otp code
     * @return boolean true if success, false otherwise
     */
    public function updateOtpCode($otp)
    {
        $pdo = static::getDB();

        $sql = "UPDATE users SET otp = :otp, otp_last_date = TO_DATE(:otp_last_date, 'YYYY-MM-DD HH24:MI:SS') WHERE email = :email";

        $result = $pdo->prepare($sql);

        return $result->execute([
            ':email' => $this->email,
            ':otp' => $otp,
            ':otp_last_date' => $this->getCurrentDateTime()
        ]);
    }

    /**
     * Checks if otp time is expired 
     * 
     * @return mixed code integer if otp not expire, false otherwise
     */
    public function isOtpValid()
    {
        $pdo = static::getDB();

        $sql = "select otp, to_char(otp_last_date, 'YYYY-MM-DD HH24:MI:SS') as otp_last_date from users where email = :email";
        $result = $pdo->prepare($sql);
        $result->execute([$this->email]);

        $result = $result->fetchObject();

        $currentTime = strtotime($this->getCurrentDateTime());
        $time = strtotime($result->OTP_LAST_DATE);

        $diff_in_seconds = ($currentTime - $time);

        if($diff_in_seconds > 300){
            return false;
        }
        return $result->OTP;
    }

    /**
     * Returns otp code from database or random
     * 
     * @return int otp code
     */
    public function getValidOtp()
    {
        $otp = $this->isOtpValid();
        if(!$otp){
            $otp = random_int(100000, 900000);
            $this->updateOtpCode($otp);
        }
        return $otp;
    }
}

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

        if(!preg_match($passwordPattern, $this->password) || $this->password !== $this->confpass ){
            if(!preg_match($passwordPattern, $this->password)){
                $this->errors['password'] = "Please enter the password with at least 8 characters with letters and numbers.";
            }
            if($this->password !== $this->confpass){
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