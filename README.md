# Back-It-Up

This project is a PHP and MySQL based application that utilizes the Symfony framework. The web app allows users to back up their files and stores them in the uploads directory.

## Prerequisites

To connect this to your own MySQL server you must add your server and user information to src/service/databaseconnector.php.  

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
  
## Important Directories and Files

/public/uploads - location of uploaded files  
/src/Service/DatabaseConnector.php - database connection file  
/public/assets - assets  
/src/Controller - controllers  
/src/Service - services  
/src/templates - templates  
/config/routes.yaml - url routes configuration  
/config/services.yaml - services configuration  
