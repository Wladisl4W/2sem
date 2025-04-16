-- Таблица для хранения заявок
CREATE TABLE user_applications (
  application_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name VARCHAR(150) NOT NULL,
  phone_number VARCHAR(20) NOT NULL,
  email_address VARCHAR(80) NOT NULL,
  birth_date DATE NOT NULL,
  gender TINYINT(1) NOT NULL,
  biography TEXT,
  user_login VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  PRIMARY KEY (application_id)
) ENGINE = InnoDB;

-- Таблица для хранения языков программирования
CREATE TABLE programming_languages (
  language_id INT(2) UNSIGNED NOT NULL AUTO_INCREMENT,
  language_name VARCHAR(20) NOT NULL,
  PRIMARY KEY (language_id)
) ENGINE = InnoDB;

-- Предзаполнение таблицы языков программирования
INSERT INTO programming_languages (language_name) VALUES 
('Pascal'),
('C'),
('C++'),
('JavaScript'),
('PHP'),
('Python'),
('Java'),
('Haskell'),
('Clojure'),
('Prolog'),
('Scala');

-- Таблица для связи заявок с языками программирования
CREATE TABLE application_languages (
  application_id INT(10) UNSIGNED NOT NULL,
  language_id INT(2) UNSIGNED NOT NULL,
  FOREIGN KEY (application_id) REFERENCES user_applications (application_id) ON DELETE CASCADE,
  FOREIGN KEY (language_id) REFERENCES programming_languages (language_id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- Таблица для хранения пользователей
CREATE TABLE users (
  user_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_login VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  application_id INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (user_id),
  FOREIGN KEY (application_id) REFERENCES user_applications (application_id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- Таблица для хранения данных администратора
CREATE TABLE admin_users (
  admin_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  admin_login VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  PRIMARY KEY (admin_id)
) ENGINE = InnoDB;

-- Добавление администратора
INSERT INTO admin_users (admin_login, password_hash) VALUES
('admin', MD5('123'));