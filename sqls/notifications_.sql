DROP SEQUENCE notification_id_seq;
CREATE SEQUENCE notification_id_seq START WITH 1 INCREMENT BY 1;

DROP TABLE NOTIFICATIONS CASCADE CONSTRAINTS;

CREATE TABLE NOTIFICATIONS (
    notification_id         INTEGER NOT NULL,
	image_link				VARCHAR2(255) NOT NULL,
    title		            VARCHAR2(255) NOT NULL,
    body				    VARCHAR2(1000) NOT NULL,
	sender_text				VARCHAR2(255) NOT NULL,
	main_link				VARCHAR2(255) DEFAULT '#',
	notified_date  			DATE NOT NULL,
	is_seen   				VARCHAR2(1) DEFAULT 'N',
	user_id					INTEGER NOT NULL,

	FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    CONSTRAINT   pk_NOTIFICATIONS PRIMARY KEY (notification_id)
);


CREATE OR REPLACE TRIGGER notification_auto_increment
BEFORE INSERT ON notifications
FOR EACH ROW
BEGIN
	IF :NEW.notification_id IS NULL THEN
		SELECT notification_id_seq.nextval
		INTO :new.notification_id
		FROM dual;
	END IF;
END;
/

