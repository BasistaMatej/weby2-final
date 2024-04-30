<?php
  require 'include/db_connect.php';

  switch(strtoupper($_SERVER["REQUEST_METHOD"])) {
    // Na overenie tokenu z emailu (FE posle GET request s tokenom)
    case "GET":
      if(!isset($_GET['token'])) {
        response(json_encode(["error" => "Missing data"]), 400);
        return;
      }

      $email = verify_jwt($_GET['token']);
      if(!$email) {
        response(json_encode(["error" => "Invalid token"]), 401);
        return;
      }

      $stmt = $conn->prepare("UPDATE users SET valid = 1 WHERE email = :email");
      $stmt->bindParam(':email', $email);
      $stmt->execute();

      response(json_encode(["message" => "Account verified"]), 200);

      break;
    // Vytvorenie noveho uzivatela a poslanie emailu na overenie uctu
    case "POST":
      // get the post data
      $postdata = json_decode(file_get_contents("php://input"), true);
      if(!isset($postdata['email']) || !isset($postdata['password']) || !isset($postdata['name']) || !isset($postdata['surname'])) {
        response(json_encode(["error" => "Missing required fields"]), 400);
        return;
      }

      // check if is email valid
      if(!filter_var($postdata['email'], FILTER_VALIDATE_EMAIL)) {
        response(json_encode(["error" => "Invalid email"]), 400);
        return;
      }

      // Check max lenght and min lenght of name and surname
      if(strlen($postdata['name']) > 50 || strlen($postdata['name']) < 3) {
        response(json_encode(["error" => "Name must be between 3 and 50 characters"]), 400);
        return;
      }

      if(strlen($postdata['surname']) > 50 || strlen($postdata['surname']) < 3) {
        response(json_encode(["error" => "Surname must be between 3 and 50 characters"]), 400);
        return;
      }

      // check if email already exists
      $stmt = $conn->prepare("SELECT email FROM users WHERE email = :email");
      $stmt->bindParam(':email', $postdata['email']);
      $stmt->execute();

      if($stmt->rowCount() > 0) {
        response(json_encode(["error" => "Email already exists"]), 400);
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
        $body    = '<h1>You have been registered successfully.</h1><br><p>Welcome to Classroom Interact! To activate your account, please click on the following link: <a style="color: #7825c6; text-decoration: none; border-bottom: 1px solid #7825c6" href="http://localhost:5173/activate/' . $token . '">Activate Account</a></p><p>Link is valid for 24 hours.</p><br><p>Best regards,<br>Classroom Interact Team</p>';
        send_email('Classroom Interact - Welcome!', $body, $postdata['email']);

        $conn->commit();
        response(json_encode(["message" => "User registered successfully"]), 201);
      } catch (Exception $e) {
          $conn->rollBack();
          response(json_encode(["error" => "Error while creating the account."]), 500);
      }

      break;


    case "OPTIONS":
      break;
    default:
      response(json_encode(["error" => "Method not allowed"]), 405);
      return;

  }
?>