# Back-It-Up

This project is a php and mysql based application that allows users to back up their files. The files are stored in the directory
public/uploads.

## Prerequisites

To connect this to your own mysql server you must add your server and user information to src/service/databaseconnector.php.

Then you must have the following tables and schemas in your database:

### users (table name)
id int
email varchar
username varchar
pwd varchar
created_on datetime

---SQL STATEMENT---

CREATE TABLE `users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `email` varchar(255) NOT NULL,
 `username` varchar(255) NOT NULL,
 `pwd` varchar(255) NOT NULL,
 `created_on` datetime NOT NULL
);

### files (table name)
id int
file_name varchar
uploaded_by int
uploaded_on datetime
status enum('1','0')

---SQL STATEMENT---

CREATE TABLE `files` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 `uploaded_by` int(11),
 `uploaded_on` datetime NOT NULL,
 `status` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

You can run this server locally with the 'symfony server:start' command.
