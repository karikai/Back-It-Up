<?php

// src/Service/DatabaseConnector.php
namespace App\Service;

class DatabaseConnector
{
    public function sqlConnection()
    {
        $conn = mysqli_connect('localhost', 'kari', 'AaBbCc123', 'back_it_up');

        return $conn;
    }
}
