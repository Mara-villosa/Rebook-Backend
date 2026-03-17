CREATE DATABASE Rebook;
USE Rebook;
CREATE TABLE users (
    id int AUTO_INCREMENT PRIMARY KEY,
    email varchar(255) UNIQUE NOT NULL,
    name varchar(255) NOT NULL,
    password varchar(255) NOT NULL
);