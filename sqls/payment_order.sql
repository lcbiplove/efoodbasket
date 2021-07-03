DROP SEQUENCE payment_id_seq;
CREATE SEQUENCE payment_id_seq START WITH 1 INCREMENT BY 1;
DROP SEQUENCE order_id_seq;
CREATE SEQUENCE order_id_seq START WITH 1 INCREMENT BY 1;
DROP SEQUENCE order_product_id_seq;
CREATE SEQUENCE order_product_id_seq START WITH 1 INCREMENT BY 1;

DROP TABLE PAYMENTS CASCADE CONSTRAINTS;
DROP TABLE ORDERS CASCADE CONSTRAINTS;
DROP TABLE ORDER_PRODUCTS CASCADE CONSTRAINTS;

CREATE TABLE PAYMENTS (
	payment_id          INTEGER NOT NULL,   
    invoice_id          VARCHAR2(50) NOT NULL UNIQUE,
	payment_type        VARCHAR2(20) NOT NULL,
    amount	            NUMBER(10, 2) NOT NULL,
	payment_date	    DATE NOT NULL,
	user_id             INTEGER NOT NULL,

	FOREIGN KEY (user_id) REFERENCES Users(user_id),
    CONSTRAINT	pk_PAYMENT PRIMARY KEY (payment_id)
);
CREATE TABLE ORDERS(
	order_id                INTEGER NOT NULL,
	ordered_date            DATE NOT NULL,
	collection_date	        DATE NOT NULL,
	collection_slot_id      INTEGER NOT NULL,
    payment_id              INTEGER NOT NULL,
	voucher_id              INTEGER,
	
	FOREIGN KEY (collection_slot_id) REFERENCES Collection_Slots(collection_slot_id),
	FOREIGN KEY (payment_id) REFERENCES Payments(payment_id),
	FOREIGN KEY (voucher_id) REFERENCES Vouchers(voucher_id),
	CONSTRAINT	pk_ORDER PRIMARY KEY (order_id)
);
CREATE TABLE ORDER_PRODUCTS (
	order_product_id	    INTEGER NOT NULL,
	quantity	        NUMBER(10) NOT NULL,
	product_id          INTEGER NOT NULL,
    order_id            INTEGER NOT NULL,
	
    FOREIGN KEY (product_id) REFERENCES Products(product_id),
	FOREIGN KEY (order_id) REFERENCES Orders(order_id),
	CONSTRAINT	pk_ORDER_PRODUCT PRIMARY KEY (order_product_id)
);

-- 
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
