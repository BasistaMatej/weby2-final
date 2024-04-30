<?php
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Headers: *");
  header("Access-Control-Allow-Methods: GET,POST,OPTIONS");
  header("Content-Type: application/json");
  
  require 'include/.config.php';
  require 'include/utils.php';
  
  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
  }
  
?>