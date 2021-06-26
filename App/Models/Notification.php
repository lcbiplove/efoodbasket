<?php

namespace App\Models;

use PDO;
use App\Extra;

/**
 * Example notification model
 *
 * PHP version 7.3
 */
class Notification extends \Core\Model
{
    /**
     * Notification is seen
     */
    public const IS_SEEN = 'Y';

    /**
     * Notification is not seen
     */
    public const IS_NOT_SEEN = 'N';

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
     * Save notification into database
     * 
     * @return boolean
     */
    public function save()
    {
        $pdo = static::getDB();

        $sql = "INSERT INTO NOTIFICATIONS (image_link, body, title, sender_text, main_link, notified_date, is_seen, user_id) 
                VALUES (:image_link, :body, :title, :sender_text, :main_link, TO_DATE(:notified_date, 'YYYY-MM-DD HH24:MI:SS'), :is_seen, :user_id)";

        $result = $pdo->prepare($sql);

        $data = [
            ":image_link" => $this->image_link,
            ":body" => $this->body,
            ":title" => $this->title,
            ":sender_text" => $this->sender_text,
            ":main_link" => $this->main_link,
            ":notified_date" => Extra::getCurrentDateTime(),
            ":is_seen" => $this->is_seen,
            ":user_id" => $this->user_id,
        ];

        return $result->execute($data);
    }

    /**
     * Make a notification as read
     * 
     * @return boolean
     */
    public function makeSeen()
    {
        $pdo = static::getDB();

        $sql = "UPDATE NOTIFICATIONS SET is_seen = :is_seen WHERE notification_id = :notification_id";

        $result = $pdo->prepare($sql);

        return $result->execute([
            ':is_seen' => static::IS_SEEN,
            ':notification_id' => $this->NOTIFICATION_ID
        ]);
    }

    /**
     * Return if notification is seen 
     * 
     * @return boolean
     */
    public function isSeen()
    {
        return $this->IS_SEEN == static::IS_SEEN;
    }

    /**
     * Return if notification is not link 
     * 
     * @return boolean
     */
    public function isNotLink()
    {
        return $this->MAIN_LINK != "#";
    }

    /**
     * Get notification from notif id
     * 
     * @param int user_id
     * @return mixed boolean, array
     */
    public static function getNotificationFromId($notification_id)
    {
        $pdo = static::getDB();

        $sql = "SELECT * FROM NOTIFICATIONS WHERE notification_id = :notification_id";

        $result = $pdo->prepare($sql);
        $result->execute([$notification_id]);
        $result->setFetchMode(\PDO::FETCH_CLASS, self::class);
        $row = $result->fetch();
        return $row;
    }

    /**
     * Get all notifications from user id
     * 
     * @param int user_id
     * @return mixed boolean, array
     */
    public static function getNotificationsFromUserId($user_id)
    {
        $pdo = static::getDB();

        $sql = "SELECT NOTIFICATION_ID, IMAGE_LINK, TITLE, BODY, SENDER_TEXT, MAIN_LINK, IS_SEEN, USER_ID,
                to_char(NOTIFIED_DATE, 'YYYY-MM-DD HH24:MI:SS') as NOTIFIED_DATE
                FROM NOTIFICATIONS WHERE user_id = :user_id ORDER BY NOTIFIED_DATE DESC";

        $result = $pdo->prepare($sql);
        $result->execute([$user_id]);

        return $result->fetchAll(\PDO::FETCH_CLASS, self::class);
    }

    /**
     * Get time difference btween now and notified date
     * 
     * @return string
     */
    public function agoDate()
    {
        return Extra::timeAgo($this->NOTIFIED_DATE);
    }

    /**
     * Returns number of unseen notifications of user
     * 
     * @param int user_id
     * @return int count
     */
    public static function getUnseenCounFromUserId($user_id)
    {
        $pdo = static::getDB();

        $sql = "SELECT count(*) FROM NOTIFICATIONS WHERE user_id = :user_id AND is_seen = :is_seen";
        $result = $pdo->prepare($sql);
        $result->execute([
            "user_id" => $user_id,
            "is_seen" => static::IS_NOT_SEEN
        ]);

        $rowsCount = $result->fetchColumn(); 

        return $rowsCount;
    }
}

