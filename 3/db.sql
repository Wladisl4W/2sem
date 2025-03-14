CREATE TABLE applications (
  id_app int(10) unsigned NOT NULL AUTO_INCREMENT,
  FIO varchar(150) NOT NULL,
  tel varchar(20) NOT NULL,
  email varchar(80) NOT NULL,
  DR DATE NOT NULL,
  sex int(1) NOT NULL,
  bio varchar(200),
  PRIMARY KEY (id_app)
) ENGINE = InnoDB;

CREATE TABLE languages (
  id_lang int(2) unsigned NOT NULL AUTO_INCREMENT,
  lang varchar(10) NOT NULL,
  PRIMARY KEY (id_lang)
) ENGINE = InnoDB;

INSERT INTO languages (lang) VALUES ('Pascal');
INSERT INTO languages (lang) VALUES ('C');
INSERT INTO languages (lang) VALUES ('C++');
INSERT INTO languages (lang) VALUES ('JavaScript');
INSERT INTO languages (lang) VALUES ('PHP');
INSERT INTO languages (lang) VALUES ('Python');
INSERT INTO languages (lang) VALUES ('Java');
INSERT INTO languages (lang) VALUES ('Haskell');
INSERT INTO languages (lang) VALUES ('Clojure');
INSERT INTO languages (lang) VALUES ('Prolog');
INSERT INTO languages (lang) VALUES ('Scala');

CREATE TABLE app_langs (
  id_app int(10) unsigned NOT NULL,
  id_lang int(2) unsigned NOT NULL,
  FOREIGN KEY (id_app) REFERENCES applications (id_app),
  FOREIGN KEY (id_lang) REFERENCES languages (id_lang)
) ENGINE = InnoDB;
