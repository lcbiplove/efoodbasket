-- Blank space work for sqlplus
SET sqlblanklines ON;
SET SERVEROUTPUT ON;

-- Sequences
DROP SEQUENCE user_id_seq;
CREATE SEQUENCE user_id_seq;




-- Drop tables --
DROP TABLE USERS CASCADE CONSTRAINTS;
DROP TABLE TRADERS CASCADE CONSTRAINTS;

-- Table Creation --
CREATE TABLE USERS(
	user_id	            INTEGER NOT NULL,
	email	            VARCHAR(30) NOT NULL UNIQUE,
	fullname	        VARCHAR(25) NOT NULL,
    password	        VARCHAR(64),
	address             VARCHAR(20) NOT NULL,
    user_role           VARCHAR(20) NOT NULL,
    contact             VARCHAR(15) NOT NULL,
    joined_date         DATE NOT NULL,
    otp                 NUMBER(6),
    otp_last_date       DATE,
    is_verified       VARCHAR(1) DEFAULT 'N',

	CONSTRAINT	pk_USERss PRIMARY KEY (user_id)
);

CREATE TABLE TRADERS (
    trader_id           INTEGER NOT NULL,
    pan                 INTEGER NOT NULL UNIQUE,
    product_type        VARCHAR(30) NOT NULL UNIQUE,
    product_details     VARCHAR(255) NOT NULL,
    documents_path      VARCHAR(255) NOT NULL,
    is_approved         VARCHAR(1) DEFAULT 'N',
    requested_date      DATE NOT NULL,
    user_id             INTEGER UNIQUE,
    
    FOREIGN KEY (user_id) REFERENCES Users(user_id), 
	CONSTRAINT	pk_Traderss PRIMARY KEY (trader_id)
);


-------------- Triggers -----------------------


-- Triggers
CREATE OR REPLACE TRIGGER user_auto_increment
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
  SELECT user_id_seq.nextval
  INTO :new.user_id
  FROM dual;
END;
/