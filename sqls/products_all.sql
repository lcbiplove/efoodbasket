DROP SEQUENCE product_category_id_seq;
CREATE SEQUENCE product_category_id_seq START WITH 1 INCREMENT BY 1;
DROP SEQUENCE product_id_seq;
CREATE SEQUENCE product_id_seq START WITH 1 INCREMENT BY 1;
DROP SEQUENCE product_image_id_seq;
CREATE SEQUENCE product_image_id_seq START WITH 1 INCREMENT BY 1;

DROP TABLE PRODUCT_CATEGORIES CASCADE CONSTRAINTS;
DROP TABLE PRODUCTS CASCADE CONSTRAINTS;
DROP TABLE PRODUCT_IMAGES CASCADE CONSTRAINTS;

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

-- Product categories add by admin
INSERT INTO PRODUCT_CATEGORIES VALUES (1, 'Meat', 'Here all the data related to pure meats are placed');
INSERT INTO PRODUCT_CATEGORIES VALUES (2, 'Bakery', 'Here all the data related to pure baked stuffs are placed');
INSERT INTO PRODUCT_CATEGORIES VALUES (3, 'Fish', 'Here all the data related to pure fishes are placed');
