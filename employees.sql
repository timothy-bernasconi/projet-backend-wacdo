CREATE DATABASE IF NOT EXISTS dataTeam;

USE dataTeam;

CREATE TABLE IF NOT EXISTS employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(60),
    lastname VARCHAR(60),
    position VARCHAR(30),
    email VARCHAR(100),
    password VARCHAR(256)
);

CREATE TABLE IF NOT EXISTS stock (
     id INT PRIMARY KEY AUTO_INCREMENT,
     productName VARCHAR(60),
     productCategory VARCHAR(30),
     price DECIMAL(10,2),
     quantity INT

);


INSERT INTO stock (id, productName, productCategory, price, quantity) VALUES
(1, 'Le 280', 'burgers', 6.80, 50),
(2, 'Big Tasty', 'burgers', 8.60, 50),
(3, 'Big Tasty Bacon', 'burgers', 8.90, 50),
(4, 'Big Mac', 'burgers', 6.00, 50),
(5, 'CBO', 'burgers', 8.90, 50),
(6, 'MC Chicken', 'burgers', 7.30, 50),
(7, 'MC Crispy', 'burgers', 5.30, 50),
(8, 'MC Fish', 'burgers', 4.85, 50),
(9, 'Royal Bacon', 'burgers', 5.10, 50),
(10, 'Royal Cheese', 'burgers', 4.40, 50),
(11, 'Royal Deluxe', 'burgers', 5.40, 50),
(12, 'Signature BBQ Beef 2 viandes', 'burgers', 11.40, 50),
(13, 'Signature Beef BBQ', 'burgers', 10.30, 50),

(14, 'Coca Cola', 'boissons', 1.90, 50),
(15, 'Coca Sans Sucres', 'boissons', 1.90, 50),
(16, 'Eau', 'boissons', 1.00, 50),
(17, 'Fanta Orange', 'boissons', 1.90, 50),
(18, 'Ice Tea Pêche', 'boissons', 1.90, 50),
(19, 'Ice Tea Citron', 'boissons', 1.90, 50),
(20, 'Jus d''Orange', 'boissons', 2.10, 50),
(21, 'Jus de Pommes Bio', 'boissons', 2.30, 50),


(22, 'Petite Frite', 'frites', 1.45, 50),
(23, 'Moyenne Frite', 'frites', 2.75, 50),
(24, 'Grande Frite', 'frites', 3.50, 50),
(25, 'Potatoes', 'frites', 2.15, 50),
(26, 'Grande Potatoes', 'frites', 3.40, 50),

(27, 'Cheeseburger', 'encas', 2.60, 50),
(28, 'Croc MCdo', 'encas', 3.20, 50),
(29, 'Nuggets x4', 'encas', 4.20, 50),
(30, 'Nuggets x20', 'encas', 13.00, 50),

(31, 'Brownie', 'desserts', 2.60, 50),
(32, 'Cheesecake chocolat M&M''S', 'desserts', 3.10, 50),
(33, 'Cheesecake Fraise', 'desserts', 3.10, 50),
(34, 'Cookie', 'desserts', 3.20, 50),
(35, 'Donut', 'desserts', 2.60, 50),
(36, 'Macarons', 'desserts', 2.70, 50),
(37, 'MC Fleury', 'desserts', 4.40, 50),
(38, 'Muffin', 'desserts', 3.60, 50),
(39, 'Sunday', 'desserts', 1.00, 50),

(40, 'Classic Barbecue', 'sauces', 0.70, 50),
(41, 'Classic Moutarde', 'sauces', 0.70, 50),
(42, 'Creamy Deluxe', 'sauces', 0.70, 50),
(43, 'Ketchup', 'sauces', 0.70, 50),
(44, 'Chinoise', 'sauces', 0.70, 50),
(45, 'Curry', 'sauces', 0.70, 50),
(46, 'Pommes Frites', 'sauces', 0.70, 50),

(47, 'Petite Salade', 'salades', 3.30, 50),
(48, 'Cesar Classic', 'salades', 8.80, 50),
(49, 'Italienne Mozza', 'salades', 8.80, 50),

(50, 'MC Wrap chevre', 'wraps', 3.10, 50),
(51, 'MC Wrap Poulet Bacon', 'wraps', 3.30, 50),
(52, 'Ptit Wrap Chevre', 'wraps', 2.60, 50),
(53, 'Ptit Wrap Ranch', 'wraps', 2.60, 50);

CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    is_menu TINYINT(1) NOT NULL DEFAULT 0,
    burger_id INT DEFAULT NULL,
    burger_qty INT DEFAULT 0,
    wrap_id INT DEFAULT NULL,
    wrap_qty INT DEFAULT 0,
    salade_id INT DEFAULT NULL,
    salade_qty INT DEFAULT 0,
    side_id INT DEFAULT NULL,
    side_qty INT DEFAULT 0,
    drink_id INT DEFAULT NULL,
    drink_qty INT DEFAULT 0,
    sauce_id INT DEFAULT NULL,
    sauce_qty INT DEFAULT 0,
    encas_id INT DEFAULT NULL,
    encas_qty INT DEFAULT 0,
    dessert_id INT DEFAULT NULL,
    dessert_qty INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);