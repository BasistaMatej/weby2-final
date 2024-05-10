<?php
require '../include/db_connect.php';
require_once '../vendor/autoload.php';


$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';

switch(strtoupper($_SERVER["REQUEST_METHOD"])) {
  
    case "POST":
      if ($endpoint === '/request-reset') {
          $postdata = json_decode(file_get_contents("php://input"), true);

          if (!isset($postdata['email'])) {
              response(["error" => "Missing required field: email"], 400);
              return;
          }

          $email = $postdata['email'];
          $stmt = $conn->prepare("SELECT user_id, valid, auth_level FROM users WHERE email = :email");
          $stmt->bindParam(':email', $email);
          $stmt->execute();
          
          if ($stmt->rowCount() === 1) {
              $user = $stmt->fetch();
              
              if ($user['auth_level'] == -1) {
                  response(["error" => "Account blocked"], 401);
                  return;
              }
              
              if ($user['valid'] == 0) {
                  response(["error" => "Account not verified"], 401);
                  return;
              }
              
              
              // Create JWT token for password reset
              // $token = generate_jwt(['email' => $email, 'scope' => 'password_reset'], 24*60*60); // Token valid for 24 hours
              $token = generate_jwt($postdata['email'], 24*60);
              
              $body = file_get_contents(__DIR__ . "/../html/email-change_password.html");
              $body = str_replace("{{token}}", $token, $body);
              $body = str_replace("{{year}}", date('Y'), $body);

              

              send_email('Classroom Interact - Password Reset', $body, $email);
              response(["message" => "Password reset email sent successfully. Token: " . $token], 200);
          } else {
              response(["error" => "Email not found"], 404);
          }
      }
        

    
      else if ($endpoint === '/reset-password') {
          $postdata = json_decode(file_get_contents("php://input"), true);

          // if(!isset($_GET['token'])) {
          //   response(["error" => "Missing data"], 400);
          //   return;
          // }
          $email = verify_jwt($_GET['token']);
          echo $email;
          if(!$email) {
            response(["error" => "Invalid token"], 401);
            return;
          }

          $newPassword = $postdata['password'] ?? '';

          if (!$token || !$newPassword) {
              response(["error" => "Missing token or new password"], 400);
              return;
          }

          $email = verify_jwt($token);

          if (!$email) {
              response(["error" => "Invalid or expired token"], 401);
              return;
          }

          $stmt = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
          $passwordHash = password_hash($newPassword, PASSWORD_ARGON2ID);
          $stmt->bindParam(':password', $passwordHash);
          $stmt->bindParam(':email', $email);
          $stmt->execute();

          response(["message" => "Password updated successfully"], 200);
      }



    
      break;



      case "GET":
        
        if ($endpoint === '/reset-password') {
          $postdata = json_decode(file_get_contents("php://input"), true);

          if(!isset($_GET['token'])) {
            response(["error" => "Missing data"], 400);
            return;
          }

          
          $email = verify_jwt($_GET['token']);
          
          if(!$email) {
            response(["error" => "Invalid token: " . verify_jwt($_GET['token']) ], 401);
            return;
          }

          

          response(["message" => "Page loaded succesfully"], 200);
      }


      break;

    default:
        response(["error" => "Method not allowed"], 405);
        break;
}
?>
