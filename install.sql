CREATE TABLE accounts (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, email VARCHAR(256), name VARCHAR(128), password VARCHAR(512));
CREATE TABLE services (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, account_id INT, name VARCHAR(128), description VARCHAR(512), type VARCHAR(128));
CREATE TABLE service_params (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, service_id INT, k VARCHAR(32), v VARCHAR(256));
CREATE TABLE announcements (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, title VARCHAR(128), body TEXT, time INT);
CREATE TABLE locks (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, ip VARCHAR(16), time INT, action VARCHAR(16), num INT);
CREATE TABLE remote_tokens (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, user_id INT, service_id INT, ip VARCHAR(32), token VARCHAR(128), time INT);
