<?php
  require 'include/db_connect.php';

  $endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';

  switch(strtoupper($_SERVER["REQUEST_METHOD"])) {
    // Na overenie tokenu z emailu (FE posle GET request s tokenom)
    case "GET":
      if(!isset($_GET['token'])) {
        response(["error" => "Missing data"], 400);
        return;
      }

      $email = verify_jwt($_GET['token']);
      if(!$email) {
        response(["error" => "Invalid token"], 401);
        return;
      }


      $stmt = $conn->prepare("UPDATE users SET valid = 1 WHERE email = :email");
      $stmt->bindParam(':email', $email);
      $stmt->execute();

      response(["message" => "Account verified"], 200);

      break;
    // Vytvorenie noveho uzivatela a poslanie emailu na overenie uctu
    case "POST":
      // get the post data
      $postdata = json_decode(file_get_contents("php://input"), true);
      if(!isset($postdata['email']) || !isset($postdata['password']) || !isset($postdata['name']) || !isset($postdata['surname'])) {
        response(["error" => "Missing required fields"], 400);
        return;
      }

      // check if is email valid
      if(!filter_var($postdata['email'], FILTER_VALIDATE_EMAIL)) {
        response(["error" => "Invalid email"], 400);
        return;
      }

      // Check max lenght and min lenght of name and surname
      if(strlen($postdata['name']) > 50 || strlen($postdata['name']) < 3) {
        response(["error" => "Name must be between 3 and 50 characters"], 400);
        return;
      }

      if(strlen($postdata['surname']) > 50 || strlen($postdata['surname']) < 3) {
        response(["error" => "Surname must be between 3 and 50 characters"], 400);
        return;
      }

      // check if email already exists
      $stmt = $conn->prepare("SELECT email FROM users WHERE email = :email");
      $stmt->bindParam(':email', $postdata['email']);
      $stmt->execute();

      if($stmt->rowCount() > 0) {
        response(["error" => "Email already exists"], 400);
        return;
      }

      // hash the password
      $password = password_hash($postdata['password'], PASSWORD_ARGON2ID);

      // insert the user
      $conn->beginTransaction();
      $stmt = $conn->prepare("INSERT INTO users (email, password, name, surname) VALUES (:email, :password, :name, :surname)");
      $stmt->bindParam(':email', $postdata['email']);
      $stmt->bindParam(':password', $password);
      $stmt->bindParam(':name', $postdata['name']);
      $stmt->bindParam(':surname', $postdata['surname']);
      $stmt->execute();

      $token = generate_jwt($postdata['email'], 24*60);

      try {
        $body = file_get_contents(__DIR__."/html/email.html");
        $body = str_replace("{{token}}", $token, $body);
        $body = str_replace("{{year}}", Date('y'), $body);

        send_email('Classroom Interact - Welcome!', $body, $postdata['email']);

        $conn->commit();
        response(["message" => "User registered successfully"], 201);
      } catch (Exception $e) {
          $conn->rollBack();
          response(["error" => "Error while creating the account."], 500);
      }

      break;


    case "OPTIONS":
      break;
    default:
      response(["error" => "Method not allowed"], 405);
      return;

  }
?>