-- Blank space work for sqlplus
SET sqlblanklines ON;
SET SERVEROUTPUT ON;
-- Sequences
DROP SEQUENCE user_id_seq;
CREATE SEQUENCE user_id_seq START WITH 100 INCREMENT BY 1;
DROP SEQUENCE trader_id_seq;
CREATE SEQUENCE trader_id_seq START WITH 100 INCREMENT BY 1;
DROP SEQUENCE notification_id_seq;
CREATE SEQUENCE notification_id_seq START WITH 500 INCREMENT BY 1;
DROP SEQUENCE shop_id_seq;
CREATE SEQUENCE shop_id_seq START WITH 100 INCREMENT BY 1;
DROP SEQUENCE product_category_id_seq;
CREATE SEQUENCE product_category_id_seq START WITH 1 INCREMENT BY 1;
DROP SEQUENCE product_id_seq;
CREATE SEQUENCE product_id_seq START WITH 500 INCREMENT BY 1;
DROP SEQUENCE product_image_id_seq;
CREATE SEQUENCE product_image_id_seq START WITH 500 INCREMENT BY 1;
DROP SEQUENCE query_id_seq;
CREATE SEQUENCE query_id_seq START WITH 500 INCREMENT BY 1;
DROP SEQUENCE wishlist_id_seq;
CREATE SEQUENCE wishlist_id_seq START WITH 500 INCREMENT BY 1;
DROP SEQUENCE cart_id_seq;
CREATE SEQUENCE cart_id_seq START WITH 500 INCREMENT BY 1;
DROP SEQUENCE product_cart_id_seq;
CREATE SEQUENCE product_cart_id_seq START WITH 500 INCREMENT BY 1;
DROP SEQUENCE collection_slot_id_seq;
CREATE SEQUENCE collection_slot_id_seq START WITH 500 INCREMENT BY 1;
DROP SEQUENCE voucher_id_seq;
CREATE SEQUENCE voucher_id_seq START WITH 1 INCREMENT BY 1;
DROP SEQUENCE payment_id_seq;
CREATE SEQUENCE payment_id_seq START WITH 500 INCREMENT BY 1;
DROP SEQUENCE order_id_seq;
CREATE SEQUENCE order_id_seq START WITH 500 INCREMENT BY 1;
DROP SEQUENCE order_product_id_seq;
CREATE SEQUENCE order_product_id_seq START WITH 500 INCREMENT BY 1;
DROP SEQUENCE review_id_seq;
CREATE SEQUENCE review_id_seq START WITH 500 INCREMENT BY 1;
DROP SEQUENCE db_access_id_seq;
CREATE SEQUENCE db_access_id_seq START WITH 100 INCREMENT BY 1;

-- Drop tables --
DROP TABLE USERS CASCADE CONSTRAINTS;
DROP TABLE TRADERS CASCADE CONSTRAINTS;
DROP TABLE NOTIFICATIONS CASCADE CONSTRAINTS;
DROP TABLE SHOPS CASCADE CONSTRAINTS;
DROP TABLE PRODUCT_CATEGORIES CASCADE CONSTRAINTS;
DROP TABLE PRODUCTS CASCADE CONSTRAINTS;
DROP TABLE PRODUCT_IMAGES CASCADE CONSTRAINTS;
DROP TABLE QUERIES CASCADE CONSTRAINTS;
DROP TABLE WISHLISTS CASCADE CONSTRAINTS;
DROP TABLE CARTS CASCADE CONSTRAINTS;
DROP TABLE PRODUCT_CARTS CASCADE CONSTRAINTS;
DROP TABLE collection_slots CASCADE CONSTRAINTS;
DROP TABLE vouchers CASCADE CONSTRAINTS;
DROP TABLE PAYMENTS CASCADE CONSTRAINTS;
DROP TABLE ORDERS CASCADE CONSTRAINTS;
DROP TABLE ORDER_PRODUCTS CASCADE CONSTRAINTS;
DROP TABLE REVIEWS CASCADE CONSTRAINTS;
DROP TABLE DB_ACCESS CASCADE CONSTRAINTS;

