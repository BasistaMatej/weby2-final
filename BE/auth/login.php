<?php
require '../include/db_connect.php';
require_once '../vendor/autoload.php';

switch(strtoupper($_SERVER["REQUEST_METHOD"])) {
  case "POST":
      $postdata = json_decode(file_get_contents("php://input"), true);

      if (!isset($postdata['email']) || !isset($postdata['password'])) {
          response(["error" => "Missing required fields"], 400);
          return;
      }

      $email = $postdata['email'];
      $password = $postdata['password'];

      $stmt = $conn->prepare("SELECT user_id, password, valid, auth_level FROM users WHERE email = :email");
      $stmt->bindParam(':email', $email);
      $stmt->execute();

      if ($stmt->rowCount() == 1) {
          $user = $stmt->fetch();

          if (!password_verify($password, $user['password'])) {
              response(["error" => "Invalid credentials"], 401);
              return;
          }

          if ($user['valid'] == 0) {
              response(["error" => "Account not verified"], 401);
              return;
          }

          if ($user['auth_level'] == -1) {
              response(["error" => "Account blocked"], 401);
              return;
          }

          // Generate a new access token
          $accessToken = generate_jwt($email, 10); // Access token valid for 10 minutes

          // Generate a new refresh token
          $refreshToken = generate_jwt($email, 1440); // Refresh token valid for 1 day

          // Set the HTTP-only cookie for the refresh token
          setcookie('jwt', $refreshToken, time() + 86400, '/', '', true, true); // cookie is set for 1 day

          response([
              "accessToken" => $accessToken,
              "refreshToken" => $refreshToken // Optionally send refresh token back in the response
          ], 200);
      } else {
          response(["error" => "Invalid credentials"], 401);
      }
      break;

  case "OPTIONS":
      break;

  default:
      response(["error" => "Method not allowed"], 405);
      break;
}