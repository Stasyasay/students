<?php

$host = 'localhost';
$dbname = 'university_db';
$user = 'root';
$pass = '';

try {

    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
?>