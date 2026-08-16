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