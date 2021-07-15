CREATE SEQUENCE db_access_id_seq START WITH 100 INCREMENT BY 1;
DROP TABLE DB_ACCESS CASCADE CONSTRAINTS;


-- Admins
insert into db_access (id, username, password, status, user_role) values (1, 'lcbiplove1@gmail.com', 'Biplove1234', 'Y', 'ADMIN');
insert into db_access (id, username, password, status, user_role) values (2, 'srijanpanta@gmail.com', 'Srijan1234', 'Y', 'ADMIN');
insert into db_access (id, username, password, status, user_role) values (3, 'manandharsamyak7@gmail.com', 'Samyak1234', 'Y', 'ADMIN');
insert into db_access (id, username, password, status, user_role) values (4, 'sunampokharel243@gmail.com', 'Sunam1234', 'Y', 'ADMIN');
insert into db_access (id, username, password, status, user_role) values (5, 'aprabesh65@gmail.com', 'Prabesh1234', 'Y', 'ADMIN');

-- Traders
insert into db_access (id, username, password, status, user_role) values (6, 'butcher@gmail.com', 'butcher', 'Y', 'TRADER');
insert into db_access (id, username, password, status, user_role) values (7, 'fishmonger@gmail.com', 'fishmonger', 'Y', 'TRADER');
insert into db_access (id, username, password, status, user_role) values (8, 'bakery@gmail.com', 'bakery', 'Y', 'TRADER');
insert into db_access (id, username, password, status, user_role) values (9, 'delicatessen@gmail.com', 'delicatessen', 'Y', 'TRADER');
insert into db_access (id, username, password, status, user_role) values (10, 'greengrocer@gmail.com', 'greengrocer', 'Y', 'TRADER');


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