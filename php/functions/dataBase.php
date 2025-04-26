<?php

$host = 'localhost';
$dbName = 'vkusnie_raki';
$user = 'root';
$password = '';

try{
   $db = new PDO("mysql:host=$host;dbname=$dbName;", $user, $password);
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
   die("Error connection to DB: " . $e->getMessage());
}



