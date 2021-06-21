<?php

namespace App\Models;

use PDO;
use App\Extra;

/**
 * Example user model
 *
 * PHP version 7.3
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
        $contact = preg_replace('/[^0-9]/', '', $this->contact);

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
     * Update attributes on table
     * 
     * @param array data key value pare of to be updated data
     * @return boolean
     */
    public function update($data)
    {
        $pdo = static::getDB();

        $user_id = $this->user_id;

        $query = 'UPDATE users SET';
        $values = array();
        foreach ($data as $name => $value) {
            $query .= ' '.$name.' = :'.$name.','; 
            if($name == 'contact'){
                $values[':'.$name] = preg_replace('/[^0-9]/', '', $value);
            } else {
                $values[':'.$name] = filter_var($value, FILTER_SANITIZE_STRING); 
            }
        }
        $query = substr($query, 0, -1).''; 
        $query .= " WHERE user_id = '$user_id'";

        $sth = $pdo->prepare($query);
        return $sth->execute($values);
    }

    /**
     * Delete object from database
     * 
     * @return boolean
     */
    public function delete()
    {
        $pdo = static::getDB();

        $sql = "DELETE FROM users WHERE user_id = :user_id";

        $result = $pdo->prepare($sql);

        return $result->execute([$this->user_id]);
    }


    /**
     * Change password in the table
     * 
     * @param string password
     * @return boolean true if success, false otherwise
     */
    public function changePassword($password)
    {
        $pdo = static::getDB();

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "UPDATE users SET password = :password WHERE user_id = :user_id";

        $result = $pdo->prepare($sql);

        return $result->execute([
            ':user_id' => $this->user_id,
            ':password' => $password_hash
        ]);
    }

    /**
     * Get user object from user id
     * 
     * @return object 
     */
    public static function getUserObjectFromId($id){
        $pdo = static::getDB();

        $sql = "select * from users where user_id = :id";

        $result = $pdo->prepare($sql);
        
        $result->execute([$id]);

        $user_array = $result->fetch(); 

        if($user_array){
            if($user_array['USER_ROLE'] == Trader::ROLE_TRADER){
                return static::getTraderObjectFromEmail($user_array['EMAIL']);
            }
            return new User($user_array);
        }
        return false;
    }

    /**
     * Get user object from email
     * 
     * @return mixed user object if found, false otherwise 
     */
    public static function getUserObjectFromEmail($email)
    {
        $pdo = static::getDB();

        $sql = "select * from users where email = :email";

        $result = $pdo->prepare($sql);
        
        $result->execute([$email]);

        $user_data = $result->fetch(); 

        if($user_data){
            if($user_data['USER_ROLE'] == Trader::ROLE_TRADER){
                return static::getTraderObjectFromEmail($user_data['EMAIL']);
            }
            return new User($user_data);
        }
        return false;
    }

    public static function getTraderObjectFromEmail($email)
    {
        $pdo = static::getDB();

        $sql = "select u.user_id as user_id, email, fullname, address, password, user_role, contact, joined_date, otp, otp_last_date, token, is_verified, pan, product_type, product_details, documents_path, is_approved, approved_date 
        from users u, traders t
        where u.user_id = t.user_id AND u.email = :email";

        $result = $pdo->prepare($sql);
        
        $result->execute([$email]);

        $user_array = $result->fetch(); 

        try {
            return new User($user_array);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Get user object from user id
     * 
     * @return mixed object if user exist, false otherwise 
     */
    public static function getTraderObjectFromId($id)
    {
        $pdo = static::getDB();

        $sql = "select u.user_id as user_id, email, fullname, address, password, user_role, contact, joined_date, otp, otp_last_date, token, is_verified, pan, product_type, product_details, documents_path, is_approved, approved_date 
        from users u, traders t
        where u.user_id = t.user_id AND u.user_id = :id";

        $result = $pdo->prepare($sql);
        
        $result->execute([$id]);

        $user_array = $result->fetch(); 

        try {
            return new User($user_array);
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Returns emails of all admin
     * 
     * @return array emails of admin
     */
    public static function getAllAdminEmails()
    {
        $pdo = static::getDB();

        $user_role = User::ROLE_ADMIN;

        $sql = "select email from users where user_role = :email";

        $result = $pdo->prepare($sql);
        
        $result->execute([$user_role]);

        return $result->fetchAll(PDO::FETCH_COLUMN, 0); 
    }

    /**
     * Check if user can login and make errors based on
     * that
     * 
     * @return boolean
     */
    public function canLogin()
    {
        $mssg = "";
        $type = Extra::COOKIE_MESSAGE_INFO;

        if(!$this->isEmailVerified()){
            $mssg = "Your email is not verified. You need to verify your email to login.<form class='otp-inline-form' method='POST' action='/user/verify-email/'><input type='hidden' name='email' value='{$this->email}' /><input type='hidden' name='resend' value='' /><button class='submit'>VERIFY</button></form>";
            Extra::setMessageCookie($mssg, $type);
            return false;
        }
        if($this->isTraderRequested()){
            $mssg = "Your request of becoming trader is not yet reviewed. We will notify you after we have reviewed.";
            Extra::setMessageCookie($mssg, $type);
            return false;
        }

        return true;
    }

    /**
     * Update traders request status
     * 
     * @param string 'Y', 'N' or 'R'
     * @return boolean
     */
    public function updateTraderApproval($is_approved)
    {
        $pdo = static::getDB();

        $sql = "UPDATE traders SET is_approved = :is_approved, approved_date = TO_DATE(:approved_date, 'YYYY-MM-DD HH24:MI:SS') WHERE user_id = :user_id";

        $result = $pdo->prepare($sql);

        return $result->execute([
            ':user_id' => $this->user_id,
            ':is_approved' => $is_approved,
            ':approved_date' => Extra::getCurrentDateTime()
        ]);
    }

    /**
     * Has trader get notification 
     * 
     * @return boolean
     */
    public function hasTraderGotNotice()
    {
        return $this->password;
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

    /**
     * Returns if trader is accepted
     * 
     * @return boolean
     */
    public function isTraderApproved()
    {
        return $this->is_approved === Trader::REQUEST_STATUS_YES;
    }

    /**
     * Returns if user is admin
     * 
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->user_role === User::ROLE_ADMIN;
    }

    /**
     * Returns if trader is requested
     * 
     * @return boolean
     */
    public function isTraderRequested()
    {
        if($this->isTrader()){
            return $this->is_approved === Trader::REQUEST_STATUS_REQUESTED;
        }
        return false;
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
     * Update token to the users table
     * 
     * @return string token
     */
    public function createUpdateToken()
    {
        $pdo = static::getDB();

        $token = bin2hex(random_bytes(24));

        $sql = "UPDATE users SET token = :token, otp_last_date = TO_DATE(:otp_last_date, 'YYYY-MM-DD HH24:MI:SS') WHERE email = :email";

        $result = $pdo->prepare($sql);

        $result->execute([
            ':email' => $this->email,
            ':token' => $token,
            ':otp_last_date' => Extra::getCurrentDateTime()
        ]);

        return $token;
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
     * Check if token is valid
     * 
     * @return boolean
     */
    public function isTokenValid($token)
    {
        $pdo = static::getDB();

        $sql = "select token, to_char(otp_last_date, 'YYYY-MM-DD HH24:MI:SS') as otp_last_date from users where email = :email";
        $result = $pdo->prepare($sql);
        $result->execute([$this->email]);

        $result = $result->fetchObject();

        if($result->TOKEN !== $token){
            return false;
        }
        $currentTime = strtotime(Extra::getCurrentDateTime());
        $time = strtotime($result->OTP_LAST_DATE);

        $diff_in_seconds = ($currentTime - $time);

        // 7 days
        if($diff_in_seconds > 604800){
            return false;
        }
        return true;
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

    /**
     * Returns readable formatted contact
     * 
     * @return string formated contact
     */
    public function getReadableContact()
    {
        return Extra::getBeautifulPhone($this->contact);
    }
}

