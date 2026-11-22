--Author: Mauricio Letort
CREATE DATABASE IF NOT EXISTS gravitask;
USE gravitask;

CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  ticket        VARCHAR(64)
);

CREATE TABLE events (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  type        VARCHAR(10)  NOT NULL,
  name        VARCHAR(100) NOT NULL,
  description TEXT,
  deadline    DATETIME NOT NULL,
  weight      INT NOT NULL,
  status      VARCHAR(10) NOT NULL DEFAULT 'open',
  section     VARCHAR(50),
  FOREIGN KEY (user_id) REFERENCES users (id)
);