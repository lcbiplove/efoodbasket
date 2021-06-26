DROP SEQUENCE query_id_seq;
CREATE SEQUENCE query_id_seq START WITH 1 INCREMENT BY 1;

DROP TABLE QUERIES CASCADE CONSTRAINTS;

CREATE TABLE QUERIES(
	query_id	        INTEGER NOT NULL,
	question	        VARCHAR2(2000) NOT NULL,
	answer    	        VARCHAR2(2000) NULL,
	question_date      	DATE NOT NULL,
	answer_date	        DATE NULL,
	product_id	        INTEGER NOT NULL,
    user_id             INTEGER NOT NULL,
	
	FOREIGN KEY (product_id) REFERENCES PRODUCTS(product_id),
	FOREIGN KEY (user_id) REFERENCES Users(user_id),
    CONSTRAINT	pk_QUERY PRIMARY KEY (query_id)
);
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