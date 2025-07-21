DROP TABLE IF EXISTS Department;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Employee;
DROP TABLE IF EXISTS Customer;
DROP TABLE IF EXISTS Product;
DROP TABLE IF EXISTS Cart;
DROP TABLE IF EXISTS CartItem;
DROP TABLE IF EXISTS Orders;
DROP TABLE IF EXISTS Flower;

-- Create the Department table first (referenced by Employee and Product)
CREATE TABLE Department (
    DepartmentNo INT PRIMARY KEY,
    DepartmentLocation VARCHAR(100) NOT NULL,
    DepartmentCategory VARCHAR(50) NOT NULL
);

-- Create the base Users table including FirstName and LastName
CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    UserName VARCHAR(50) NOT NULL,
    Address VARCHAR(50) NOT NULL,
    PhoneNumber VARCHAR(20) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    Password VARCHAR(50) NOT NULL,
    Role VARCHAR(20) NOT NULL CHECK (Role IN ('Employee', 'Customer', 'Admin', 'Manager')),
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL
);

-- Create the Employee table as a specialization of Users.
CREATE TABLE Employee (
    UserID INT PRIMARY KEY,
    Sex CHAR(1) NOT NULL CHECK (Sex IN ('M', 'F')),
    HireDate DATE NOT NULL,
    Salary DECIMAL(10,2) NOT NULL,
    DepartmentNo INT NOT NULL,
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    FOREIGN KEY (DepartmentNo) REFERENCES Department(DepartmentNo)
);


-- Create the Customer table as a specialization of Users.
CREATE TABLE Customer (
    UserID INT PRIMARY KEY,
    ShippingAddress VARCHAR(255) NOT NULL,
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);


-- Create the Product table
CREATE TABLE Product (
    ProductID INT PRIMARY KEY,
    ProductName VARCHAR(100) NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    Picture VARCHAR(255) NOT NULL,
    Category VARCHAR(50) NOT NULL,
    Description TEXT NOT NULL,
    Stock INT NOT NULL CHECK (Stock BETWEEN 10 AND 500),
    DepartmentNo INT NOT NULL,
    FOREIGN KEY (DepartmentNo) REFERENCES Department(DepartmentNo)
);


-- Create the Cart table
CREATE TABLE Cart (
    CartID INT AUTO_INCREMENT PRIMARY KEY,
    CurrentTotal DECIMAL(10,2) NOT NULL,
    CustomerID INT NOT NULL,
    FOREIGN KEY (CustomerID) REFERENCES Customer(UserID)
);

-- Create the CartItem table to represent the many-to-many relationship between Cart and Product
CREATE TABLE CartItem (
    CartItemID INT AUTO_INCREMENT PRIMARY KEY,
    CartID INT NOT NULL,
    ProductID INT  NOT NULL,
    Quantity INT  NOT NULL,
    UnitPrice DECIMAL(10,2)  NOT NULL,
    FOREIGN KEY (CartID) REFERENCES Cart(CartID),
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
);

-- Create the Order table
CREATE TABLE Orders (
    OrderID INT AUTO_INCREMENT PRIMARY KEY,
    OrderDate DATE NOT NULL,
    CustomerID INT NOT NULL,
    CartID INT UNIQUE NOT NULL,
    TotalAmount DECIMAL(10,2) NOT NULL,
    Status VARCHAR(20) NOT NULL CHECK (Status IN ('processed', 'cancelled', 'delivered')),
    FOREIGN KEY (CustomerID) REFERENCES Customer(UserID),
    FOREIGN KEY (CartID) REFERENCES Cart(CartID)
);

CREATE TABLE Flower (
    Name VARCHAR(20) NOT NULL,
    ScientificName VARCHAR(20) NOT NULL,
    Color VARCHAR(15) NOT NULL,
    Region1 VARCHAR(30) NOT NULL,
    Region2 VARCHAR(30) NOT NULL,
    Region3 VARCHAR(30) NOT NULL,
    Description VARCHAR(200) NOT NULL,
    Fact1 VARCHAR(150) NOT NULL,
    Fact2 VARCHAR(150) NOT NULL
);

-- Insert departments into the Department table
INSERT INTO Department (DepartmentNo, DepartmentLocation, DepartmentCategory) VALUES
(1, '1 Maple St, Los Angeles, CA', 'Home utilities'),
(2, '101 Oak Ave, Dallas, TX', 'Merchandise'),
(3, '202 Pine Rd, Albany, NY', 'Cosmetics'),
(4, '303 Palm Dr, Miami, FL', 'Decorations'),
(5, '404 Elm Blvd, Chicago, IL', 'Personal Care');

