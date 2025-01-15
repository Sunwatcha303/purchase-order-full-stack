DROP TABLE TransactionDetail;
DROP TABLE Transaction;
DROP TABLE Customer;
DROP TABLE Stock;

CREATE TABLE Customer (
    IDCust INT NOT NULL,
    Custname VARCHAR(50) NULL DEFAULT NULL,
    Sex CHAR(1) NULL DEFAULT NULL,
    Address VARCHAR(100) NULL DEFAULT NULL,
    Tel VARCHAR(20) NULL DEFAULT NULL,
    PRIMARY KEY (IDCust)
);

CREATE TABLE Product (
    IDProduct INT NOT NULL,
    ProductName VARCHAR(50) NULL DEFAULT NULL,
    PricePerUnit DECIMAL(10,2) NULL DEFAULT NULL,
    StockQty INT NULL DEFAULT NULL,
    PRIMARY KEY (IDProduct)
);

CREATE TABLE Transaction (
    IDtransaction INT NOT NULL,
    IDCust INT NOT NULL,
    Qty INT,
    Totalprice DECIMAL(10, 2),
    Vat DECIMAL(10, 2),
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (IDtransaction),
    FOREIGN KEY (IDCust) REFERENCES Customer(IDCust)
);

CREATE TABLE TransactionDetail (
    IDtransaction INT NOT NULL,
    IDProduct INT NOT NULL,
    Qty INT,
    PRIMARY KEY (IDtransaction, IDProduct),
    FOREIGN KEY (IDtransaction) REFERENCES Transaction(IDtransaction),
    FOREIGN KEY (IDProduct) REFERENCES Product(IDProduct)
);

INSERT INTO Customer (IDCust, Custname, Sex, Address, Tel) VALUES
(1, 'John Doe', 'M', '123 Maple St, Springfield', '555-1234'),
(2, 'Jane Smith', 'F', '456 Oak St, Metropolis', '555-5678'),
(3, 'Alice Johnson', 'F', '789 Pine St, Gotham', '555-9101'),
(4, 'Bob Brown', 'M', '101 Elm St, Smallville', '555-1122'),
(5, 'Mary Davis', 'F', '202 Birch St, Star City', '555-3344');


INSERT INTO Product (IDProduct, ProductName, PricePerUnit, StockQty) VALUES
(101, 'Laptop', 999.99, 50),
(102, 'Smartphone', 699.99, 200),
(103, 'Tablet', 299.99, 150),
(104, 'Headphones', 49.99, 300),
(105, 'Smartwatch', 199.99, 120);

SELECT * FROM Product;
SELECT * FROM Transaction td ;
SELECT * FROM TransactionDetail td ORDER BY 1;
