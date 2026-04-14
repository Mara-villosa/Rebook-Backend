CREATE DATABASE Rebook;
USE Rebook;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    id_document CHAR(9) NOT NULL,
    birthday DATE NOT NULL,
    city VARCHAR(50) NOT NULL,
    address VARCHAR(100) NOT NULL,
    postal_code CHAR(5) NOT NULL,
    phone CHAR(9) NOT NULL,
    card_name VARCHAR(100),
    card_number CHAR(16),
    cvv CHAR(3)
);

CREATE TABLE books(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    author VARCHAR(100),
    description VARCHAR(100) NOT NULL,
    rent_price DECIMAL(10, 2),
    sell_price DECIMAL (10, 2),
    isbn CHAR(13),
    url VARCHAR(250),
    category VARCHAR(50)
);

CREATE TABLE rented(
    id_user INT,
    id_book INT,
    PRIMARY KEY (id_user, id_book),
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_book) REFERENCES books(id)
);

CREATE TABLE favourites(
    id_user INT,
    id_book INT,
    PRIMARY KEY (id_user, id_book),
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_book) REFERENCES books(id)
);

CREATE TABLE inCart(
    id_user INT,
    id_book INT,
    PRIMARY KEY (id_user, id_book),
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_book) REFERENCES books(id)
);