-- Insert users into the Users table
INSERT INTO Users (UserID, UserName, Address, PhoneNumber, Email, Password, Role, FirstName, LastName) VALUES
(123, 'thomasp', '12 Birch Lane, Springfield, IL', '217-555-1234', 'thomas.petersen@example.com', 'password', 'Manager', 'Thomas', 'Petersen'),
(321, 'marina!', '45 Cedar Street, Austin, TX', '512-555-3210', 'marina.rodriguez@example.com', 'admin1', 'Admin', 'Marina', 'Rodriguez'),
(213, 'yana_', '78 Willow Avenue, Orlando, FL', '407-555-2134', 'yana.santos@example.com', 'password123', 'Customer', 'Yana', 'Santos'),
(456, 'timmyc89', '23 Elm Drive, Denver, CO', '303-555-4567', 'timmy.cuamba@example.com', 'Mbvc214asfd', 'Employee', 'Timmy', 'Cuamba'),
(789, 'poly_vian', '56 Maple Court, Seattle, WA', '206-555-7890', 'polyana.viana@example.com', 'MibBIB12', 'Employee', 'Polyana', 'Viana'),
(159, 'chrisguts', '89 Oak Boulevard, Phoenix, AZ', '602-555-1598', 'chris.gutierrez@example.com', 'passtheport', 'Manager', 'Chris', 'Gutierrez'),
(753, 'songy1', '34 Pine Road, Boston, MA', '617-555-7534', 'song.yadong@example.com', 'pazzword', 'Customer', 'Song', 'Yadong'),
(138, 'jobberelder', '67 Aspen Way, Columbus, OH', '614-555-1389', 'evan.elder@example.com', 'DklklF_12', 'Employee', 'Evan', 'Elder'),
(792, 'chinnyschnell', '90 Poplar Circle, Portland, OR', '503-555-7921', 'matt.schnell@example.com', 'LEoasnhf125', 'Customer', 'Matt', 'Schnell'),
(117, 'nerdhagen', '100 Main Ave, Wall Street, Alabama', '666-201-5423', 'sandman@example.com', 'testtube', 'Admin', 'Cory', 'Sandhagen');


-- Insert employees into the Employees table.
INSERT INTO Employee (UserID, Sex, HireDate, Salary, DepartmentNo)
VALUES 
(123, 'M', '2017-05-02', 90000, 1),
(321, 'F', '2016-03-15', 92000, 1),
(456, 'M', '2019-08-20', 78000, 3),
(789, 'F', '2023-02-10', 71000, 4),
(159, 'M', '2017-06-05', 87000, 2),
(138, 'M', '2021-11-25', 74000, 5),
(117, 'M', '2020-06-29', 78000, 5);


-- Insert customers into Customer table
INSERT INTO Customer (UserID, ShippingAddress)
VALUES 
(213, '78 Willow Avenue, Orlando, FL'),
(753, '34 Pine Road, Boston, MA'),
(792, '90 Poplar Circle, Portland, OR');

