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
DROP SEQUENCE shop_id_seq;
CREATE SEQUENCE shop_id_seq START WITH 1 INCREMENT BY 1;
DROP SEQUENCE product_category_id_seq;
CREATE SEQUENCE product_category_id_seq START WITH 1 INCREMENT BY 1;
DROP SEQUENCE product_id_seq;
CREATE SEQUENCE product_id_seq START WITH 1 INCREMENT BY 1;
DROP SEQUENCE product_image_id_seq;
CREATE SEQUENCE product_image_id_seq START WITH 1 INCREMENT BY 1;

-- Drop tables --
DROP TABLE USERS CASCADE CONSTRAINTS;
DROP TABLE TRADERS CASCADE CONSTRAINTS;
DROP TABLE NOTIFICATIONS CASCADE CONSTRAINTS;
DROP TABLE SHOPS CASCADE CONSTRAINTS;
DROP TABLE PRODUCT_CATEGORIES CASCADE CONSTRAINTS;
DROP TABLE PRODUCTS CASCADE CONSTRAINTS;
DROP TABLE PRODUCT_IMAGES CASCADE CONSTRAINTS;
-- Table Creation --
CREATE TABLE USERS(
	user_id INTEGER NOT NULL,
	email VARCHAR2(128) NOT NULL UNIQUE,
	fullname VARCHAR2(48) NOT NULL,
	password VARCHAR2(64),
	address VARCHAR2(48) NOT NULL,
	user_role VARCHAR2(20) NOT NULL,
	contact NUMBER(10) NOT NULL,
	joined_date DATE NOT NULL,
	otp NUMBER(6),
	token VARCHAR2(48),
	otp_last_date DATE,
	is_verified VARCHAR2(1) DEFAULT 'N',
	CONSTRAINT pk_USERs PRIMARY KEY (user_id)
);
CREATE TABLE TRADERS (
	trader_id INTEGER NOT NULL,
	pan VARCHAR2(12) NOT NULL UNIQUE,
	product_type VARCHAR2(30) NOT NULL UNIQUE,
	product_details VARCHAR2(4000) NOT NULL,
	documents_path VARCHAR2(1024) NOT NULL,
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
CREATE TABLE SHOPS (
	shop_id                 INTEGER NOT NULL,
	shop_name               VARCHAR2(40) NOT NULL,
	address	           		VARCHAR2(40) NOT NULL,
    contact	                NUMBER(10) NOT NULL,
	trader_id	            INTEGER NOT NULL,
	
	FOREIGN KEY (trader_id) REFERENCES Users(user_id),
    CONSTRAINT   pk_SHOPS PRIMARY KEY (shop_id)
);
CREATE TABLE PRODUCT_CATEGORIES (
	category_id	    		INTEGER NOT NULL,
	category_name	        VARCHAR2(50) NOT NULL UNIQUE,
	category_description	VARCHAR2(2000) NOT NULL,
	
    CONSTRAINT	pk_PRODUCT_CATEGORIES PRIMARY KEY (category_id)
);
CREATE TABLE PRODUCTS (
	product_id	        INTEGER NOT NULL,
	product_name	    VARCHAR2(50) NOT NULL,
	price       	    NUMBER(10, 2) NOT NULL,
	quantity	        NUMBER(10) NOT NULL,
	availability 	    VARCHAR2(1) DEFAULT 'Y',
	description	        VARCHAR2(2000) NOT NULL,
	allergy_information VARCHAR2(2000) NOT NULL,
	discount			NUMBER(3,2) DEFAULT 0.00,
    shop_id        		INTEGER NOT NULL,
	category_id			INTEGER NOT NULL,
	added_date			DATE NOT NULL,
    
	FOREIGN KEY (shop_id) REFERENCES Shops(shop_id),
	FOREIGN KEY (category_id) REFERENCES Product_Categories(category_id),
	CONSTRAINT	pk_PRODUCTS PRIMARY KEY (product_id)
);
CREATE TABLE PRODUCT_IMAGES (
    product_image_id        INTEGER NOT NULL,
    image_name              VARCHAR2(255),
	product_id          	INTEGER NOT NULL,

	FOREIGN KEY (product_id) REFERENCES products(product_id),
    CONSTRAINT	pk_PRODUCT_IMAGES PRIMARY KEY (product_image_id)
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
CREATE OR REPLACE TRIGGER shop_auto_increment
BEFORE INSERT ON shops
FOR EACH ROW
BEGIN
	IF :NEW.shop_id IS NULL THEN
		SELECT shop_id_seq.nextval
		INTO :new.shop_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER category_auto_increment
BEFORE INSERT ON product_categories
FOR EACH ROW
BEGIN
	IF :NEW.category_id IS NULL THEN
		SELECT product_category_id_seq.nextval
		INTO :new.category_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER product_auto_increment
BEFORE INSERT ON products
FOR EACH ROW
BEGIN
	IF :NEW.product_id IS NULL THEN
		SELECT product_id_seq.nextval
		INTO :new.product_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER product_image_auto_increment
BEFORE INSERT ON product_images
FOR EACH ROW
BEGIN
	IF :NEW.product_image_id IS NULL THEN
		SELECT product_image_id_seq.nextval
		INTO :new.product_image_id
		FROM dual;
	END IF;
END;
/