-- Table Creation --
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
CREATE TABLE SHOPS (
	shop_id                 INTEGER NOT NULL,
	shop_name               VARCHAR2(40) NOT NULL,
	address	           		VARCHAR2(40) NOT NULL,
    contact	                NUMBER(10) NOT NULL,
	trader_id	            INTEGER NOT NULL,
	
	FOREIGN KEY (trader_id) REFERENCES Users(user_id) ON DELETE CASCADE,
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
	product_name	    VARCHAR2(255) NOT NULL,
	price       	    NUMBER(10, 2) NOT NULL,
	quantity	        NUMBER(10) NOT NULL,
	availability 	    VARCHAR2(1) DEFAULT 'Y',
	description	        VARCHAR2(2000) NOT NULL,
	allergy_information VARCHAR2(2000),
	discount			NUMBER(5,2) DEFAULT 0.00,
    shop_id        		INTEGER NOT NULL,
	category_id			INTEGER NOT NULL,
	added_date			DATE NOT NULL,
    
	FOREIGN KEY (shop_id) REFERENCES Shops(shop_id) ON DELETE CASCADE,
	FOREIGN KEY (category_id) REFERENCES Product_Categories(category_id) ON DELETE CASCADE,
	CONSTRAINT	pk_PRODUCTS PRIMARY KEY (product_id)
);
CREATE TABLE PRODUCT_IMAGES (
    product_image_id        INTEGER NOT NULL,
    image_name              VARCHAR2(255),
	product_id          	INTEGER NOT NULL,

	FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    CONSTRAINT	pk_PRODUCT_IMAGES PRIMARY KEY (product_image_id)
);
CREATE TABLE QUERIES(
	query_id	        INTEGER NOT NULL,
	question	        VARCHAR2(2000) NOT NULL,
	answer    	        VARCHAR2(2000) NULL,
	question_date      	DATE NOT NULL,
	answer_date	        DATE NULL,
	product_id	        INTEGER NOT NULL,
    user_id             INTEGER NOT NULL,
	
	FOREIGN KEY (product_id) REFERENCES PRODUCTS(product_id) ON DELETE CASCADE,
	FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    CONSTRAINT	pk_QUERY PRIMARY KEY (query_id)
);
CREATE TABLE WISHLISTS (
    wishlist_id             INTEGER NOT NULL,
    user_id             	INTEGER NOT NULL,
    product_id          	INTEGER NOT NULL,    
    added_date              DATE,

	FOREIGN KEY (product_id) REFERENCES PRODUCTS(product_id) ON DELETE CASCADE,
	FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    CONSTRAINT pk_WISHLIST PRIMARY KEY (wishlist_id)  
);
CREATE TABLE CARTS (
	cart_id             INTEGER NOT NULL,
    user_id             INTEGER NOT NULL UNIQUE,
	
	FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
	CONSTRAINT	pk_CART PRIMARY KEY (cart_id)
);
CREATE TABLE PRODUCT_CARTS (
    product_cart_id     INTEGER NOT NULL,
	quantity	        NUMBER(10) NOT NULL,
	cart_id             INTEGER NOT NULL,
    product_id          INTEGER NOT NULL,

	FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE,
	CONSTRAINT	pk_PRODUCT_CART PRIMARY KEY (product_cart_id)
);
CREATE TABLE COLLECTION_SLOTS (
	collection_slot_id      INTEGER NOT NULL,
    day                     VARCHAR(20) NOT NULL,
    shift                   VARCHAR(20)  NOT NULL,

    CONSTRAINT	pk_COLLECTION_SLOT PRIMARY KEY (collection_slot_id)
);
CREATE TABLE VOUCHERS (
    voucher_id  INTEGER NOT NULL,
    code        VARCHAR2(20) NOT NULL UNIQUE,
    discount    NUMBER(5, 2) NOT NULL,
	valid_till 	DATE NOT NULL,

    CONSTRAINT	pk_VOUCHER PRIMARY KEY (voucher_id)
); 
CREATE TABLE PAYMENTS (
	payment_id          INTEGER NOT NULL,   
	payment_type        VARCHAR2(20) DEFAULT 'PAYPAL',
    amount	            NUMBER(10, 2) NOT NULL,
	payment_date	    DATE NOT NULL,
	user_id             INTEGER NOT NULL,
	paypal_order_id		VARCHAR2(128) NOT NULL,
	paypal_payer_id		VARCHAR2(128) NOT NULL,

	FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    CONSTRAINT	pk_PAYMENT PRIMARY KEY (payment_id)
);
CREATE TABLE ORDERS(
	order_id                INTEGER NOT NULL,
	ordered_date            DATE NOT NULL,
	collection_date	        DATE NOT NULL,
	collection_slot_id      INTEGER NOT NULL,
    payment_id              INTEGER NOT NULL,
	voucher_id              INTEGER,
	
	FOREIGN KEY (collection_slot_id) REFERENCES Collection_Slots(collection_slot_id) ON DELETE CASCADE,
	FOREIGN KEY (payment_id) REFERENCES Payments(payment_id) ON DELETE CASCADE,
	FOREIGN KEY (voucher_id) REFERENCES Vouchers(voucher_id) ON DELETE CASCADE,
	CONSTRAINT	pk_ORDER PRIMARY KEY (order_id)
);
CREATE TABLE ORDER_PRODUCTS (
	order_product_id	    INTEGER NOT NULL,
	quantity	        NUMBER(10) NOT NULL,
	product_id          INTEGER NOT NULL,
    order_id            INTEGER NOT NULL,
	
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE,
	FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE,
	CONSTRAINT	pk_ORDER_PRODUCT PRIMARY KEY (order_product_id)
);
CREATE TABLE REVIEWS (
	review_id	        INTEGER NOT NULL,
    rating	            NUMBER(2, 1) NOT NULL,
    review_text         VARCHAR2(2000),
	review_date	        DATE NOT NULL,
	user_id	            INTEGER NOT NULL,
    product_id          INTEGER NOT NULL,
	
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE,
    CONSTRAINT	pk_REVIEW PRIMARY KEY (review_id)
);
create table DB_ACCESS(
    id number primary key,
    username varchar(255) not null unique,
    password varchar(255) not null,
    status varchar(1) not null,
    user_role varchar(20) DEFAULT 'TRADER'
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
CREATE OR REPLACE TRIGGER query_auto_increment
BEFORE INSERT ON queries
FOR EACH ROW
BEGIN
	IF :NEW.query_id IS NULL THEN
		SELECT query_id_seq.nextval
		INTO :new.query_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER wishlist_auto_increment
BEFORE INSERT ON wishlists
FOR EACH ROW
BEGIN
	IF :NEW.wishlist_id IS NULL THEN
		SELECT wishlist_id_seq.nextval
		INTO :new.wishlist_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER cart_auto_increment
BEFORE INSERT ON carts
FOR EACH ROW
BEGIN
	IF :NEW.cart_id IS NULL THEN
		SELECT cart_id_seq.nextval
		INTO :new.cart_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER product_cart_auto_increment
BEFORE INSERT ON product_carts
FOR EACH ROW
BEGIN
	IF :NEW.product_cart_id IS NULL THEN
		SELECT product_cart_id_seq.nextval
		INTO :new.product_cart_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER collection_slot_auto_increment
BEFORE INSERT ON collection_slots
FOR EACH ROW
BEGIN
	IF :NEW.collection_slot_id IS NULL THEN
		SELECT collection_slot_id_seq.nextval
		INTO :new.collection_slot_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER voucher_auto_increment
BEFORE INSERT ON vouchers
FOR EACH ROW
BEGIN
	IF :NEW.voucher_id IS NULL THEN
		SELECT voucher_id_seq.nextval
		INTO :new.voucher_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER payment_auto_increment
BEFORE INSERT ON payments
FOR EACH ROW
BEGIN
	IF :NEW.payment_id IS NULL THEN
		SELECT payment_id_seq.nextval
		INTO :new.payment_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER order_auto_increment
BEFORE INSERT ON orders
FOR EACH ROW
BEGIN
	IF :NEW.order_id IS NULL THEN
		SELECT order_id_seq.nextval
		INTO :new.order_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER order_product_auto_increment
BEFORE INSERT ON order_products
FOR EACH ROW
BEGIN
	IF :NEW.order_product_id IS NULL THEN
		SELECT order_product_id_seq.nextval
		INTO :new.order_product_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER review_auto_increment
BEFORE INSERT ON reviews
FOR EACH ROW
BEGIN
	IF :NEW.review_id IS NULL THEN
		SELECT review_id_seq.nextval
		INTO :new.review_id
		FROM dual;
	END IF;
END;
/
CREATE OR REPLACE TRIGGER db_access_auto_increment
BEFORE INSERT ON db_access
FOR EACH ROW
BEGIN
	IF :NEW.id IS NULL THEN
		SELECT db_access_id_seq.nextval
		INTO :new.id
		FROM dual;
	END IF;
END;
/

-- insert vouchers
INSERT INTO VOUCHERS (voucher_id, code, discount, valid_till) VALUES (1, 'EFOODBASKET', 20, DATE '2021-07-20');
INSERT INTO VOUCHERS (voucher_id, code, discount, valid_till) VALUES (2, 'ROHIT', 13, DATE '2021-07-20');

-- insert collection slots 
INSERT INTO COLLECTION_SLOTS VALUES (1, 'WEDNESDAY', '10:00 - 13:00');
INSERT INTO COLLECTION_SLOTS VALUES (2, 'WEDNESDAY', '13:00 - 16:00');
INSERT INTO COLLECTION_SLOTS VALUES (3, 'WEDNESDAY', '16:00 - 19:00');
INSERT INTO COLLECTION_SLOTS VALUES (4, 'THURSDAY', '10:00 - 13:00');
INSERT INTO COLLECTION_SLOTS VALUES (5, 'THURSDAY', '13:00 - 16:00');
INSERT INTO COLLECTION_SLOTS VALUES (6, 'THURSDAY', '16:00 - 19:00');
INSERT INTO COLLECTION_SLOTS VALUES (7, 'FRIDAY', '10:00 - 13:00');
INSERT INTO COLLECTION_SLOTS VALUES (8, 'FRIDAY', '13:00 - 16:00');
INSERT INTO COLLECTION_SLOTS VALUES (9, 'FRIDAY', '16:00 - 19:00');





------ INSERT ALL
---User Insert
INSERT INTO USERS(USER_ID,EMAIL,FULLNAME,PASSWORD,ADDRESS,USER_ROLE,CONTACT,JOINED_DATE,OTP,TOKEN,OTP_LAST_DATE,IS_VERIFIED) VALUES (6,'greengrocer@gmail.com','GreenGrocer trader','$2y$10$UTyY3E.8lKTKFxua2dgDq.JmMnprJxi0WMjPmI7xZ6Y5zaL2VYY/K','Cleckhuddersfax','TRADER',1234567890,'06/27/2021',697720,'685590061753467a2ee78c7305da4704112e160f749c47c1','06/27/2021','Y');
INSERT INTO USERS(USER_ID,EMAIL,FULLNAME,PASSWORD,ADDRESS,USER_ROLE,CONTACT,JOINED_DATE,OTP,TOKEN,OTP_LAST_DATE,IS_VERIFIED) VALUES (2,'srijanpanta@gmail.com','Srijan Panta','$2y$10$V9N5QKyKyZzs46VJhkGL6.Inb8pnmqaTgbqrlmeZZQO6pACxgTFla','Cleckhuddersfax','ADMIN',9845506555,'06/27/2021',853461,NULL,'06/27/2021','Y');
INSERT INTO USERS(USER_ID,EMAIL,FULLNAME,PASSWORD,ADDRESS,USER_ROLE,CONTACT,JOINED_DATE,OTP,TOKEN,OTP_LAST_DATE,IS_VERIFIED) VALUES (5,'fishmonger@gmail.com','Fish Monger','$2y$10$UTyY3E.8lKTKFxua2dgDq.JmMnprJxi0WMjPmI7xZ6Y5zaL2VYY/K','Cleckhuddersfax','TRADER',1234567890,'06/27/2021',171332,'d6319c538ab6a614f91b2803cee7b6366574f20a6fa0e1ae','06/27/2021','Y');
INSERT INTO USERS(USER_ID,EMAIL,FULLNAME,PASSWORD,ADDRESS,USER_ROLE,CONTACT,JOINED_DATE,OTP,TOKEN,OTP_LAST_DATE,IS_VERIFIED) VALUES (4,'butcher@gmail.com','Fresh Butchers','$2y$10$E6MwR6g8AW7pFoMRhUbkneBinPvu37xmFiQpXh25.18UySoWOlptW','Cleckhuddersfax','TRADER',1234567890,'06/27/2021',304485,'cc57751ded32f5a46c4eb3866a243418d32ccf6a117c3b6a','06/27/2021','Y');
INSERT INTO USERS(USER_ID,EMAIL,FULLNAME,PASSWORD,ADDRESS,USER_ROLE,CONTACT,JOINED_DATE,OTP,TOKEN,OTP_LAST_DATE,IS_VERIFIED) VALUES (7,'bakery@gmail.com','Cleck Bakery','$2y$10$0b0HYTNuIFQBB0isVJPdN.ohFuKuOqYX2ILltnsUv5mUGvpHziPce','Cleckhuddersfax','TRADER',1234567890,'06/27/2021',726812,'8d72379e777c5cb10bbd54ced1761a6b29f9f5e1e7d0cc43','06/27/2021','Y');
INSERT INTO USERS(USER_ID,EMAIL,FULLNAME,PASSWORD,ADDRESS,USER_ROLE,CONTACT,JOINED_DATE,OTP,TOKEN,OTP_LAST_DATE,IS_VERIFIED) VALUES (8,'delicatessen@gmail.com','Cleck Delicatessen','$2y$10$KpIjf0MW5sVvvb5HO7VJwOY9/JN6tEWCQopl7yXPw/mSseiZlM8t2','Cleckhuddersfax','TRADER',1234567890,'06/27/2021',482769,'c2a90e740b3eaad41deb4b1314d661105b12bf151348df7d','06/27/2021','Y');
INSERT INTO USERS(USER_ID,EMAIL,FULLNAME,PASSWORD,ADDRESS,USER_ROLE,CONTACT,JOINED_DATE,OTP,TOKEN,OTP_LAST_DATE,IS_VERIFIED) VALUES (11,'psrijan19@tbc.edu.np','Srijan Panta','$2y$10$nif3qTDsP.i6VEVsIi4hmOajxD/ySVOaR15oMnKicNiJcAkbvlsxq','Cleckhuddersfax','CUSTOMER',9845506555,'07/01/2021',220640,'c6136bce44bde75881961ce7eb9d7a48ade32c453f1fdefe','07/08/2021','Y');
INSERT INTO USERS(USER_ID,EMAIL,FULLNAME,PASSWORD,ADDRESS,USER_ROLE,CONTACT,JOINED_DATE,OTP,TOKEN,OTP_LAST_DATE,IS_VERIFIED) VALUES (41,'aprabesh65@gmail.com','Prabesh Adhikari','$2y$10$yAPEsaMMGhtjKUpDnfE/9OAVsCBIRxmipWHDdxoNmUoKl8aU5qJ6y','Cleckhuddersfax','CUSTOMER',9823123131,'07/09/2021',223036,NULL,'07/09/2021','Y');
INSERT INTO USERS(USER_ID,EMAIL,FULLNAME,PASSWORD,ADDRESS,USER_ROLE,CONTACT,JOINED_DATE,OTP,TOKEN,OTP_LAST_DATE,IS_VERIFIED) VALUES (42,'lcbiplove1@gmail.com','Biplove Lamichhane','$2y$10$dVdJGmp/Qgn2NwB.8dsJUuBeOtjG9oPY29FCXkGwfnUEUUPrudK0O','Cleckhuddersfax','ADMIN',9866040057,'07/09/2021',827403,NULL,'07/09/2021','Y');
INSERT INTO USERS(USER_ID,EMAIL,FULLNAME,PASSWORD,ADDRESS,USER_ROLE,CONTACT,JOINED_DATE,OTP,TOKEN,OTP_LAST_DATE,IS_VERIFIED) VALUES (43,'sunampokharel243@gmail.com','Sunam Pokharel','$2y$10$KIgacVNAGNdUQidZO1dd0.Wa8h3TLCCSiR6Ifg0/na3mLy4X9feSG','Cleckhuddersfax','CUSTOMER',9851176456,'07/09/2021',809997,NULL,'07/09/2021','Y');
INSERT INTO USERS(USER_ID,EMAIL,FULLNAME,PASSWORD,ADDRESS,USER_ROLE,CONTACT,JOINED_DATE,OTP,TOKEN,OTP_LAST_DATE,IS_VERIFIED) VALUES (44,'manandharsamyak7@gmail.com','Samyak Manandhar','$2y$10$/lOi/jotH5eD4KiNQvqLve2AVQQihw5tfBuefMY6W6e88pbZ8ajsW','Cleckhuddersfax','CUSTOMER',9841229458,'07/09/2021',355170,NULL,'07/09/2021','Y');

---TRADERS INSERT
INSERT INTO TRADERS(TRADER_ID,PAN,PRODUCT_TYPE,PRODUCT_DETAILS,DOCUMENTS_PATH,IS_APPROVED,APPROVED_DATE,USER_ID) VALUES (5,'ABCDEF1345G','Bakery','We sell bakery items. We sell bakery items. We sell bakery items. We sell bakery items. We sell bakery items. We sell bakery items. We sell bakery items. We sell bakery items.','cover letter_4.jpg','Y','06/27/2021',7);
INSERT INTO TRADERS(TRADER_ID,PAN,PRODUCT_TYPE,PRODUCT_DETAILS,DOCUMENTS_PATH,IS_APPROVED,APPROVED_DATE,USER_ID) VALUES (6,'ABCDEF4652G','Delicatessen','We sell delicatessen products. We sell delicatessen products. We sell delicatessen products. We sell delicatessen products. We sell delicatessen products. We sell delicatessen products. We sell delicatessen products. We sell delicatessen products.','cover letter_5.jpg','Y','06/27/2021',8);
INSERT INTO TRADERS(TRADER_ID,PAN,PRODUCT_TYPE,PRODUCT_DETAILS,DOCUMENTS_PATH,IS_APPROVED,APPROVED_DATE,USER_ID) VALUES (3,'ABCDEF4578G','Fish','We sell different types of fish. We sell different types of fish. We sell different types of fish. We sell different types of fish.','cover letter_2.jpg','Y','06/27/2021',5);
INSERT INTO TRADERS(TRADER_ID,PAN,PRODUCT_TYPE,PRODUCT_DETAILS,DOCUMENTS_PATH,IS_APPROVED,APPROVED_DATE,USER_ID) VALUES (2,'ABCDEF1234G','Meat','We sell fresh meat products. We sell fresh meat products. We sell fresh meat products.We sell fresh meat products.We sell fresh meat products.We sell fresh meat products.We sell fresh meat products.We sell fresh meat products.We sell fresh meat products.','cover letter_1.jpg','Y','06/27/2021',4);
INSERT INTO TRADERS(TRADER_ID,PAN,PRODUCT_TYPE,PRODUCT_DETAILS,DOCUMENTS_PATH,IS_APPROVED,APPROVED_DATE,USER_ID) VALUES (4,'ABCDEF5225G','Grocery','We sell fresh vegetables. We sell fresh vegetables. We sell fresh vegetables. We sell fresh vegetables. We sell fresh vegetables. We sell fresh vegetables. We sell fresh vegetables.','cover letter_3.jpg','Y','06/27/2021',6);

---CARTS
INSERT INTO CARTS(CART_ID,USER_ID) VALUES (3,11);
INSERT INTO CARTS(CART_ID,USER_ID) VALUES (21,41);
INSERT INTO CARTS(CART_ID,USER_ID) VALUES (22,42);
INSERT INTO CARTS(CART_ID,USER_ID) VALUES (23,43);
INSERT INTO CARTS(CART_ID,USER_ID) VALUES (24,44);

DECLARE 
butcher_id number(10):= 4;
greengrocer_id number(10):= 6;
fishmonger_id number(10):= 5;
bakery_id number(10):= 7;
delicatessen_id number(10):= 8;
BEGIN

delete from product_images;
delete from products;
delete from product_categories;
delete from shops;
----------------------------------------------------BUTCHERS------------------------------------------------------------------------------------------------
    -----Shops---------    
        INSERT INTO SHOPS (shop_id,shop_name,address,contact,trader_id) 
        VALUES (1,'Butcher Shop','Cleckhuddersfax',9876463210,butcher_id);

        INSERT INTO SHOPS (shop_id,shop_name,address,contact,trader_id) 
        VALUES (2,'Butcher Fresh Shop','Cleckhuddersfax',9876463210,butcher_id);
    -----Category Chicken 1---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (1, 'Chicken', 'Our free-range chicken is exceptionally active, producing healthy and natural fats because of their varied diet and lifestyle.');
    

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (1, 'Organic Ground Chicken', 10.05, 30, 'Y', 'Made from 100% chicken breast and leg meat, our organic ground chicken is perfect for meatloaf, burgers, meat sauces and chili. No added broth, No water and NO chlorine. Just 100% breast meat.', '', 0, 1, 1, sysdate);
        
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (1,'Organic Ground Chicken1.jpg',1);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (2,'Organic Ground Chicken2.jpg',1);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (3,'Organic Ground Chicken3.jpg',1);
        
        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (2, 'Air Chilled Chicken Wings', 12.35, 30, 'Y', 'These chicken wings are perfect for easy meals, whether roasted or braised. Our chickens are raised humanely, with no antibiotics, hormones, or arsenicals. Air-chilled for best flavor, this chicken is a restaurant favorite and now available for home cooks.', '', 0, 1, 1, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (4,'Air Chilled Chicken Wings1.jpg',2);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (5,'Air Chilled Chicken Wings2.jpg',2);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (6,'Air Chilled Chicken Wings3.jpg',2);
                   
        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (3, 'Chicken Livers', 6.05, 30, 'Y', 'Chicken liver is conserved as one of the superfoods of nature. It is packed with essential nutrients. Obtained from farm-raised, healthy chickens, the chicken liver is a rich source of vitamins, protein, and iron. Ideal for pan-fried or stir-fried dishes.', '', 0, 1, 1, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (7,'Chicken Livers1.jpg',3);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (8,'Chicken Livers2.jpg',3);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (9,'Chicken Livers3.jpg',3);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (4, 'Chicken & Roasted Red Pepper Sausage', 11.50, 30, 'Y', 'They have no additional flavours added and are made from our 100% Free Chicken Range Chicken Leg meat. We’ve chosen this cut of the bird because we know the darker leg meat is richest in flavor. Because of this, we can guarantee that this sausage is full of flavor, tender and lean. They’re low in fat, high in protein and high in flavor. ', 'Contains Sulphites.', 0, 1, 1, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (10,'Chicken & Roasted Red Pepper Sausage1.jpg',4);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (11,'Chicken & Roasted Red Pepper Sausage2.jpg',4);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (12,'Chicken & Roasted Red Pepper Sausage3.jpg',4);

    -----Category Pork 2---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (2, 'Pork', 'Our pork promises rich succulence. We strictly use free-range pork, for both ethical reasons as well as for quality. Hogs reared outdoors simply taste better, offering flavour and wholesome nutrition.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (5, 'Pork Loin Steaks', 8.05, 30, 'Y', 'The loin is from the top of the rib cage, where the pork yields lean meat, with creamy fat covering. This steak is very versatile, being well suited to grilling, frying, slow cooking, or griddling.', '', 0, 1, 2, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (13,'Pork Loin Steaks1.jpg',5);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (14,'Pork Loin Steaks2.jpg',5);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (15,'Pork Loin Steaks3.jpg',5);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (6, 'Hardwood Smoked Bacons', 7.99, 30, 'Y', 'Naturally Hardwood Smoked Bacon is slow smoked and hand-trimmed from the finest cuts of pork. We use whole cuts of rare breed cross free-range British pork from older pigs which gives a much deeper, more intense flavour. The muscles have a fermentation culture added to them at the time of dry curing. The fermenting culture activates preserving enzymes & amino acids in the meat deepening the flavour profile even further. The cultures will react naturally with the sugars in the meat to create a milder, sweeter tasting bacon. Using whole cured muscles which are then matured and dried for approx. 3 days to ensure there is very little moisture left in the meat. This gives a much crisper slice.', '', 0, 1, 2, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (16,'Hardwood Smoked Bacons1.jpg',6);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (17,'Hardwood Smoked Bacons2.jpg',6);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (18,'Hardwood Smoked Bacons3.jpg',6);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (7, 'Pork Liver', 7.99, 30, 'Y', 'Our Pork is raised on green pastures, with no added hormones, no antibiotics, no chemical fertilizer, and no pesticides. No GMOs, no corn, and no soy.', '', 0, 1, 2, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (19,'Pork Liver1.jpg',7);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (20,'Pork Liver2.jpg',7);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (21,'Pork Liver3.jpg',7);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (8, 'Fresh Pork Ham Steak', 13.50, 30, 'Y', 'Ham is made from Pork leg. The pork leg is brined, cured and carefully trimmed. It is then naturally smoked to perfection over a mixture of hickory and applewood chips for up to 30 hours. Antibiotic free and hormone free from birth! Their outdoor diet is supplemented with Non-GMO grain. Fresh Pork Ham Steak is a savory treat!', '', 0, 1, 2, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (22,'Fresh Pork Ham Steak1.jpg',8);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (23,'Fresh Pork Ham Steak2.jpg',8);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (24,'Fresh Pork Ham Steak3.jpg',8);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (9, 'Pig Feet', 10.85, 30, 'Y', 'Pig Feet is a world-famous exotic-cut pork item that’s enjoyed by many. These succulent Pork Feet are sure to satisfy any appetite. Available by the pound and wholesale quantities!', '', 0, 1, 2, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (25,'Pig Feet1.jpg',9);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (26,'Pig Feet2.jpg',9);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (27,'Pig Feet3.jpg',9);

    -----Category Lamb 3---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (3, 'Lamb', 'With a dedicated promise to free-range and high welfare farming, we provide the greatest level of care for animals. All of our lamb is hung to maximize its quality and fine-grained texture.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (10, 'Lamb Leg Steaks', 6.35, 30, 'Y', 'Lamb Leg Steaks are a great alternative to lamb steaks cut from the loin, and are perfect for making nutritious and wholesome mid-week meals. These classic steaks lend themselves to frying or griddling on a high heat, with the impressive sweet taste reflecting the quality of the lush pastures where our flocks mature on diets of wild flowers, herbs, and natural grasses.', '', 0, 1, 3, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (28,'Lamb Leg Steaks1.jpg',10);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (29,'Lamb Leg Steaks2.jpg',10);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (30,'Lamb Leg Steaks3.jpg',10);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (11, 'Lamb Frenched Rack', 12.05, 30, 'Y', 'Visually stunning, and on the bone for maximum flavour, these mini lamb racks are ideal for roasted individual portions of our succulent lamb, with each rack promising the qualities that set our lamb apart. That is, rich flavours owing to the lush pastures of our small farms where our flocks mature on diets of wild flowers, herbs, and natural grasses.', '', 0, 1, 3, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (31,'Lamb Frenched Rack1.jpg',11);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (32,'Lamb Frenched Rack2.jpg',11);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (33,'Lamb Frenched Rack3.jpg',11);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (12, 'Ground Lamb', 11.55, 30, 'Y', 'Our Minced Lamb boasts the standout flavour that sets our lamb apart, with a good ratio of fat to meat to give melting tenderness and real taste upon cooking. It’s also made using our finest free-range lamb. It can be used for making an array of wonderful, quick dinners.', '', 0, 1, 3, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (34,'Ground Lamb1.jpg',12);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (35,'Ground Lamb2.jpg',12);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (36,'Ground Lamb3.jpg',12);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (13, 'Lamb Rib Chops', 11.99, 30, 'Y', 'Lamb Chops are a versatile cut, perfect for quick mid-week meals or marinating as a barbecue or dinner party dish. Lamb chops naturally lend themselves to frying or griddling, with the impressive flavour reflecting the diets of wild flowers, herbs, and natural grasses our free ranging flocks mature upon.', '', 0, 1, 3, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (37,'Lamb Rib Chops1.jpg',13);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (38,'Lamb Rib Chops2.jpg',13);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (39,'Lamb Rib Chops3.jpg',13);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (14, 'Lamb Sweetbreads', 13.50, 30, 'Y', 'Sweetbreads have a delicate flavour, and a soft and creamy texture.', '', 0, 1, 3, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (40,'Lamb Sweetbreads1.jpg',14);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (41,'Lamb Sweetbreads2.jpg',14);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (42,'Lamb Sweetbreads3.jpg',14);

    -----Category Wagyu Beef 4---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (4, 'Wagyu Beef', 'Wagyu literally translates to "Japanese Cow". The breed is highly sought after due to its consistent high quality and reputation. Our Japanese Wagyu steak is some of the most sought-after beef in the world.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (15, 'Hokkaido Wagyu Beef A5 Ribeye Steak', 9.50, 30, 'Y', 'The ribeye cut is prized for its exceptional flavor and texture.Authentic A5 Graded Wagyu beef imported from Japan.Hokkaido Perfecture. Temperature controlled standard overnight shipping.', '', 0, 1, 4, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (43,'Hokkaido Wagyu Beef A5 Ribeye Steak1.jpg',15);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (44,'Hokkaido Wagyu Beef A5 Ribeye Steak2.jpg',15);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (45,'Hokkaido Wagyu Beef A5 Ribeye Steak3.jpg',15);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (16, 'Hokkaido Wagyu Beef A5 Whole Boneless Strip Loin', 25.55, 30, 'Y', 'The Striploin is commonly known as the New York Strip. The cut is great for grilling. For something quick and delicious, simply portion INSERT into steaks.', '', 0, 1, 4, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (46,'Hokkaido Wagyu Beef A5 Whole Boneless Strip Loin1.jpg',16);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (47,'Hokkaido Wagyu Beef A5 Whole Boneless Strip Loin2.jpg',16);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (48,'Hokkaido Wagyu Beef A5 Whole Boneless Strip Loin3.jpg',16);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (17, 'Hokkaido Wagyu Beef A5 Wagyu Beef Assortment Steaks', 30.55, 30, 'Y', 'The ribeye cut is prized for its decadent flavor and rich buttery texture. It offers generous marbling that always finishes juicy and tender. The striploin is moderately tender with a balanced texture. It offers good marbling and a mouth-watering robust beef flavor.', '', 0, 1, 4, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (49,'Hokkaido Wagyu Beef A5 Wagyu Beef Assortment Steaks1.jpg',17);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (50,'Hokkaido Wagyu Beef A5 Wagyu Beef Assortment Steaks2.jpg',17);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (51,'Hokkaido Wagyu Beef A5 Wagyu Beef Assortment Steaks3.jpg',17);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (18, 'Miyazakigyu A5 Wagyu Beef Coulotte', 40.55, 30, 'Y', 'Miyazakigyu is Wagyu which must be raised and processed in Miyazaki Prefecture. The Coulotte Steak is best served at Medium Rare.', '', 0, 1, 4, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (52,'Miyazakigyu A5 Wagyu Beef Coulotte1.jpg',18);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (53,'Miyazakigyu A5 Wagyu Beef Coulotte2.jpg',18);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (54,'Miyazakigyu A5 Wagyu Beef Coulotte3.jpg',18);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (19, 'Miyazakigyu A5 Wagyu Beef Tenderloin Cubes', 35.55, 30, 'Y', 'Cut from Tenderloin only. Each pack is hand cut.', '', 0, 1, 4, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (55,'Miyazakigyu A5 Wagyu Beef Tenderloin Cubes1.jpg',19);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (56,'Miyazakigyu A5 Wagyu Beef Tenderloin Cubes2.jpg',19);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (57,'Miyazakigyu A5 Wagyu Beef Tenderloin Cubes3.jpg',19);
    -----Category Duck 5---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (5, 'Duck', 'For our free-range duck, we use high welfare systems coupled with traditional farming techniques so that our birds plump up gradually. Try our duck breast, or our whole pekin duck, to experience the flavor for yourself.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (20, 'Whole Pekin Duck', 25.55, 30, 'Y', 'Humanely-raised Pekin Ducks. Delicately-flavored meat. No antibiotics, no hormones. Sold in an uncooked state.', '', 0, 1, 5, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (58,'Whole Pekin Duck1.jpg',20);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (59,'Whole Pekin Duck2.jpg',20);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (60,'Whole Pekin Duck3.jpg',20);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (21, 'Duck Prosciutto (Uncured Duck Breast)', 20.50, 30, 'Y', 'Dry-cured duck prosciutto, made with magret from Mulard ducks raised humanely with no antibiotics or hormones. This ready-to-eat prosciutto is perfect when sliced for a charcuterie or cheese board. It can also be cubed and added to beans, pasta, and stew.', '', 0, 1, 5, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (61,'Duck Prosciutto (Uncured Duck Breast)1.jpg',21);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (62,'Duck Prosciutto (Uncured Duck Breast)2.jpg',21);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (63,'Duck Prosciutto (Uncured Duck Breast)3.jpg',21);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (22, 'Rendered Duck Fat', 20.55, 30, 'Y', 'Duck fat has similar qualities to olive oil. It has a delicate flavour and is the healthy alternative to butter, adding its distinctive, delicate flavour to all your favorite dishes. Salt-free, no preservatives', '', 0, 1, 5, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (64,'Rendered Duck Fat1.jpg',22);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (65,'Rendered Duck Fat2.jpg',22);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (66,'Rendered Duck Fat3.jpg',22);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (23, 'Cured Duck Salami', 17.35, 30, 'Y', 'Our salami is made with 100% Magret breast for optimal tenderness. This salami is all-natural, without sodium nitrate or other preservatives. Rich and flavorful, the duck meat is mixed with spices, fresh garlic, white wine, and black pepper, all in a natural pork casing. It is dry-cured slowly giving an unmatched flavor profile.', '', 0, 1, 5, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (67,'Cured Duck Salami1.jpg',23);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (68,'Cured Duck Salami2.jpg',23);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (69,'Cured Duck Salami3.jpg',23);

    -----Category Turkey 6---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (6, 'Turkey', 'Turkeys are sold sliced and ground, as well as "whole" in a manner similar to chicken with the head, feet, and feathers removed.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (24, 'Whole Bird', 25.00, 30, 'Y', 'Irish whole turkey, fresh from local Country Down farms. Turkey is a very rich source of protein, niacin, vitamin B6 and the amino acid tryptothan. Apart from these nutrients, it is also contains zinc and vitamin B12. The skinless white meat of turkey is low on fat and is an excellent source of high protein. Turkey also contains anti-cancer properties.', '', 0, 1, 6, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (70,'Whole Bird1.jpg',24);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (71,'Whole Bird2.jpg',24);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (72,'Whole Bird3.jpg',24);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (25, 'Turkey Butterfly', 18.50, 30, 'Y', 'Fresh / Frozen available Specified origin on request', '', 0, 1, 6, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (73,'Turkey Butterfly1.jpg',25);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (74,'Turkey Butterfly2.jpg',25);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (75,'Turkey Butterfly3.jpg',25);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (26, 'Turkey Lobes', 16.45, 30, 'Y', 'Red Tractor Farm Assured skin on, boneless, single turkey breast fillet lobe, held with elastic bands; 2-2.5kg. Hand-prepared and banded, ready to cook. Boneless for easy carving once cooked. Small joint for quicker cook time, particularly suitable for smaller-volume users. Red Tractor farm assured for your guarantee of quality and British provenance - great for promoting on your menu.', '', 0, 1, 6, sysdate);
            
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (76,'Turkey Lobes1.jpg',26);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (77,'Turkey Lobes2.jpg',26);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (78,'Turkey Lobes3.jpg',26);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (27, 'Turkey Crown', 19.55, 30, 'Y', 'Turkey breast crown with backbone & wing; 7.25-9kg crown, Skin-on and hand-prepared. Backbone and wing give added visual appeal on a carvery. Red Tractor farm assured for your guarantee of quality and British provenance - great for promoting on your menu. Large bird size is particularly suitable for high-volume users.', '', 0, 1, 6, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (79,'Turkey Crown1.jpg',27);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (80,'Turkey Crown2.jpg',27);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (81,'Turkey Crown3.jpg',27);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (28, 'Minced Turkey', 21.35, 30, 'Y', 'ree flow frozen 6mm minced turkey', '', 0, 1, 6, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (82,'Minced Turkey1.jpg',28);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (83,'Minced Turkey2.jpg',28);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (84,'Minced Turkey3.jpg',28);

----------------------------------------------------GREENGROCERS--------------------------------------------------------------------------------------------
    -----Shops---------    
        INSERT INTO SHOPS (shop_id,shop_name,address,contact,trader_id) 
        VALUES (3,'Greengroshers Shop','Cleckhuddersfax',9876463210,greengrocer_id);

        INSERT INTO SHOPS (shop_id,shop_name,address,contact,trader_id) 
        VALUES (4,'Greengroshers Fresh Shop','Cleckhuddersfax',9876463210,greengrocer_id);
    -----Category Fruit 7---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (7, 'Fruit', 'We''re proud to source the freshest and sweetest selection of fruits.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (29, 'Figs', 6.50, 30, 'Y', 'Fresh figs are small to medium sized, with a bright green to yellowish colored skin. Their flesh is pink blush to deep magenta with a mass of juicy seeds. They have honey like sweetness with fresh flavor and nuttiness from the numerous seeds.', '', 0, 3, 7, sysdate);
            
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (85,'Figs1.jpg',29);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (86,'Figs2.jpg',29);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (87,'Figs3.jpg',29);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (30, 'Cantaloupe', 7.50, 30, 'Y', 'The beige-green skin of the cantaloupe barely hints at the delectably fragrant, orange-colored fruit inside. A delicious, refreshing juice made from cantaloupe is a summer favorite!', '', 0, 3, 7, sysdate);
            
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (88,'Cantaloupe1.jpg',30);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (89,'Cantaloupe2.jpg',30);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (90,'Cantaloupe3.jpg',30);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (31, 'Passion Fruit', 9.80, 30, 'Y', 'Passion fruit is generally round shaped with yellow-orange skin that gives the taste buds an explosion of sweetness to tartness. The flesh of a passion fruit is soft and juicy. Its jelly like texture with the crunchiness of numerous tart seeds within the flesh is a taste loved by many.', '', 0, 3, 7, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (91,'Passion Fruit1.jpg',31);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (92,'Passion Fruit2.jpg',31);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (93,'Passion Fruit3.jpg',31);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (32, 'Kiwis', 10.55, 30, 'Y', 'Kiwis are distinct with its sweet-sour taste and a pleasant smell. One Kiwi contains a full day requirement of vitamin C. It is also full of dietary Fiber, Vitamin K, Amino acid, Vitamin B6 and several antioxidants.', '', 0, 3, 7, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (94,'Kiwis1.jpg',32);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (95,'Kiwis2.jpg',32);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (96,'Kiwis3.jpg',32);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (33, 'Mandarines', 7.35, 30, 'Y', 'Mandarines are sugary and juicy and is wealthy in Vitamin C, A and Folate and includes small amounts of Vitamin E & B complex vitamins too. Easy Peel, great for the lunch box, the perfect snack.', '', 0, 3, 7, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (97,'Mandarines1.jpg',33);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (98,'Mandarines2.jpg',33);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (99,'Mandarines3.jpg',33);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (34, 'Pineapple', 8.45, 30, 'Y', 'With the shape of a pine cone, the fruit is loosely fibrous and juicy with white to yellowish flesh. The edible center part is firm, leathery and sweet. Pineapples reduce the risk of macular degeneration, a disease that affects the eyes as people age due to vitamin C and antioxidants present in it.', '', 0, 3, 7, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (100,'Pineapple1.jpg',34);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (101,'Pineapple2.jpg',34);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (102,'Pineapple3.jpg',34);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (35, 'Watermelon', 5.55, 30, 'Y', 'Watermelons are globular in shape and are freshly picked for you directly from our farmers. The juicy, sweet and grainy textured flesh is filled with 12-14% of sugar content, making it a healthy alternative to sugary carbonated drinks. Watermelons have excellent hydrating properties with 90% water content.', '', 0, 3, 7, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (103,'Watermelon1.jpg',35);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (104,'Watermelon2.jpg',35);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (105,'Watermelon3.jpg',35);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (36, 'Raspberry', 9.35, 30, 'Y', 'Raspberry have a fair number of antioxidants and are great for making smoothies, juices and desserts. These fruits can also be used as a cereal topper or can be mixed with yoghurt and relished as a healthy snack.', '', 0, 3, 7, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (106,'Raspberry1.jpg',36);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (107,'Raspberry2.jpg',36);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (108,'Raspberry3.jpg',36);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (37, 'Blueberry', 9.55, 30, 'Y', 'Plump, smooth-skinned and indigo colored perfect little globes of juicy berries have mostly sweet and slightly tart flavor. Can also be used as a cereal topper or can be mixed with yoghurt and relished as a healthy snack.', '', 0, 3, 7, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (109,'Blueberry1.jpg',37);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (110,'Blueberry2.jpg',37);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (111,'Blueberry3.jpg',37);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (38, 'Papaya', 7.55, 30, 'Y', 'They have a musky taste and buttery consistency. Papayas reduce the risk of macular degeneration, a disease that affects the eyes as people age.', '', 0, 3, 1, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (112,'Papaya1.jpg',38);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (113,'Papaya2.jpg',38);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (114,'Papaya3.jpg',38);

    -----Category Vegetable 8---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (8, 'Vegetable', 'Finest and Freshest quality produce from the markets and local farmers.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (39, 'Asparagus', 12.55, 30, 'Y', 'The uniquely mild and bitter flavored Asparagus has long, deep green stalks that are tender at the tip and thick at the end. Asparagus is fiber rich and a natural diuretic.', '', 0, 3, 8, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (115,'Asparagus1.jpg',39);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (116,'Asparagus2.jpg',39);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (117,'Asparagus3.jpg',39);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (40, 'Baby Spinach', 6.50, 30, 'Y', 'Baby Spinach contains low fat and cholesterol and more amount of fiber.', '', 0, 3, 8, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (118,'Baby Spinach1.jpg',40);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (119,'Baby Spinach2.jpg',40);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (120,'Baby Spinach3.jpg',40);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (41, 'Cabbage Drumhead', 10.55, 30, 'Y', 'With a texture of crispness and juiciness the moment you take the first bite, cabbages are sweet and grassy flavored with dense and smooth leafy layers. Best for people who want to lose weight in a healthy way.It detoxifies the body and contains glutamine that reduces effects of inflammation, allergies, joint pain, irritation, fever.', '', 0, 3, 8, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (121,'Cabbage Drumhead1.jpg',41);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (122,'Cabbage Drumhead2.jpg',41);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (123,'Cabbage Drumhead3.jpg',41);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (42, 'Brussel Sprouts', 13.55, 30, 'Y', 'Delicate, earthy flavor with hints of nuttiness. These hearty little green nuggets pack loads of healthful fiber and antioxidants, with a tiny calorie count.', '', 0, 3, 8, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (124,'Brussel Sprouts1.jpg',42);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (125,'Brussel Sprouts2.jpg',42);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (126,'Brussel Sprouts3.jpg',42);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (43, 'Broccoli', 6.55, 30, 'Y', 'Broccolis have clusters of small, tight flower heads. Its nutritious, low in calories, available year-round and hearty.', '', 0, 3, 8, sysdate);
           
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (127,'Broccoli1.jpg',43);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (128,'Broccoli2.jpg',43);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (129,'Broccoli3.jpg',43);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (44, 'Chilli Birdseye', 6.00, 30, 'Y', 'Chilis are the best kitchen ingredient to bring a dash of spiciness to recipes. The fresh flavor and sharp bite make them a must in almost all dishes. It also possesses anti-bacterial properties.', '', 0, 3, 8, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (130,'Chilli Birdseye1.jpg',44);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (131,'Chilli Birdseye2.jpg',44);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (132,'Chilli Birdseye3.jpg',44);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (45, 'Cauliflower', 5.99, 30, 'Y', 'Cauliflower is made up of tightly bound clusters of soft, crumbly, sweet cauliflower florets that form a dense head. Cauliflowers are rich in B complex vitamins, potassium and manganese.', '', 0, 3, 8, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (133,'Cauliflower1.jpg',45);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (134,'Cauliflower2.jpg',45);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (135,'Cauliflower3.jpg',45);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (46, 'Bok Choy', 7.20, 30, 'Y', 'Bok choy is a type of Chinese cabbage known for its mild flavor that serves as the perfect complement to other foods in dishes like stir-fries and also referred to as “soup spoon” because of the shape of its leaves.', '', 0, 3, 8, sysdate);
            
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (136,'Bok Choy1.jpg',46);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (137,'Bok Choy2.jpg',46);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (138,'Bok Choy3.jpg',46);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (47, 'Eggplant', 8.90, 30, 'Y', 'Deep purple and oval shaped eggplants are glossy skinned vegetables with a white and have a soft flesh. Eggplants are a nutritionally rich food item. They are rich in dietary fibers, Vitamin C and K, phytonutrient compounds and anti-oxidants.', '', 0, 3, 8, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (139,'Eggplant1.jpg',47);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (140,'Eggplant2.jpg',47);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (141,'Eggplant3.jpg',47);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (48, 'Shitake Mushroom', 12.40, 30, 'Y', 'Shitake is very convenient for a quick stir fry, just open the package and toss right in. Shitake Mushrooms boost our immune system.', '', 0, 3, 8, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (142,'Shitake Mushroom1.jpg',48);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (143,'Shitake Mushroom2.jpg',48);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (144,'Shitake Mushroom3.jpg',48);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (49, 'Yellow Zucchini', 13.55, 30, 'Y', 'Yellow zucchini is a long and slightly fat vegetable, also known as squash. Yellow variant has comparatively softer flesh and tastes sweeter than the green one. Zucchinis are rich in vitamin A, magnesium, folate, potassium, copper, and phosphorus.', '', 0, 3, 1, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (145,'Yellow Zucchini1.jpg',49);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (146,'Yellow Zucchini2.jpg',49);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (147,'Yellow Zucchini3.jpg',49);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (50, 'Watercress', 14.15, 30, 'Y', 'Watercress adds Sharp, bold, and bitter, with parsley''s juiciness and fresh flavor. It mellows when cooked, so enjoy this nutrient-packed green in soups and sautés.', '', 0, 3, 1, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (148,'Watercress1.jpg',50);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (149,'Watercress2.jpg',50);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (150,'Watercress3.jpg',50);

    -----Category Herbs 9---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (9, 'Herbs', 'Herbs are plants with savory or aromatic properties that are used for flavoring and garnishing food, for medicinal purposes or for fragrances and more to elevate the flavors in your dish.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (51, 'Basil', 4.55, 30, 'Y', 'Sweet, fragrant, and aromatic. The taste of our sweet basil is like a bouquet with hints of mint, clove, and licorice. Basil is a widely used culinary herb and has multiple health benefits.', '', 0, 3, 9, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (151,'Basil1.jpg',51);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (152,'Basil2.jpg',51);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (153,'Basil3.jpg',51);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (52, 'Bay Leaves', 3.55, 30, 'Y', 'The bay leaf is an aromatic leaf commonly used in cooking. Bay leaves are a rich source of vitamin A, vitamin C, iron, potassium, calcium, and magnesium.', '', 0, 3, 9, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (154,'Bay Leaves1.jpg',52);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (155,'Bay Leaves2.jpg',52);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (156,'Bay Leaves3.jpg',52);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (53, 'Bean Sprouts', 6.75, 30, 'Y', 'With a high-water content, bean sprouts offer a crunch and subtle nutty flavor. We take a very good care while growing and preparing sprouts in order to prevent contamination. Bean sprouts are low in calories and rich in fiber and Vitamins B, C and K and also rich in iron.', '', 0, 3, 9, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (157,'Bean Sprouts1.jpg',53);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (158,'Bean Sprouts2.jpg',53);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (159,'Bean Sprouts3.jpg',53);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (54, 'Galangal', 9.75, 30, 'Y', 'Galangal is also known as Thai ginger and the skin of galangal is smoother and paler and also the flavor galangal is strong; it’s earthy, sharp, and extra citrusy.', '', 0, 3, 9, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (160,'Galangal1.jpg',54);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (161,'Galangal2.jpg',54);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (162,'Galangal3.jpg',54);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (55, 'Lemon Grass', 4.25, 30, 'Y', 'Lemon Grass is a culinary herb that comes with an excellent aroma. The subtle lemony aroma of this woody herb works wonders with chicken, fish, shellfish, curries, soups, and marinades.', '', 0, 3, 9, sysdate);
            
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (163,'Lemon Grass1.jpg',55);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (164,'Lemon Grass2.jpg',55);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (165,'Lemon Grass3.jpg',55);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (56, 'Mint', 3.50, 30, 'Y', 'Mint leaves are tender herbs with gentle stems and have a distinct pleasant aroma, pleasing taste, cool after-sensation, and medicinal qualities. They are best used raw or added at the end of cooking in order to maintain their delicate flavor and texture.', '', 0, 3, 9, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (166,'Mint1.jpg',56);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (167,'Mint2.jpg',56);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (168,'Mint3.jpg',56);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (57, 'Oregano', 4.75, 30, 'Y', 'Pungent and freshly herbal, oregano is a signature ingredient in the foods. It can be used to add flavor to grills, salads, sauces, pasta etc.', '', 0, 3, 9, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (169,'Oregano1.jpg',57);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (170,'Oregano2.jpg',57);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (171,'Oregano3.jpg',57);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (58, 'Tarragon', 8.35, 30, 'Y', 'Dark green and lean, fresh tarragon packs a distinctive anise flavor that is mild but sneaks up on you. Fresh tarragon works especially well in dishes with eggs, tomatoes, chicken, fish, and lobster.', '', 0, 3, 9, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (172,'Tarragon1.jpg',58);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (173,'Tarragon2.jpg',58);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (174,'Tarragon3.jpg',58);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (59, 'Turmeric', 3.25, 30, 'Y', 'The best thing about fresh turmeric is that is pleasantly mild and does not have any sharp taste. Fresh turmeric is known to have both anti-inflammatory and antioxidant properties.', '', 0, 3, 9, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (175,'Turmeric1.jpg',59);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (176,'Turmeric2.jpg',59);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (177,'Turmeric3.jpg',59);

    -----Category Salad 10---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (10, 'Salad', 'Salads can provide a wide array of nutrients. ');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (60, 'Avocados', 12.25, 30, 'Y', 'Avocado is an oval-shaped fruit with thick green and a bumpy, leathery outer skin. They have a unique texture and are rich in vitamins and low in cholesterol.', '', 0, 3, 10, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (178,'Avocados1.jpg',60);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (179,'Avocados2.jpg',60);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (180,'Avocados3.jpg',60);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (61, 'Baby Bliss Tomatoes', 13.50, 30, 'Y', 'Naturally grown baby bliss tomatoes which have been produced without any chemicals and are not coated with pesticides are simple perfect for salads.', '', 0, 3, 10, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (181,'Baby Bliss Tomatoes1.jpg',61);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (182,'Baby Bliss Tomatoes2.jpg',61);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (183,'Baby Bliss Tomatoes3.jpg',61);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (62, 'Cucumber', 6.75, 30, 'Y', 'With high water content and crunchy flesh, Cucumber keep our body hydrated and has good amount of fiber and minerals.', '', 0, 3, 10, sysdate);
            
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (184,'Cucumber1.jpg',62);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (185,'Cucumber2.jpg',62);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (186,'Cucumber3.jpg',62);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (63, 'Iceberg Lettuce', 8.50, 30, 'Y', 'Iceberg lettuce is a variety of lettuce with crisp leaves which grows in a spherical head resembling a cabbage. They have a high-water content, making it a refreshing choice during hot weather.', '', 0, 3, 10, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (187,'Iceberg Lettuce1.jpg',63);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (188,'Iceberg Lettuce2.jpg',63);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (189,'Iceberg Lettuce3.jpg',63);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (64, 'Cooked Beetroot Pack', 14.75, 30, 'Y', 'These edible ruby red roots are smooth and bulbous. Beetroots have the highest sugar content than any other vegetable.', '', 0, 3, 10, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (190,'Cooked Beetroot Pack1.jpg',64);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (191,'Cooked Beetroot Pack2.jpg',64);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (192,'Cooked Beetroot Pack3.jpg',64);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (65, 'Mixed Salad Bag', 13.25, 30, 'Y', 'Fresh Cut Green Salad is hand-picked and selected Fresh Vegetables are washed, cut and packed in hygienic conditions through an automated process.', '', 0, 3, 10, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (193,'Mixed Salad Bag1.jpg',65);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (194,'Mixed Salad Bag2.jpg',65);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (195,'Mixed Salad Bag3.jpg',65);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (66, 'Kale', 9.25, 30, 'Y', 'Kale is a crisp and hearty vegetable, with a hint of earthiness. Kale is considered a superfood as it is nutrient-dense while being very low in calories and packs more nutrition than almost any other whole food.', '', 0, 3, 10, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (196,'Kale1.jpg',66);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (197,'Kale2.jpg',66);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (198,'Kale3.jpg',66);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (67, 'Roquette', 7.75, 30, 'Y', 'Roquette, are tender and bite-sized with a tangy flavor. Along with other leafy greens, it contains high levels of beneficial nitrates and polyphenols.', '', 0, 3, 10, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (199,'Roquette1.jpg',67);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (200,'Roquette2.jpg',67);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (201,'Roquette3.jpg',67);

----------------------------------------------------FISHMONGER--------------------------------------------------------------------------------------------
    -----Shops---------    
        INSERT INTO SHOPS (shop_id,shop_name,address,contact,trader_id) 
        VALUES (5,'Fishmongers Shop','Cleckhuddersfax',9876463210,fishmonger_id);

        INSERT INTO SHOPS (shop_id,shop_name,address,contact,trader_id) 
        VALUES (6,'Fishmongers Fresh Shop','Cleckhuddersfax',9876463210,fishmonger_id);

    -----Category Seafood 11---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (11, 'Seafood', 'Seafood is any form of sea life regarded as food by humans, prominently including fish and shellfish.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (68, 'Maine Lobster', 11.25, 30, 'Y', 'This is the soft green or red substance which is found in the body cavity of Lobsters that fulfills the function of both the liver and pancreas. It is perfectly safe to eat and is often considered a delicacy.', '', 0, 5, 11, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (202,'Maine Lobster1.jpg',68);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (203,'Maine Lobster2.jpg',68);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (204,'Maine Lobster3.jpg',68);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (69, 'Wild Caught Yellowfin Tuna', 25.25, 30, 'Y', 'Tuna is an excellent source of vitamin B12, an essential vitamin needed to make DNA. Vitamin B12 also helps you to form new red blood cells and prevent the development of anemia.', '', 0, 5, 11, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (205,'Wild Caught Yellowfin Tuna1.jpg',69);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (206,'Wild Caught Yellowfin Tuna2.jpg',69);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (207,'Wild Caught Yellowfin Tuna3.jpg',69);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (70, 'Black COD Fillet Steaks', 27.35, 30, 'Y', 'Black Cod are rich in Omega 3 fatty acids, which boost your immune system and can help lower blood pressure. With great flavor, a versatility of cooking applications, and various health benefits, there''s no reason Black Cod shouldn''t be on your plate!', '', 0, 5, 11, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (208,'Black COD Fillet Steaks1.jpg',70);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (209,'Black COD Fillet Steaks2.jpg',70);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (210,'Black COD Fillet Steaks3.jpg',70);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (71, 'Mussels', 15.55, 30, 'Y', 'They keep your heartbeat regular, lower blood pressure, and help blood vessels work as they should. Mussels are rich in the marine Omega-3s, EPA and DHA. If you are trying to lose weight, mussels give you a lot of nutrition without a lot of calories.', '', 0, 5, 11, sysdate);
            
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (211,'Mussels1.jpg',71);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (212,'Mussels2.jpg',71);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (213,'Mussels3.jpg',71);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (72, 'Baby Squid', 19.25, 30, 'Y', 'Firm, pearly-colored body. They are small, so the whole squid can be eaten. Simply clean them. Baby squid can be used like calamari and octopus: grilled, marinated, fried, sautéed or used in fritters or salads.', '', 0, 5, 11, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (214,'Baby Squid1.jpg',72);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (215,'Baby Squid2.jpg',72);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (216,'Baby Squid3.jpg',72);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (73, 'Sea Urchin Roe (Uni)', 18.75, 30, 'Y', 'Uni is actually the sex organ that produces roe, sometimes referred to as the gonads or corals.High in protein.A good source of fiber.A healthy source of Vitamin A, Vitamin E, calcium, and iodine.Promote good blood circulation.A great snack for those watching their weight. It''s low in fat and carbohydrates, and contains only about 125 calories per 2-3 pieces.', '', 0, 5, 11, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (217,'Sea Urchin Roe (Uni)1.jpg',73);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (218,'Sea Urchin Roe (Uni)2.jpg',73);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (219,'Sea Urchin Roe (Uni)3.jpg',73);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (74, 'Abalone', 25.75, 30, 'Y', 'Abalone is a species of shellfish (mollusks) from the Haliotidae family. Abalone has been found to contain bioactive compounds that exhibit anti-oxidant, anti-thrombotic, anti-inflammatory, anti-microbial and anti-cancer activities', '', 0, 5, 11, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (220,'Abalone1.jpg',74);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (221,'Abalone2.jpg',74);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (222,'Abalone3.jpg',74);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (75, 'Wild Sockeye Salmon', 16.75, 30, 'Y', 'Sockeye salmon is a species of wild salmon found in the northern pacific ocean and the rivers that feed into it. They are fresh water as juveniles but migrate to the ocean. You may also see them called red salmon or blueback salmon. Rich in Omega-3 Fatty Acids. Great Source of Protein. High in B Vitamins.Good Source of Potassium. Loaded With Selenium.Contains the Antioxidant Astaxanthin. May Reduce the Risk of Heart Disease. ', '', 0, 5, 11, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (223,'Wild Sockeye Salmon1.jpg',75);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (224,'Wild Sockeye Salmon2.jpg',75);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (225,'Wild Sockeye Salmon3.jpg',75);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (76, 'Farm-Raised Osetra Caviar Malossol', 25.75, 30, 'Y', 'Osetra caviar has large to medium-size grains. It has a unique clean, crisp, "nutty" flavor and varies in color from golden yellow to dark brown. Many consider it the best tasting sturgeon caviar of all. Caviar is an excellent source of vitamin B12 and the fatty acids DHA and EPA. It also provides selenium, iron, and sodium, among other vitamins and minerals.', '', 0, 5, 11, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (226,'Farm-Raised Osetra Caviar Malossol1.jpg',76);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (227,'Farm-Raised Osetra Caviar Malossol2.jpg',76);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (228,'Farm-Raised Osetra Caviar Malossol3.jpg',76);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (77, 'King Crab Legs', 22.25, 30, 'Y', 'The claw and leg meat is somewhat firmer than the body meat. Crab is packed with protein, which is important for building and maintaining muscle. Crab also contains high levels of omega-3 fatty acids, vitamin B12, and selenium. These nutrients play vital roles in improving general health while helping prevent a variety of chronic conditions.', '', 0, 5, 11, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (229,'King Crab Legs1.jpg',77);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (230,'King Crab Legs2.jpg',77);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (231,'King Crab Legs3.jpg',77);

----------------------------------------------------BAKERY----------------------------------------------------------------------------------------------

    -----Shops---------    
        INSERT INTO SHOPS (shop_id,shop_name,address,contact,trader_id) 
        VALUES (7,'Bakery Shop','Cleckhuddersfax',9876463210,bakery_id);

        INSERT INTO SHOPS (shop_id,shop_name,address,contact,trader_id) 
        VALUES (8,'Bakery Fresh Shop','Cleckhuddersfax',9876463210,bakery_id);

    -----Category Viennoiserie 12---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (12, 'Viennoiserie', 'Viennoiseries are baked goods made from a yeast-leavened dough in a manner similar to bread, or from puff pastry, but with added ingredients, which give them a richer, sweeter character that approaches that of pastry.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (78, 'Croissant', 5.55, 30, 'Y', 'A croissant is a buttery, flaky, viennoiserie pastry of Austrian origin, but mostly associated with France. Including croissants in a healthy eating plan is possible, but may take some portion control. The popular baked good is relatively high in calories and because they are made with butter, they also provide saturated fat.', 'Allergen: Milk, egg, yeast', 0, 7, 12, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (232,'Croissant1.jpg',78);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (233,'Croissant2.jpg',78);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (234,'Croissant3.jpg',78);
            
        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (79, 'Pain Au Chocolat', 6.25, 30, 'Y', 'Pain au chocolat, also known as chocolatine in the south-west part of France and in Quebec, is a type of viennoiserie sweet roll consisting of a cuboid-shaped piece of yeast-leavened laminated dough, similar in texture to a puff pastry, with one or two pieces of dark chocolate in the centre.', ' Contains wheat, soya, milk and egg. Our bakers prepare a variety of products in our bakeries, this product may also contain peanuts, nuts, milk, egg, soya and other allergens., For allergens, including cereals containing gluten.', 0, 7, 12, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (235,'Pain Au Chocolat1.jpg',79);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (236,'Pain Au Chocolat2.jpg',79);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (237,'Pain Au Chocolat3.jpg',79);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (80, 'Brownie Style Swirl', 6.25, 30, 'Y', 'This exclusive hybrid filled viennoiserie combines best of French classic puff pastry with the bestselling chocolatey American treat. The perfect solution for a super indulgent breakfast or break.', 'Item contains soya, egg, milk and nuts. If allergic DO NOT CONSUME.', 0, 7, 12, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (238,'Brownie Style Swirl1.jpg',80);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (239,'Brownie Style Swirl2.jpg',80);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (240,'Brownie Style Swirl3.jpg',80);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (81, 'Butter Brioche Leaf', 7.25, 30, 'Y', 'This brioche bread is ultra soft, rich, and buttery! Not only delicious to eat, but easy to make too! Perfect for sandwiches, French toast and just to eat on its own.', 'Allergen: Milk, Egg, Gluten', 0, 7, 12, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (241,'Butter Brioche Leaf1.jpg',81);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (242,'Butter Brioche Leaf2.jpg',81);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (243,'Butter Brioche Leaf3.jpg',81);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (82, 'Vanilla Cruffin', 13.75, 30, 'Y', 'A cruffin is a hybrid of a croissant, a popular French pastry, and a muffin. The pastry is made by proving and baking laminated dough in a muffin mould.', 'Contains Gluten', 0, 7, 12, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (244,'Vanilla Cruffin1.jpg',82);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (245,'Vanilla Cruffin2.jpg',82);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (246,'Vanilla Cruffin3.jpg',82);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (83, 'Blueberry Cruffin', 14.25, 30, 'Y', 'A cruffin is a hybrid of a croissant, a popular French pastry, and a muffin. The pastry is made by proving and baking laminated dough in a muffin mould.', 'Contains Gluten', 0, 7, 12, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (247,'Blueberry Cruffin1.jpg',83);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (248,'Blueberry Cruffin2.jpg',83);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (249,'Blueberry Cruffin3.jpg',83);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (84, 'Sausage Roll', 9.75, 30, 'Y', 'A sausage roll is a savoury pastry snack, popular in current and former Commonwealth nations.', 'Contains Milk and egg', 0, 7, 12, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (250,'Sausage Roll1.jpg',84);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (251,'Sausage Roll2.jpg',84);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (252,'Sausage Roll3.jpg',84);

    -----Category Bread 13---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (13, 'Bread', 'Bread is a staple food prepared from a dough of flour and water, usually by baking.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (85, 'Milk Bread', 15.25, 30, 'Y', 'Soft and incredibly fluffy Japanese Milk Bread is world famous! It’s also really easy to make at home. Get the step by step recipe to make this flavorful sandwich bread right here.', 'Contains Milk, Egg, Gluten', 0, 7, 13, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (253,'Milk Bread1.jpg',85);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (254,'Milk Bread2.jpg',85);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (255,'Milk Bread3.jpg',85);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (86, 'Multigrain', 25.50, 30, 'Y', 'Multigrain bread is a type of bread prepared with two or more types of grain.', 'Allergens: Sesame seeds', 0, 7, 13, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (256,'Multigrain1.jpg',86);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (257,'Multigrain2.jpg',86);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (258,'Multigrain3.jpg',86);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (87, 'Gluten Free Bread', 30.25, 30, 'Y', 'A homemade gluten-free bread recipe that''s simple to make, dairy-free, and bakes into the best gluten-free bread. Make your next lunch with this easy gluten-free sandwich bread. A gluten-free diet can provide many health benefits, especially for those with celiac disease. It may help ease digestive symptoms, reduce chronic inflammation, boost energy and promote weight loss.', '', 0, 7, 13, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (259,'Gluten Free Bread1.jpg',87);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (260,'Gluten Free Bread2.jpg',87);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (261,'Gluten Free Bread3.jpg',87);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (88, 'Rye Bread', 30.25, 30, 'Y', 'Rye bread is a type of bread made with various proportions of flour from rye grain. It can be light or dark in color, depending on the type of flour used and the addition of coloring agents, and is typically denser than bread made from wheat flour. Rye bread has been linked to many potential health benefits, including weight loss, reduced inflammation, better blood sugar control, and improved heart and digestive health.', 'Contains Gluten', 0, 7, 13, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (262,'Rye Bread1.jpg',88);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (263,'Rye Bread2.jpg',88);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (264,'Rye Bread3.jpg',88);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (89, 'Baguettes', 8.25, 30, 'Y', 'A baguette is a long, thin type of bread of French origin that is commonly made from basic lean dough. It is distinguishable by its length and crisp crust.', 'Allergens: Wheat, Sesame seeds', 0, 7, 13, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (265,'Baguettes1.jpg',89);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (266,'Baguettes2.jpg',89);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (267,'Baguettes3.jpg',89);


----------------------------------------------------DELICATESSEN----------------------------------------------------------------------------------------------
    -----Shops---------    
        INSERT INTO SHOPS (shop_id,shop_name,address,contact,trader_id) 
        VALUES (9,'Delicatessen Shop','Cleckhuddersfax',9876463210,delicatessen_id);

        INSERT INTO SHOPS (shop_id,shop_name,address,contact,trader_id) 
        VALUES (10,'Delicatessen Fresh Shop','Cleckhuddersfax',9876463210,delicatessen_id);

      -----Category Cheese 14---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (14, 'Cheese', 'Cheese ranges from very soft to very hard, with semi-soft, firm, and hard somewhere in between the two extremes. The higher the moisture and milk fat of a cheese, the smoother the mouthfeel.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (90, 'Beauvale', 6.50, 30, 'Y', 'Beauvale has been carefully developed to perfection. We love its soft, spreadable texture and mellow flavor. Beauvale is perfect for those who prefer a milder blue flavor.', '', 0, 9, 14, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (268,'Beauvale1.jpg',90);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (269,'Beauvale2.jpg',90);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (270,'Beauvale3.jpg',90);
        
        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (91, 'Blue Murder', 8.50, 30, 'Y', 'This cheese was called Blue Monday after the famous song by Blur. Blue Murder is made by Highland Fine Cheeses in Tain. It is made from cow’s milk, and it matures for up to 8 weeks during which time it develops a mellow, soft, creamy texture. The flavors are savory, slightly sweet, buttery, spicy, and rich. It has a kick of chocolate and malt. Made with pasteurized milk and vegetarian rennet.', '', 0, 9, 14, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (271,'Blue Murder1.jpg',91);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (272,'Blue Murder2.jpg',91);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (273,'Blue Murder3.jpg',91);
        
        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (92, 'Brie de Meaux', 15.25, 30, 'Y', 'Brie de Meaux AOC is a full and fruity raw milk cheese, traditionally made to artisan methods. A soft, unctuous texture with easy yet complex, nutty flavors making this cheese one of our customers’ favorites. The cheeses have a very pale-yellow center when ripe, which becomes gooey and more flavorsome with maturity.', '', 0, 9, 14, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (274,'Brie de Meaux1.jpg',92);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (275,'Brie de Meaux2.jpg',92);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (276,'Brie de Meaux3.jpg',92);
        
        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (93, 'Geitost', 9.25, 30, 'Y', 'Dark brown or honey-brown or golden orange in color, the cheese is non-perishable, dessert cheese sold in blocks. It has a sweet and caramel-like taste and comes with an unusual, aromatic quality. It is sliced paper-thin and placed on Norwegian flatbread. Its sweet, fishy, caramel flavor is simply irresistible!', '', 0, 9, 14, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (277,'Geitost1.jpg',93);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (278,'Geitost2.jpg',93);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (279,'Geitost3.jpg',93);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (94, 'Keens Cheddar', 12.25, 30, 'Y', 'Keen''s is one of the last traditional raw milk artisan Somerset Cheddars. The result is a strong, tangy cheddar with a mellow depth of flavor, with occasional bluing throughout. A standout traditional cheddar that is keeping its place firmly in the classic cheese world.', '', 0, 9, 14, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (280,'Keens Cheddar1.jpg',94);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (281,'Keens Cheddar2.jpg',94);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (282,'Keens Cheddar3.jpg',94);

     -----Category Accompaniments for cheese 15---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (15, 'Accompaniments for cheese', 'Dried or fresh fruit and nuts are classic Cheese Accompaniments. The sweetness of fruit offers a balance to the saltiness of many cheeses.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (95, 'Natural Wafer Crackers', 13.25, 30, 'Y', 'These crackers are baked in small batches for the best quality and light crispy texture. They are a satisfying accompaniment to any entertaining platter. Excellent with cheese dips and other condiments.', 'Contains Milk and Gluten', 0, 9, 15, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (283,'Natural Wafer Crackers1.jpg',95);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (284,'Natural Wafer Crackers2.jpg',95);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (285,'Natural Wafer Crackers3.jpg',95);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (96, 'Cracked Pepper Wafer Crackers', 14.25, 30, 'Y', 'Deliciously tasty... Paired with soft, velvety goat''s cheese and sweet caramelized red onion, with a scattering of aromatic thyme', 'Sesame Seeds, Milk, Gluten and Lupin', 0, 9, 15, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (286,'Cracked Pepper Wafer Crackers1.jpg',96);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (287,'Cracked Pepper Wafer Crackers2.jpg',96);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (288,'Cracked Pepper Wafer Crackers3.jpg',96);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (97, 'Pickled Walnuts', 15.75, 30, 'Y', 'Preserved Black Walnuts are created in a traditional manner, with growing practices that reflect a long-standing respect for the earth. The first step in creating these walnut preserves is when fresh baby walnuts are picked green from the tree before they have a chance to age or grow tough. Cured and then pickled in a malt vinegar solution, these walnuts are completely edible with the shells.', '', 0, 9, 15, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (289,'Pickled Walnuts1.jpg',97);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (290,'Pickled Walnuts2.jpg',97);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (291,'Pickled Walnuts3.jpg',97);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (98, 'Bakehouse Seeded Toasts and Almond', 13.50, 30, 'Y', 'Fig & Almond Seeded Toasts, ideal with cheese, dips and other condiments.', 'Soya, Milk', 0, 9, 15, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (292,'Bakehouse Seeded Toasts and Almond1.jpg',98);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (293,'Bakehouse Seeded Toasts and Almond2.jpg',98);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (294,'Bakehouse Seeded Toasts and Almond3.jpg',98);

     -----Category Ready-to-eat items 16---------
        INSERT INTO PRODUCT_CATEGORIES (category_id, category_name, category_description)
        VALUES (16, 'Ready-to-eat items', 'Ready-to-eat food is food that will not be cooked or reheated before serving. This includes salads, cooked meats, smoked fish, desserts, sandwiches, cheese and food that you have cooked in advance to serve cold.');

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (99, 'Baguette Club Ham Cheese', 12.25, 30, 'Y', 'Half baguette with cooked ham, emmental cheese, cucumber, fresh tomato and soft butter. Cereals containing gluten (wheat).', 'Cereals, Milk', 0, 9, 16, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (295,'Baguette Club Ham Cheese1.jpg',99);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (296,'Baguette Club Ham Cheese2.jpg',99);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (297,'Baguette Club Ham Cheese3.jpg',99);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (100, 'Tuna & Seafood Sandwich', 18.25, 30, 'Y', 'Although every care has been taken to remove all bones, some may remain. Low saturated fat- Reducing consumption of saturated fat contributes to the maintenance of normal blood cholesterol levels. High protein- Protein supports the growth and maintenance of muscle mass. Enjoy as part of a varied and balanced diet and a healthy lifestyle.', 'For allergens, including cereals containing gluten.', 0, 9, 16, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (298,'Tuna & Seafood Sandwich1.jpg',100);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (299,'Tuna & Seafood Sandwich2.jpg',100);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (300,'Tuna & Seafood Sandwich3.jpg',100);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (101, 'Tomato & Italian Cheese Salad Bowl', 15.25, 30, 'Y', 'A mix of baby leaves with baby plum tomatoes, cucumber and sachets of balsamic vinegar dressing and Italian hard cheese. With a balsamic vinegar dressing Suitable for vegeterians.To serve: Mix the Italian cheese and dressing with the salad', '', 0, 9, 16, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (301,'Tomato & Italian Cheese Salad Bowl1.jpg',101);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (302,'Tomato & Italian Cheese Salad Bowl2.jpg',101);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (303,'Tomato & Italian Cheese Salad Bowl3.jpg',101);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (102, 'Filled French Toast', 13.75, 30, 'Y', 'Stuffed to the brim with the flavors of fresh cooked French toast, our Filled French Toast won’t ever let you down.', '', 0, 9, 16, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (304,'Filled French Toast1.jpg',102);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (305,'Filled French Toast2.jpg',102);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (306,'Filled French Toast3.jpg',102);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (103, 'Spicy Black Bean Chilly', 12.25, 30, 'Y', 'A rich and smoky Black Bean chili with pulled jackfruit, protein packed black beans and a chipotle kick.', '', 0, 9, 16, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (307,'Spicy Black Bean Chilly1.jpg',103);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (308,'Spicy Black Bean Chilly2.jpg',103);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (309,'Spicy Black Bean Chilly3.jpg',103);

        INSERT INTO PRODUCTS (product_id, product_name, price, quantity, availability, description, allergy_information, discount, shop_id, category_id, added_date)
        VALUES (104, 'Creamy Coconut Fish Pie', 25.75, 30, 'Y', 'Our decadent fish pie is a real crowd-pleaser, brimming with sustainably sourced fish chunks and lashings of coconut milk. We’ve snuck in healthy greens too: leafy spinach and green beans.', 'Produced in a facility that handles gluten, milk, nuts and sesame.', 0, 9, 16, sysdate);

            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (310,'Creamy Coconut Fish Pie1.jpg',104);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (311,'Creamy Coconut Fish Pie2.jpg',104);
            INSERT INTO PRODUCT_IMAGES (product_image_id, image_name, product_id)
            VALUES (312,'Creamy Coconut Fish Pie3.jpg',104);
END;
/




-- FUNCTION
--PAYMENTS
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (1,'PAYPAL',136.37,'07/09/2021',11,'4WV49126VC055164C','YAQHDV2ZXYUAS');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (2,'PAYPAL',115.7,'07/10/2021',11,'4XE57994HM851264V','YAQHDV2ZXYUAS');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (3,'PAYPAL',118.65,'07/10/2021',11,'2R456996GX104542N','YAQHDV2ZXYUAS');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (4,'PAYPAL',82.2,'07/10/2021',44,'69645887KW135674B','4KN9J9RKSNREL');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (5,'PAYPAL',151.95,'07/10/2021',44,'5WW11236SM381350B','4KN9J9RKSNREL');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (6,'PAYPAL',54,'07/10/2021',44,'6SB35763503674910','4KN9J9RKSNREL');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (7,'PAYPAL',80.5,'07/10/2021',44,'8PA30506R76709311','4KN9J9RKSNREL');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (8,'PAYPAL',64.3,'07/10/2021',44,'5Y861201YK025653S','4KN9J9RKSNREL');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (9,'PAYPAL',76.1,'07/10/2021',11,'41003609NH745771M','YAQHDV2ZXYUAS');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (10,'PAYPAL',72.35,'07/10/2021',11,'0NM14389733817608','YAQHDV2ZXYUAS');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (11,'PAYPAL',51.75,'07/10/2021',41,'7AM00177CG3359325','YAQHDV2ZXYUAS');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (12,'PAYPAL',118.35,'07/10/2021',41,'80444920LE0703523','YAQHDV2ZXYUAS');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (13,'PAYPAL',156.65,'07/10/2021',41,'6BW67510D8833601H','YAQHDV2ZXYUAS');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (14,'PAYPAL',130.15,'07/10/2021',41,'22A53059EA477533N','YAQHDV2ZXYUAS');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (15,'PAYPAL',94,'07/10/2021',41,'1UK97138BP442145V','YAQHDV2ZXYUAS');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (16,'PAYPAL',118.15,'07/10/2021',43,'78Y68207CL792141T','YAQHDV2ZXYUAS');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (17,'PAYPAL',58.97,'07/10/2021',43,'7MW44787DE758310H','YAQHDV2ZXYUAS');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (18,'PAYPAL',152.5,'07/10/2021',43,'52X39991C2686444K','YAQHDV2ZXYUAS');
INSERT INTO PAYMENTS(PAYMENT_ID,PAYMENT_TYPE,AMOUNT,PAYMENT_DATE,USER_ID,PAYPAL_ORDER_ID,PAYPAL_PAYER_ID) VALUES (19,'PAYPAL',124.95,'07/10/2021',43,'4R9947560T4020158','YAQHDV2ZXYUAS');

--ORDER
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (1,'07/09/2021','07/14/2021',2,1,2);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (2,'07/10/2021','07/14/2021',2,2,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (3,'07/10/2021','07/14/2021',1,3,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (4,'07/10/2021','07/15/2021',5,4,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (5,'07/10/2021','07/15/2021',5,5,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (6,'07/10/2021','07/16/2021',8,6,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (7,'07/10/2021','07/15/2021',5,7,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (8,'07/10/2021','07/14/2021',1,8,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (9,'07/10/2021','07/14/2021',2,9,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (10,'07/10/2021','07/15/2021',6,10,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (11,'07/10/2021','07/16/2021',8,11,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (12,'07/10/2021','07/14/2021',3,12,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (13,'07/10/2021','07/15/2021',6,13,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (14,'07/10/2021','07/16/2021',7,14,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (15,'07/10/2021','07/15/2021',5,15,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (16,'07/10/2021','07/15/2021',6,16,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (17,'07/10/2021','07/15/2021',6,17,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (18,'07/10/2021','07/15/2021',6,18,NULL);
INSERT INTO ORDERS(ORDER_ID,ORDERED_DATE,COLLECTION_DATE,COLLECTION_SLOT_ID,PAYMENT_ID,VOUCHER_ID) VALUES (19,'07/10/2021','07/16/2021',7,19,NULL);

----Order Products
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (1,3,17,1);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (2,1,70,1);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (3,1,76,1);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (4,1,67,1);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (5,1,55,1);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (6,2,73,2);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (7,2,71,2);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (8,1,76,2);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (9,1,83,2);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (10,2,52,2);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (11,1,87,3);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (12,1,88,3);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (13,1,20,3);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (14,1,23,3);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (15,1,85,3);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (16,1,18,4);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (17,1,39,4);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (18,1,48,4);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (19,1,15,4);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (20,1,46,4);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (21,1,74,5);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (22,1,20,5);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (23,1,28,5);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (24,1,21,5);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (25,1,27,5);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (26,1,25,5);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (27,1,60,5);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (28,1,63,5);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (29,1,72,6);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (30,1,75,6);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (31,1,68,6);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (32,1,62,6);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (33,1,77,7);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (34,1,100,7);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (35,1,101,7);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (36,1,99,7);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (37,1,79,7);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (38,1,80,7);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (39,1,64,8);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (40,1,50,8);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (41,1,49,8);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (42,1,61,8);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (43,1,58,8);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (44,1,69,9);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (45,1,42,9);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (46,1,49,9);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (47,1,47,9);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (48,1,58,9);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (49,1,29,9);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (50,2,75,10);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (51,1,21,10);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (52,1,53,10);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (53,1,51,10);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (54,1,52,10);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (55,1,56,10);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (56,1,31,11);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (57,1,36,11);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (58,1,58,11);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (59,1,30,11);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (60,1,53,11);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (61,1,29,11);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (62,1,56,11);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (63,1,70,12);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (64,1,76,12);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (65,1,72,12);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (66,1,64,12);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (67,1,82,12);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (68,1,84,12);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (69,1,67,12);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (70,3,76,13);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (71,1,70,13);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (72,1,69,13);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (73,1,71,13);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (74,1,68,13);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (75,3,64,14);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (76,2,49,14);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (77,2,32,14);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (78,2,31,14);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (79,1,54,14);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (80,1,58,14);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (81,2,99,15);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (82,2,93,15);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (83,1,101,15);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (84,1,102,15);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (85,1,98,15);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (86,1,91,15);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (87,1,70,16);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (88,1,77,16);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (89,3,62,16);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (90,1,71,16);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (91,1,60,16);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (92,1,68,16);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (93,1,66,16);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (94,3,5,17);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (95,2,6,17);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (96,1,9,17);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (97,1,7,17);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (98,3,97,18);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (99,1,87,18);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (100,2,64,18);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (101,2,91,18);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (102,1,92,18);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (103,1,65,18);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (104,3,42,19);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (105,3,43,19);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (106,1,72,19);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (107,2,63,19);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (108,1,49,19);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (109,1,58,19);
INSERT INTO ORDER_PRODUCTS(ORDER_PRODUCT_ID,QUANTITY,PRODUCT_ID,ORDER_ID) VALUES (110,1,29,19);

--Notifications
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (1,'/public/images/notif-order.png','Order Placed','Your order of 7 items is being placed. You can collect them from collection slot after 2021-07-14.','From efoodbasket','#','07/09/2021','Y',11);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (2,'/public/images/notif-order.png','Order Received','Customer has ordered 3 items of Hokkaido Wagyu Beef A5 Wagyu Beef Assortment Steaks from your shop.','From efoodbasket','/products/17/','07/09/2021','Y',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (3,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Black COD Fillet Steaks from your shop.','From efoodbasket','/products/70/','07/09/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (4,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Farm-Raised Osetra Caviar Malossol from your shop.','From efoodbasket','/products/76/','07/09/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (5,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Roquette from your shop.','From efoodbasket','/products/67/','07/09/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (6,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Lemon Grass from your shop.','From efoodbasket','/products/55/','07/09/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (7,'/public/images/notif-question.png','Query about product','You have recieved a query on your product: "Is this a farm raised product?"''','From Srijan Panta','/products/5/?is_notif=true&query_id=1','07/09/2021','Y',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (8,'/public/images/notif-answer.png','Answer about product','Your query about a product is answerd: "Yes, this is farm raised product."''','From Fresh Butchers','/products/5/?is_notif=true&query_id=1','07/09/2021','N',11);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (9,'/public/images/notif-order.png','Order Placed','Your order of 8 items is being placed. You can collect them from collection slot after 2021-07-14.','From efoodbasket','#','07/10/2021','N',11);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (10,'/public/images/notif-order.png','Order Received','Customer has ordered 2 items of Sea Urchin Roe (Uni) from your shop.','From efoodbasket','/products/73/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (11,'/public/images/notif-order.png','Order Received','Customer has ordered 2 items of Mussels from your shop.','From efoodbasket','/products/71/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (12,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Farm-Raised Osetra Caviar Malossol from your shop.','From efoodbasket','/products/76/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (13,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Blueberry Cruffin from your shop.','From efoodbasket','/products/83/','07/10/2021','N',7);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (14,'/public/images/notif-order.png','Order Received','Customer has ordered 2 items of Bay Leaves from your shop.','From efoodbasket','/products/52/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (15,'/public/images/notif-order.png','Order Placed','Your order of 5 items is being placed. You can collect them from collection slot after 2021-07-14.','From efoodbasket','#','07/10/2021','N',11);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (16,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Gluten Free Bread from your shop.','From efoodbasket','/products/87/','07/10/2021','N',7);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (17,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Rye Bread from your shop.','From efoodbasket','/products/88/','07/10/2021','N',7);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (18,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Whole Pekin Duck from your shop.','From efoodbasket','/products/20/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (19,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Cured Duck Salami from your shop.','From efoodbasket','/products/23/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (20,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Milk Bread from your shop.','From efoodbasket','/products/85/','07/10/2021','N',7);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (21,'/public/images/notif-order.png','Order Placed','Your order of 5 items is being placed. You can collect them from collection slot after 2021-07-15.','From efoodbasket','#','07/10/2021','N',44);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (22,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Miyazakigyu A5 Wagyu Beef Coulotte from your shop.','From efoodbasket','/products/18/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (23,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Asparagus from your shop.','From efoodbasket','/products/39/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (24,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Shitake Mushroom from your shop.','From efoodbasket','/products/48/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (25,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Hokkaido Wagyu Beef A5 Ribeye Steak from your shop.','From efoodbasket','/products/15/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (26,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Bok Choy from your shop.','From efoodbasket','/products/46/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (27,'/public/images/notif-order.png','Order Placed','Your order of 8 items is being placed. You can collect them from collection slot after 2021-07-15.','From efoodbasket','#','07/10/2021','N',44);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (28,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Abalone from your shop.','From efoodbasket','/products/74/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (29,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Whole Pekin Duck from your shop.','From efoodbasket','/products/20/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (30,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Minced Turkey from your shop.','From efoodbasket','/products/28/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (31,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Duck Prosciutto (Uncured Duck Breast) from your shop.','From efoodbasket','/products/21/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (32,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Turkey Crown from your shop.','From efoodbasket','/products/27/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (33,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Turkey Butterfly from your shop.','From efoodbasket','/products/25/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (34,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Avocados from your shop.','From efoodbasket','/products/60/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (35,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Iceberg Lettuce from your shop.','From efoodbasket','/products/63/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (36,'/public/images/notif-order.png','Order Placed','Your order of 4 items is being placed. You can collect them from collection slot after 2021-07-16.','From efoodbasket','#','07/10/2021','N',44);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (37,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Baby Squid from your shop.','From efoodbasket','/products/72/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (38,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Wild Sockeye Salmon from your shop.','From efoodbasket','/products/75/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (39,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Maine Lobster from your shop.','From efoodbasket','/products/68/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (40,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Cucumber from your shop.','From efoodbasket','/products/62/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (41,'/public/images/notif-order.png','Order Placed','Your order of 6 items is being placed. You can collect them from collection slot after 2021-07-15.','From efoodbasket','#','07/10/2021','N',44);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (42,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of King Crab Legs from your shop.','From efoodbasket','/products/77/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (43,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Tuna & Seafood Sandwich from your shop.','From efoodbasket','/products/100/','07/10/2021','N',8);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (44,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Tomato & Italian Cheese Salad Bowl from your shop.','From efoodbasket','/products/101/','07/10/2021','N',8);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (45,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Baguette Club Ham Cheese from your shop.','From efoodbasket','/products/99/','07/10/2021','N',8);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (46,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Pain Au Chocolat from your shop.','From efoodbasket','/products/79/','07/10/2021','N',7);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (47,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Brownie Style Swirl from your shop.','From efoodbasket','/products/80/','07/10/2021','N',7);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (48,'/public/images/notif-order.png','Order Placed','Your order of 5 items is being placed. You can collect them from collection slot after 2021-07-14.','From efoodbasket','#','07/10/2021','N',44);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (49,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Cooked Beetroot Pack from your shop.','From efoodbasket','/products/64/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (50,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Watercress from your shop.','From efoodbasket','/products/50/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (51,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Yellow Zucchini from your shop.','From efoodbasket','/products/49/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (52,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Baby Bliss Tomatoes from your shop.','From efoodbasket','/products/61/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (53,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Tarragon from your shop.','From efoodbasket','/products/58/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (54,'/public/images/notif-order.png','Order Placed','Your order of 6 items is being placed. You can collect them from collection slot after 2021-07-14.','From efoodbasket','#','07/10/2021','N',11);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (55,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Wild Caught Yellowfin Tuna from your shop.','From efoodbasket','/products/69/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (56,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Brussel Sprouts from your shop.','From efoodbasket','/products/42/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (57,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Yellow Zucchini from your shop.','From efoodbasket','/products/49/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (58,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Eggplant from your shop.','From efoodbasket','/products/47/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (59,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Tarragon from your shop.','From efoodbasket','/products/58/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (60,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Figs from your shop.','From efoodbasket','/products/29/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (61,'/public/images/notif-order.png','Order Placed','Your order of 7 items is being placed. You can collect them from collection slot after 2021-07-15.','From efoodbasket','#','07/10/2021','N',11);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (62,'/public/images/notif-order.png','Order Received','Customer has ordered 2 items of Wild Sockeye Salmon from your shop.','From efoodbasket','/products/75/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (63,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Duck Prosciutto (Uncured Duck Breast) from your shop.','From efoodbasket','/products/21/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (64,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Bean Sprouts from your shop.','From efoodbasket','/products/53/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (65,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Basil from your shop.','From efoodbasket','/products/51/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (66,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Bay Leaves from your shop.','From efoodbasket','/products/52/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (67,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Mint from your shop.','From efoodbasket','/products/56/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (68,'/public/images/notif-order.png','Order Placed','Your order of 7 items is being placed. You can collect them from collection slot after 2021-07-16.','From efoodbasket','#','07/10/2021','N',41);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (69,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Passion Fruit from your shop.','From efoodbasket','/products/31/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (70,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Raspberry from your shop.','From efoodbasket','/products/36/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (71,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Tarragon from your shop.','From efoodbasket','/products/58/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (72,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Cantaloupe from your shop.','From efoodbasket','/products/30/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (73,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Bean Sprouts from your shop.','From efoodbasket','/products/53/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (74,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Figs from your shop.','From efoodbasket','/products/29/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (75,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Mint from your shop.','From efoodbasket','/products/56/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (76,'/public/images/notif-order.png','Order Placed','Your order of 7 items is being placed. You can collect them from collection slot after 2021-07-14.','From efoodbasket','#','07/10/2021','N',41);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (77,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Black COD Fillet Steaks from your shop.','From efoodbasket','/products/70/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (78,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Farm-Raised Osetra Caviar Malossol from your shop.','From efoodbasket','/products/76/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (79,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Baby Squid from your shop.','From efoodbasket','/products/72/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (80,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Cooked Beetroot Pack from your shop.','From efoodbasket','/products/64/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (81,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Vanilla Cruffin from your shop.','From efoodbasket','/products/82/','07/10/2021','N',7);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (82,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Sausage Roll from your shop.','From efoodbasket','/products/84/','07/10/2021','N',7);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (83,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Roquette from your shop.','From efoodbasket','/products/67/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (84,'/public/images/notif-order.png','Order Placed','Your order of 7 items is being placed. You can collect them from collection slot after 2021-07-15.','From efoodbasket','#','07/10/2021','N',41);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (85,'/public/images/notif-order.png','Order Received','Customer has ordered 3 items of Farm-Raised Osetra Caviar Malossol from your shop.','From efoodbasket','/products/76/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (86,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Black COD Fillet Steaks from your shop.','From efoodbasket','/products/70/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (87,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Wild Caught Yellowfin Tuna from your shop.','From efoodbasket','/products/69/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (88,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Mussels from your shop.','From efoodbasket','/products/71/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (89,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Maine Lobster from your shop.','From efoodbasket','/products/68/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (90,'/public/images/notif-order.png','Order Placed','Your order of 11 items is being placed. You can collect them from collection slot after 2021-07-16.','From efoodbasket','#','07/10/2021','N',41);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (91,'/public/images/notif-order.png','Order Received','Customer has ordered 3 items of Cooked Beetroot Pack from your shop.','From efoodbasket','/products/64/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (92,'/public/images/notif-order.png','Order Received','Customer has ordered 2 items of Yellow Zucchini from your shop.','From efoodbasket','/products/49/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (93,'/public/images/notif-order.png','Order Received','Customer has ordered 2 items of Kiwis from your shop.','From efoodbasket','/products/32/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (94,'/public/images/notif-order.png','Order Received','Customer has ordered 2 items of Passion Fruit from your shop.','From efoodbasket','/products/31/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (95,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Galangal from your shop.','From efoodbasket','/products/54/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (96,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Tarragon from your shop.','From efoodbasket','/products/58/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (97,'/public/images/notif-order.png','Order Placed','Your order of 8 items is being placed. You can collect them from collection slot after 2021-07-15.','From efoodbasket','#','07/10/2021','N',41);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (98,'/public/images/notif-order.png','Order Received','Customer has ordered 2 items of Baguette Club Ham Cheese from your shop.','From efoodbasket','/products/99/','07/10/2021','N',8);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (99,'/public/images/notif-order.png','Order Received','Customer has ordered 2 items of Geitost from your shop.','From efoodbasket','/products/93/','07/10/2021','N',8);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (100,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Tomato & Italian Cheese Salad Bowl from your shop.','From efoodbasket','/products/101/','07/10/2021','N',8);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (101,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Filled French Toast from your shop.','From efoodbasket','/products/102/','07/10/2021','N',8);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (102,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Bakehouse Seeded Toasts and Almond from your shop.','From efoodbasket','/products/98/','07/10/2021','N',8);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (103,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Blue Murder from your shop.','From efoodbasket','/products/91/','07/10/2021','N',8);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (104,'/public/images/notif-order.png','Order Placed','Your order of 9 items is being placed. You can collect them from collection slot after 2021-07-15.','From efoodbasket','#','07/10/2021','N',43);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (105,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Black COD Fillet Steaks from your shop.','From efoodbasket','/products/70/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (106,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of King Crab Legs from your shop.','From efoodbasket','/products/77/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (107,'/public/images/notif-order.png','Order Received','Customer has ordered 3 items of Cucumber from your shop.','From efoodbasket','/products/62/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (108,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Mussels from your shop.','From efoodbasket','/products/71/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (109,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Avocados from your shop.','From efoodbasket','/products/60/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (110,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Maine Lobster from your shop.','From efoodbasket','/products/68/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (111,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Kale from your shop.','From efoodbasket','/products/66/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (112,'/public/images/notif-order.png','Order Placed','Your order of 7 items is being placed. You can collect them from collection slot after 2021-07-15.','From efoodbasket','#','07/10/2021','N',43);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (113,'/public/images/notif-order.png','Order Received','Customer has ordered 3 items of Pork Loin Steaks from your shop.','From efoodbasket','/products/5/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (114,'/public/images/notif-order.png','Order Received','Customer has ordered 2 items of Hardwood Smoked Bacons from your shop.','From efoodbasket','/products/6/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (115,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Pig Feet from your shop.','From efoodbasket','/products/9/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (116,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Pork Liver from your shop.','From efoodbasket','/products/7/','07/10/2021','N',4);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (117,'/public/images/notif-order.png','Order Placed','Your order of 10 items is being placed. You can collect them from collection slot after 2021-07-15.','From efoodbasket','#','07/10/2021','N',43);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (118,'/public/images/notif-order.png','Order Received','Customer has ordered 3 items of Pickled Walnuts from your shop.','From efoodbasket','/products/97/','07/10/2021','N',8);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (119,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Gluten Free Bread from your shop.','From efoodbasket','/products/87/','07/10/2021','N',7);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (120,'/public/images/notif-order.png','Order Received','Customer has ordered 2 items of Cooked Beetroot Pack from your shop.','From efoodbasket','/products/64/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (121,'/public/images/notif-order.png','Order Received','Customer has ordered 2 items of Blue Murder from your shop.','From efoodbasket','/products/91/','07/10/2021','N',8);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (122,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Brie de Meaux from your shop.','From efoodbasket','/products/92/','07/10/2021','N',8);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (123,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Mixed Salad Bag from your shop.','From efoodbasket','/products/65/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (124,'/public/images/notif-order.png','Order Placed','Your order of 12 items is being placed. You can collect them from collection slot after 2021-07-16.','From efoodbasket','#','07/10/2021','N',43);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (125,'/public/images/notif-order.png','Order Received','Customer has ordered 3 items of Brussel Sprouts from your shop.','From efoodbasket','/products/42/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (126,'/public/images/notif-order.png','Order Received','Customer has ordered 3 items of Broccoli from your shop.','From efoodbasket','/products/43/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (127,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Baby Squid from your shop.','From efoodbasket','/products/72/','07/10/2021','N',5);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (128,'/public/images/notif-order.png','Order Received','Customer has ordered 2 items of Iceberg Lettuce from your shop.','From efoodbasket','/products/63/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (129,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Yellow Zucchini from your shop.','From efoodbasket','/products/49/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (130,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Tarragon from your shop.','From efoodbasket','/products/58/','07/10/2021','N',6);
INSERT INTO NOTIFICATIONS(NOTIFICATION_ID,IMAGE_LINK,TITLE,BODY,SENDER_TEXT,MAIN_LINK,NOTIFIED_DATE,IS_SEEN,USER_ID) VALUES (131,'/public/images/notif-order.png','Order Received','Customer has ordered 1 item of Figs from your shop.','From efoodbasket','/products/29/','07/10/2021','N',6);

-- DB Admins
insert into db_access (id, username, password, status, user_role) values (1, 'lcbiplove1@gmail.com', 'Biplove1234', 'Y', 'ADMIN');
insert into db_access (id, username, password, status, user_role) values (2, 'srijanpanta@gmail.com', 'Srijan1234', 'Y', 'ADMIN');
insert into db_access (id, username, password, status, user_role) values (3, 'manandharsamyak7@gmail.com', 'Samyak1234', 'Y', 'ADMIN');
insert into db_access (id, username, password, status, user_role) values (4, 'sunampokharel243@gmail.com', 'Sunam1234', 'Y', 'ADMIN');
insert into db_access (id, username, password, status, user_role) values (5, 'aprabesh65@gmail.com', 'Prabesh1234', 'Y', 'ADMIN');

-- DB Traders
insert into db_access (id, username, password, status, user_role) values (6, 'butcher@gmail.com', 'butcher', 'Y', 'TRADER');
insert into db_access (id, username, password, status, user_role) values (7, 'fishmonger@gmail.com', 'fishmonger', 'Y', 'TRADER');
insert into db_access (id, username, password, status, user_role) values (8, 'bakery@gmail.com', 'bakery', 'Y', 'TRADER');
insert into db_access (id, username, password, status, user_role) values (9, 'delicatessen@gmail.com', 'delicatessen', 'Y', 'TRADER');
insert into db_access (id, username, password, status, user_role) values (10, 'greengrocer@gmail.com', 'greengrocer', 'Y', 'TRADER');

-- FUNCTIONS
-- Give total price of product 
CREATE OR REPLACE FUNCTION Total_Price (
    price in number,
    product_discount in number,
    quantity in number,
    discount in number
)
RETURN number IS 
    subtotal number;
    total number;
BEGIN 
   subtotal := (100 - product_discount) * price * quantity / 100 ;
   total := (100 - discount) * subtotal / 100 ;
    
   RETURN total; 
END; 
/ 

-- Thumbnail url for product
create or replace FUNCTION THUMBNAIL_URL(
    prod_id NUMBER
) 
RETURN varchar2
IS
    var varchar2(500);
BEGIN
    select 'http://localhost/media/products/' || IMAGE_NAME as image_url into var
    from products p, product_images pi
    where p.product_id = pi.product_id and p.product_id = prod_id and rownum = 1;

    return var;
END;
/