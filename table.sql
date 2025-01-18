DROP TABLE IF EXISTS TransactionDetail;
DROP TABLE IF EXISTS Transaction;
DROP TABLE IF EXISTS Customer;
DROP TABLE IF EXISTS Product;
DROP TABLE IF EXISTS StatusDetail;
DROP TABLE IF EXISTS Status;

DELIMITER $$

CREATE TRIGGER trg_log_status_change
AFTER UPDATE ON `Transaction`
FOR EACH ROW
BEGIN
    -- ตรวจสอบว่าค่า IDStatus มีการเปลี่ยนแปลง
    IF NEW.IDStatus <> OLD.IDStatus THEN
        -- เก็บ log ลงใน StatusDetail
        INSERT INTO StatusDetail (IDtransaction, IDStatus, Timestamp)
        VALUES (NEW.IDtransaction, NEW.IDStatus, CURRENT_TIMESTAMP);
    END IF;
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_log_status_insert
AFTER INSERT ON `Transaction`
FOR EACH ROW
BEGIN
    -- บันทึก log ลงใน StatusDetail เมื่อมีการ INSERT
    INSERT INTO StatusDetail (IDtransaction, IDStatus, Timestamp)
    VALUES (NEW.IDtransaction, NEW.IDStatus, CURRENT_TIMESTAMP);
END$$

DELIMITER ;


INSERT INTO Transaction (IDCust, Qty, Totalprice, Vat, IDStatus)
VALUES (1, 2, 1999.98, 139.99, 1);

UPDATE Transaction
SET IDStatus = 2
WHERE IDtransaction = 4;





CREATE TABLE Customer (
    IDCust INT NOT NULL AUTO_INCREMENT,
    Custname VARCHAR(50) NULL DEFAULT NULL,
    Sex CHAR(1) NULL DEFAULT NULL,
    Address VARCHAR(100) NULL DEFAULT NULL,
    Tel VARCHAR(20) NULL DEFAULT NULL,
    PRIMARY KEY (IDCust)
);

CREATE TABLE Product (
    IDProduct INT NOT NULL AUTO_INCREMENT,
    ProductName VARCHAR(50) NULL DEFAULT NULL,
    PricePerUnit DECIMAL(10,2) NULL DEFAULT NULL,
    StockQty INT NULL DEFAULT NULL,
    ReserveQty INT NULL DEFAULT NULL,
    PRIMARY KEY (IDProduct)
);

CREATE TABLE Status(
	IDStatus INT NOT NULL AUTO_INCREMENT,
	StatusName VARCHAR(50) NOT NULL,
	PRIMARY KEY (IDStatus)
);

CREATE TABLE Transaction (
    IDtransaction INT NOT NULL AUTO_INCREMENT,
    IDCust INT NOT NULL,
    Qty INT,
    Totalprice DECIMAL(10, 2),
    Vat DECIMAL(10, 2),
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    IDStatus INT, 
    PRIMARY KEY (IDtransaction),
    FOREIGN KEY (IDCust) REFERENCES Customer(IDCust),
    FOREIGN KEY (IDStatus) REFERENCES Status(IDStatus)
);

CREATE TABLE TransactionDetail (
    IDtransaction INT NOT NULL,
    IDProduct INT NOT NULL,
    Qty INT,
    PRIMARY KEY (IDtransaction, IDProduct),
    FOREIGN KEY (IDtransaction) REFERENCES Transaction(IDtransaction),
    FOREIGN KEY (IDProduct) REFERENCES Product(IDProduct)
);

CREATE TABLE StatusDetail(
	seq INT NOT NULL AUTO_INCREMENT,
	IDtransaction INT NOT NULL,
	IDStatus INT NOT NULL ,
	Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (seq, IDtransaction),
	FOREIGN KEY (IDtransaction) REFERENCES Transaction(IDtransaction),
	FOREIGN KEY (IDStatus) REFERENCES Status(IDStatus)
);
-- Delete data if any exists
DELETE FROM Customer;
DELETE FROM Product;

-- Insert sample data (without specifying the auto-incremented columns)
INSERT INTO Customer (Custname, Sex, Address, Tel) VALUES
('John Doe', 'M', '123 Maple St, Springfield', '555-1234'),
('Jane Smith', 'F', '456 Oak St, Metropolis', '555-5678'),
('Alice Johnson', 'F', '789 Pine St, Gotham', '555-9101'),
('Bob Brown', 'M', '101 Elm St, Smallville', '555-1122'),
('Mary Davis', 'F', '202 Birch St, Star City', '555-3344');

INSERT INTO Status (StatusName) VALUES 
('Pending'),
('Approved'),
('Rejected'),
('Packing'),
('Shipping'),
('Completed'),
('Cancelled');

INSERT INTO Product (ProductName, PricePerUnit, StockQty, ReserveQty) VALUES
('Laptop', 999.99, 50, 50),
('Smartphone', 699.99, 200, 200 ),
('Tablet', 299.99, 150, 150),
('Headphones', 49.99, 300, 300),
('Smartwatch', 199.99, 120, 120),
('Wireless Charger', 39.99, 250, 250),
('Gaming Mouse', 59.99, 180, 180),
('Mechanical Keyboard', 89.99, 120, 120),
('External SSD 1TB', 129.99, 100, 100),
('4K Monitor', 349.99, 80, 80),
('Portable Speaker', 79.99, 220, 220),
('Fitness Tracker', 99.99, 140, 140),
('Drone', 499.99, 50, 50),
('VR Headset', 299.99, 60, 60),
('Action Camera', 199.99, 90, 90),
('Electric Scooter', 599.99, 30, 30),
('E-Reader', 129.99, 110, 110),
('Webcam 1080p', 49.99, 250, 250),
('Noise-Canceling Earbuds', 149.99, 200, 200),
('Smart Home Hub', 89.99, 100, 100);

-- Show all data from Product, Transaction, and TransactionDetail
SELECT * FROM Product;
SELECT * FROM Transaction;
SELECT * FROM TransactionDetail ORDER BY 1;
