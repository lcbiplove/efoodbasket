DROP SEQUENCE cart_id_seq;
CREATE SEQUENCE cart_id_seq START WITH 1 INCREMENT BY 1;
DROP SEQUENCE product_cart_id_seq;
CREATE SEQUENCE product_cart_id_seq START WITH 1 INCREMENT BY 1;

DROP TABLE CARTS CASCADE CONSTRAINTS;
DROP TABLE PRODUCT_CARTS CASCADE CONSTRAINTS;

CREATE TABLE CARTS (
	cart_id             INTEGER NOT NULL,
	total_items       	NUMBER(10) DEFAULT 0,
    total_price	        NUMBER(10,2) DEFAULT 0,
    user_id             INTEGER NOT NULL UNIQUE,
	
	FOREIGN KEY (user_id) REFERENCES Users(user_id),
	CONSTRAINT	pk_CART PRIMARY KEY (cart_id)
);
CREATE TABLE PRODUCT_CARTS (
    product_cart_id     INTEGER NOT NULL,
	quantity	        NUMBER(10) NOT NULL,
	cart_id             INTEGER NOT NULL,
    product_id          INTEGER NOT NULL,

	FOREIGN KEY (product_id) REFERENCES Products(product_id),
	CONSTRAINT	pk_PRODUCT_CART PRIMARY KEY (product_cart_id)
);


-- Trigger
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