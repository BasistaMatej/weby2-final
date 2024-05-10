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
                response(["message" => "Password reset email sent successfully."], 200); 
                // response(["message" => "Password reset email sent successfully. Token: " . $token], 200); //TODO remove token from response

            } else {
                response(["error" => "Email not found"], 404);
            }
        }
        

    
        else if ($endpoint === '/reset-password') {
            $postdata = json_decode(file_get_contents("php://input"), true);

            $email = $postdata['email'] ?? '';
            $newPassword = $postdata['password'] ?? '';
            
            // Check if required fields are present
            if (!$email || !$newPassword) {
                response(["error" => "Missing required fields"], 400);
                return;
            }

            // Basic validation
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$newPassword) {
                response(["error" => "Missing or invalid fields"], 400);
                return;
            }


            // Check if email exists in the database
            $checkStmt = $conn->prepare("SELECT 1 FROM users WHERE email = :email");
            $checkStmt->bindParam(':email', $email);
            $checkStmt->execute();
            if ($checkStmt->rowCount() === 0) {
                response(["error" => "Email does not exist"], 404);
                return;
            }
            
            // Update password
            $stmt = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
            $passwordHash = password_hash($newPassword, PASSWORD_ARGON2ID);
            $stmt->bindParam(':password', $passwordHash);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
        
            if ($stmt->rowCount() === 0) {
                response(["error" => "Password not updated"], 500);
            } else {
            response(["message" => "Password updated successfully"], 200);
            }
        }

    break;
    

    default:
        response(["error" => "Method not allowed"], 405);
        break;
}
?>
