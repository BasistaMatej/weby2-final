<?php
require '../include/db_connect.php';
require_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;


switch(strtoupper($_SERVER["REQUEST_METHOD"])) {
  case "POST":
      
    $postdata = json_decode(file_get_contents("php://input"), true);

    if(!isset($postdata['email']) || !isset($postdata['password'])) {
        response(["error" => "Missing required fields"], 400);
        return;
    }

    $email = $postdata['email'];
    $password = $postdata['password'];
    
    $stmt = $conn->prepare("SELECT user_id, password, valid, auth_level FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if($stmt->rowCount() == 1) {
      $user = $stmt->fetch();

      if(!password_verify($password, $user['password'])) {
        response(["error" => "Invalid credentials"], 401);
        return;
      }

      if($user['valid'] == 0) {
        response(["error" => "Account not verified"], 401);
        return;
      }

      if($user['auth_level'] == -1) {
        response(["error" => "Account blocked"], 401);
        return;
      }

      $accessToken = JWT::encode([
          'user_id' => $user['user_id'],
          'email' => $email
      ], '&CC€ZZ=v0K€M_S€CReD', 'HS256', 600); // 10 minut 
      
      $refreshToken = JWT::encode([
          'email' => $email
      ], 'R€FR€?H_T0k€N_s€CR€T', 'HS256', 86400); // 1 den
      
      // HTTP-only cookie
      setcookie('jwt', $refreshToken, time() + 86400, '/', '', true, true);
      
      response(["accessToken" => $accessToken], 200);
    } else {
      response(["error" => "Invalid credentials"], 401);
      return;
    }
    
    break;
  case "OPTIONS":
      break;
  default:
      response(["error" => "Method not allowed"], 405);
      break;
  }