-- Insert products into the Product table
INSERT INTO Product (Description, Stock, Price, Category, ProductID, ProductName, DepartmentNo, Picture) VALUES
('Hydrating body lotion with lily of the valley extract', 350, 9.99, 'Personal Care', 221, 'Lily Valley Body Lotion', 5, './img/lotion.png'),
('Exfoliating bar of soap.', 400, 3.99, 'Personal Care', 222, 'Lily of the Valley Soap', 5, './img/soap.png'),
('Bed sheets with a lily of the valley flower design', 200, 20.99, 'Home', 223, 'Lily Valley Bed Sheets', 1, './img/bed_sheet.png'),
('Wall art depicting the Lily of the Valley flower', 150, 30.99, 'Decoration', 224, 'Lily of the Valley Painting', 4, './img/painting.png'),
('Handcrafted glass dome featuring a Lily of the Valley flower', 300, 25.99, 'Decoration', 225, 'Glass Dome', 4, './img/glass_dome.png'),
('Phone Case with a Lily of the Valley flower design on the back', 400, 9.99, 'Accessories', 226, 'Lily of the Valley Phone Case', 2, './img/phone_case.png'),
('A backpack with a Lily of the Valley flower print', 400, 35.99, 'Accessories', 227, 'Lily of the Valley Backpack', 2, './img/backpack.png'),
('Deep cleaning, volumizing shampoo', 250, 12.99, 'Personal Care', 228, 'Lily of the Valley Shampoo', 5, './img/shampoo.png'),
('Moisturizing conditioner', 200, 14.99, 'Personal Care', 229, 'Lily of the Valley Conditioner', 5, './img/conditioner.png'),
('Table Runner with Lily of the Valley flower design', 300, 8.99, 'Home', 230, 'Lily of the Valley Table Runner', 1, './img/table_runner.png'),
('Ultrasoft pillow protectors with Lily of the Valley flower print', 180, 10.99, 'Home', 231, 'Lily of the Valley Pillow Cover', 1, './img/pillowcase.png'),
('Moisturizing face cream', 350, 12.99, 'Cosmetics', 232, 'Lily of the Valley Face Cream', 3, './img/face_cream.png'),
('Lip balm infused with the Lily of the Valley flower essence', 450, 6.99, 'Cosmetics', 233, 'Lily of the Valley Lip Balm', 3, './img/lip_balm.png'),
('Ceramic Vase with Lily of the Valley design', 200, 8.99, 'Decoration', 234, 'Lily of the Valley Ceramic Vase', 4, './img/vase.png'),
('Moisturizing hand cream with Shea Butter', 275, 19.99, 'Cosmetics', 211, 'Hand Cream', 3, './img/hand_cream.png'),
('Close fitting cap with deep visor', 150, 17.99, 'Accessories', 865, 'Lily of the Valley Cap', 2, './img/cap.png'),
('Cotton loose fitting T-shirt', 100, 24.99, 'Accessories', 867, 'Lily of the Valley T-shirt', 2, './img/tshirt.png'),
('Smooth and fragrant lotion for men', 300, 11.99, 'Cosmetics', 868, 'Lily of the Valley Lotion for Men', 3, './img/lotion_men.png'),
('Soft fleece hoodie with Lily of the Valley design', 150, 14.99, 'Accessories', 321, 'Lily of the Valley-themed hoodie', 2, './img/hoodie.png'),
('Tall, fluorescent Lamp with Lily of the Valley design', 250, 16.99, 'Decoration', 322, 'Lily of the Valley Lamp', 4, './img/lamp.png'),
('Antiperspirant deodorant for men and women', 400, 4.99, 'Personal Care', 746, 'Lily of the Valley scented deodorant', 5, './img/deodorant.png'),
('Lily of the Valley Scented Candle', 300, 5.99, 'Home', 323, 'Lily of the Valley Scented Candle', 1, './img/candle.png'),
('Handwoven Rug with Lily of the Valley design', 100, 13.99, 'Home', 324, 'Lily of the Valley Design', 1, './img/rug.png'),
('Lily of the Valley Scented Perfume', 400, 17.99, 'Cosmetics', 325, 'Lily of the Valley Perfume', 3, './img/perfume.png'),
('Hardcover Notebook with Lily of the Valley design', 250, 7.99, 'Accessories', 326, 'Lily of the Valley Notebook', 2, './img/notebook.png'),
('Lily of the Valley flower-scented cleaning gel hand soap', 100, 6.99, 'Personal Care', 138, 'Lily of the Valley hand soap', 5, './img/hand_soap.png'),
('Lily of the Valley-scented room spray', 250, 4.99, 'Home', 139, 'Lily of the Valley Room Spray', 1, './img/room_spray.png'),
('Bronze Lily of the Valley flower garden sculpture', 50, 59.99, 'Decoration', 199, 'Lily of the Valley sculpture', 4, './img/bronze_garden_sculpture.png'),
('Artificial Lily of the Valley Flower', 300, 6.99, 'Decoration', 140, 'Artificial Lily of the Valley Flower', 4, './img/artificial_flowers.png'),
('Lily of the Valley Decorative Mug', 200, 14.99, 'Decoration', 141, 'Lily of the Valley Decorative Mug', 4, './img/mug.png');

-- Insert flower into the Flower table
INSERT INTO Flower (Name, ScientificName, Color, Region1, Region2, Region3, Description, fact1, fact2)
VALUES ('Lily of the Valley', 'Convallaria majalis', 'White', 'North America', 'Europe', 'Asia', 'A woodland flowering plant with sweetly scented, pendent, bell-shaped white flowers borne in sprays in spring', 'Is often mistaken for wild garlic, which is a fatal mistake as they are very poisonous.', ' In the language of flowers, the Lily of the Valley symbolizes the return of happiness.');
