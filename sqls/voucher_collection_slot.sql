DROP SEQUENCE collection_slot_id_seq;
CREATE SEQUENCE collection_slot_id_seq START WITH 1 INCREMENT BY 1;
DROP SEQUENCE voucher_id_seq;
CREATE SEQUENCE voucher_id_seq START WITH 1 INCREMENT BY 1;

DROP TABLE collection_slots CASCADE CONSTRAINTS;
DROP TABLE vouchers CASCADE CONSTRAINTS;

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