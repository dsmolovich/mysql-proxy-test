#!/bin/sh
mysql -hdbproxy -uappuser -papppassword -e"
DROP DATABASE IF EXISTS appdb;
CREATE DATABASE appdb;
USE appdb;
CREATE TABLE rw_test (
  id int(11) NOT NULL AUTO_INCREMENT,
  counter int(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
INSERT INTO rw_test VALUES (1, 0);"