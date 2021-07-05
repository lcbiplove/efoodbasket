DROP SEQUENCE review_id_seq;
CREATE SEQUENCE review_id_seq START WITH 1 INCREMENT BY 1;

DROP TABLE REVIEWS CASCADE CONSTRAINTS;

CREATE TABLE REVIEWS (
	review_id	        INTEGER NOT NULL,
    rating	            NUMBER(2, 1) NOT NULL,
    review_text         VARCHAR2(2000),
	review_date	        DATE NOT NULL,
	user_id	            INTEGER NOT NULL,
    product_id          INTEGER NOT NULL,
	
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id),
    CONSTRAINT	pk_REVIEW PRIMARY KEY (review_id)
);

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
