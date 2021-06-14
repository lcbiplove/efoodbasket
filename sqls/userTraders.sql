SET sqlblanklines ON;
-- Drop tables --
DROP TABLE USERS CASCADE CONSTRAINTS;
DROP TABLE TRADERS CASCADE CONSTRAINTS;

-- Table Creation --
CREATE TABLE USERS(
	User_id	            INTEGER NOT NULL,
	Email	            VARCHAR(30) NOT NULL UNIQUE,
	Fullname	        VARCHAR(25) NOT NULL,
    Password	        VARCHAR(20) NOT NULL,
	Address             VARCHAR(20) NOT NULL,
    User_role           VARCHAR(20) NOT NULL,
    Contact             VARCHAR(15) NOT NULL,
    JOINED_DATE         DATE NOT NULL,

	CONSTRAINT	pk_USERss PRIMARY KEY (User_id)
);

CREATE TABLE TRADERS (
    Trader_id           INTEGER NOT NULL,
    PAN                 INTEGER NOT NULL UNIQUE,
    Product_type        VARCHAR(30) NOT NULL UNIQUE,
    Product_details     VARCHAR(255) NOT NULL,
    Documents_path      VARCHAR(255) NOT NULL,
    IS_APPROVED         VARCHAR(1) DEFAULT 'N',
    Requested_date      DATE NOT NULL,
    User_id             INTEGER UNIQUE,
    
    FOREIGN KEY (User_id) REFERENCES Users(User_id), 
	CONSTRAINT	pk_Traderss PRIMARY KEY (Trader_id)
);