-- Blank space work for sqlplus
SET sqlblanklines ON;
SET SERVEROUTPUT ON;
-- Sequences
DROP SEQUENCE user_id_seq;
CREATE SEQUENCE user_id_seq START WITH 1 INCREMENT BY 1;
DROP SEQUENCE trader_id_seq;
CREATE SEQUENCE trader_id_seq START WITH 1 INCREMENT BY 1;
DROP SEQUENCE notification_id_seq;
CREATE SEQUENCE notification_id_seq START WITH 1 INCREMENT BY 1;

-- Drop tables --
DROP TABLE USERS CASCADE CONSTRAINTS;
DROP TABLE TRADERS CASCADE CONSTRAINTS;
DROP TABLE NOTIFICATIONS CASCADE CONSTRAINTS;
-- Table Creation --
CREATE TABLE USERS(
	user_id INTEGER NOT NULL,
	email VARCHAR2(30) NOT NULL UNIQUE,
	fullname VARCHAR2(25) NOT NULL,
	password VARCHAR2(64),
	address VARCHAR2(20) NOT NULL,
	user_role VARCHAR2(20) NOT NULL,
	contact NUMBER(10) NOT NULL,
	joined_date DATE NOT NULL,
	otp NUMBER(6),
	otp_last_date DATE,
	is_verified VARCHAR2(1) DEFAULT 'N',
	CONSTRAINT pk_USERs PRIMARY KEY (user_id)
);
CREATE TABLE TRADERS (
	trader_id INTEGER NOT NULL,
	pan VARCHAR2(12) NOT NULL UNIQUE,
	product_type VARCHAR2(30) NOT NULL UNIQUE,
	product_details VARCHAR2(255) NOT NULL,
	documents_path VARCHAR2(255) NOT NULL,
	is_approved VARCHAR2(1) DEFAULT 'R',
	approved_date DATE,
	user_id INTEGER UNIQUE,
	FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
	CONSTRAINT pk_Traders PRIMARY KEY (trader_id)
);
CREATE TABLE NOTIFICATIONS (
    notification_id         INTEGER NOT NULL,
    notification            VARCHAR2(128) NOT NULL,
    notification_type       VARCHAR2(30) NOT NULL,
	notified_date DATE NOT NULL,
	is_seen   VARCHAR2(1) DEFAULT 'N',
    user_id   INTEGER NOT NULL,
	sender_id INTEGER,

	FOREIGN KEY (user_id) REFERENCES Users(user_id),
	FOREIGN KEY (sender_id) REFERENCES Users(user_id),
    CONSTRAINT   pk_NOTIFICATIONS PRIMARY KEY (notification_id)
);





-------------- Triggers -----------------------
-- Triggers
CREATE OR REPLACE TRIGGER user_auto_increment BEFORE
INSERT ON users FOR EACH ROW BEGIN
	IF :NEW.user_id IS NULL THEN
		SELECT user_id_seq.nextval INTO :new.user_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER trader_auto_increment BEFORE
INSERT ON traders FOR EACH ROW BEGIN
	IF :NEW.trader_id IS NULL THEN
		SELECT trader_id_seq.nextval INTO :new.trader_id
		FROM dual;
	END IF;
END;
/
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