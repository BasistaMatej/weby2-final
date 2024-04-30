<?php

require '../include/db_connect.php';
require_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

switch(strtoupper($_SERVER["REQUEST_METHOD"])) {
  case "GET":
      if(!isset($_COOKIE['jwt'])) {
        response(json_encode(["error" => "Unauthorized"]), 401);
        return;
      }

      $refreshToken = $_COOKIE['jwt'];
      try {
          $decoded = JWT::decode($refreshToken, new Key('R€FR€?H_T0k€N_s€CR€T','HS256'));

          // Check validity of user
          $stmt = $conn->prepare("SELECT user_id, email, valid, auth_level FROM users WHERE email = :email");
          $stmt->bindParam(':email', $decoded->email);
          $stmt->execute();
          $user = $stmt->fetch();

          if($stmt->rowCount() == 0 || $user['valid'] == 0 || $user['auth_level'] == -1) {
              response(json_encode(["error" => "Unauthorized"]), 401);
              return;
          }

          $accessToken = JWT::encode([
              'user_id' => $user['user_id'],
              'email' => $decoded->email
          ], '&CC€ZZ=v0K€M_S€CReD', 'HS256', 600); // 10 minut
          
          response(json_encode(["accessToken" => $accessToken]), 200);
      } catch (Exception $e) {
          response(json_encode(["error" => "Unauthorized"]), 401);
          return;
      }
      break;
  default:
      response(json_encode(["error" => "Method not allowed"]), 405);
      break;
  }