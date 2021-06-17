<?php

namespace App\Models;

use PDO;
use App\Extra;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{
    /**
     * Admin role type
     * @var string
     */
    public const ROLE_ADMIN = 'ADMIN';

    /**
     * Trader role type
     * @var string
     */
    public const ROLE_TRADER = 'TRADER';

    /**
     * Customer role type
     * @var string
     */
    public const ROLE_CUSTOMER = 'CUSTOMER';

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
     * @return mixed row id if success, false otherwise
     */
    public function save($role){
        $db_next_array = static::getNextId("user_id_seq");
        $connection = $db_next_array['db'];

        $user_id = $db_next_array['next_id'];

        $fullname = filter_var($this->fullname, FILTER_SANITIZE_STRING);
        $email = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        $address = filter_var($this->address, FILTER_SANITIZE_STRING);
        $contact = filter_var($this->contact, FILTER_SANITIZE_STRING);

        $password = isset($this->password) ? password_hash($this->password, PASSWORD_BCRYPT) : "";
        $user_role = $role;

        $joined_date = Extra::getCurrentDateTime();

        $sql_query = "INSERT INTO USERS (user_id, email, fullname, address, password, contact, user_role, joined_date) VALUES (:user_id, :email, :fullname, :address, :password, :contact, :user_role, TO_DATE(:joined_date, 'YYYY-MM-DD HH24:MI:SS'))";

        $prepared = $connection->prepare($sql_query);
        $data = [
            ':user_id' => $user_id,
            ':email' => $email,
            ':fullname' => $fullname,
            ':address' => $address,
            ':password' => $password,
            ':contact' => $contact,
            ':user_role' => $user_role,
            ':joined_date' => $joined_date
        ];

        if($prepared->execute($data)){
            return $user_id;
        }
        return false;
    }

    /**
     * Get user object from user id
     * 
     * @return object 
     */
    public static function getUserObjectFromId($id){
        $pdo = static::getDB();

        $sql = "select user_id, email, fullname, address, user_role, contact, joined_date, otp, otp_last_date, is_verified from users where user_id = :id";

        $result = $pdo->prepare($sql);
        
        $result->execute([$id]);

        $user_array = $result->fetch(); 

        return new User($user_array);
    }

    /**
     * Get user object from email
     * 
     * @return object 
     */
    public static function getUserObjectFromEmail($email)
    {
        $user_data = static::getUserArrayFromEmail($email);

        if($user_data){
            return new User($user_data);
        }
        return false;
    }

    /**
     * Get user array from email
     * 
     * @return object user except password
     */
    public static function getUserArrayFromEmail($email)
    {
        $pdo = static::getDB();

        $sql = "select * from users where email = :email";

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
     * Returns if user is trader
     * 
     * @return boolean
     */
    public function isTrader()
    {
        return $this->user_role === User::ROLE_TRADER;
    }

    public function canLogin()
    {
        $mssg = "";
        $type = Extra::COOKIE_MESSAGE_INFO;

        if(!$this->isEmailVerified()){
            $mssg = "Your email is not verified. You need to verify your email to login.<form class='otp-inline-form' method='POST' action='/user/verify-email/'><input type='hidden' name='resend' value='' /><button class='submit'>VERIFY</button></form>";
            Extra::setMessageCookie($mssg, $type);
            return false;
        }
        // if($this->isTrader() && $this->trader->isRequested()){
        //     $mssg = "Your request of becoming trader is not yet reviewed. We will notify you after we have reviewed.";
        //     Extra::setMessageCookie($mssg, $type);
        //     return false;
        // }
        // if($this->isTrader() && $this->trader->isRejected()){
        //     $mssg = "Your request of becoming trader is not accepted. Try signing up again with new detail.";
        //     Extra::setMessageCookie($mssg, Extra::COOKIE_MESSAGE_FAIL);
        //     return false;
        // }

        return true;
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
            ':otp_last_date' => Extra::getCurrentDateTime()
        ]);
    }

    /**
     * Verify the email to 'Y'
     * 
     * @param int code
     * @return boolean
     */
    public function verifyEmail($code)
    {
        if($code === $this->isOtpValid()){
            $pdo = static::getDB();

            $sql = "UPDATE users SET is_verified = :is_verified WHERE email = :email";

            $result = $pdo->prepare($sql);

            return $result->execute([
                ':email' => $this->email,
                ':is_verified' => 'Y',
            ]);
        }
        return false;
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

        $currentTime = strtotime(Extra::getCurrentDateTime());
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

