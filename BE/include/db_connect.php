<?php
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Headers: *");
  header("Access-Control-Allow-Methods: GET,POST,OPTIONS");
  header("Content-Type: application/json");
  
  require __DIR__ . '/.config.php';
  require __DIR__ . '/utils.php';
  
  try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
  }
  
?>