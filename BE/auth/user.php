<?php

// IMPLEMENT - NOT IMPLEMENTED YET
require '../include/db_connect.php';
require_once '../vendor/autoload.php';


$endpoint1 = isset($_GET['endpoint1']) ? $_GET['endpoint1'] : '';
// $endpoint2 = isset($_GET['endpoint2']) ? $_GET['endpoint2'] : '';

switch(strtoupper($_SERVER["REQUEST_METHOD"])) {
    case "GET":
        $user = verify_token($conn);
        if (!$user) {
            // The response is already handled within the function
            exit;  // Stop further execution if the token is invalid
        }

        // if the user is not an admin, return 403
        if ($user['auth_level'] != 2) {
            response(["error" => "Unauthorized"], 403);
            exit;
        }

        try {
           // get all user from the database
            $stmt = $conn->prepare("SELECT * FROM users");
            $stmt->execute();

            $users = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = $row;
            }

            if ($users) {
                response(['users' => $users], 200);
            } else {
                response(['error' => 'No users found'], 204);
            }

            
        } catch (PDOException $e) {
            response(['error' => 'Database error: ' . $e->getMessage()], 500);
        }

    break;

    case "PUT":
        
        $user = verify_token($conn);
        if (!$user) {
            // The response is already handled within the function
            exit;  // Stop further execution if the token is invalid
        }

        // if the user is not an admin, return 403
        if ($user['auth_level'] != 2) {
            response(["error" => "Unauthorized"], 403);
            exit;
        }

        if (!$endpoint1) {
            response(["error" => "Missing required field: user_id"], 400);
            return;
        }

        $user_id = $endpoint1;


        $postdata = json_decode(file_get_contents("php://input"), true);
        

        // check if the user exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
       
        if ($stmt->rowCount() === 0) {
            response(["error" => "User not found"], 204);
            return;
        }

        // if the user is not an admin, return 403
        if ($editUser['auth_level'] == 2) {
            response(["error" => "You cannot change admin" ], 403);
            exit;
        }
        

        $name = isset($postdata['name']) ? $postdata['name'] : $editUser['name'];
        $surname = isset($postdata['surname']) ? $postdata['surname'] : $editUser['surname'];
        $email = isset($postdata['email']) ? $postdata['email'] : $editUser['email'];
        $password = isset($postdata['password']) ? password_hash($postdata['password'], PASSWORD_DEFAULT) : $editUser['password'];
        $auth_level = isset($postdata['auth_level']) ? $postdata['auth_level'] : $editUser['auth_level'];
        $valid = isset($postdata['valid']) ? $postdata['valid'] : $editUser['valid'];
        $last_login = isset($postdata['last_login']) ? $postdata['last_login'] : $editUser['last_login'];


        // update the user with the new data, but not every data need to be updated
        $stmt = $conn->prepare("UPDATE users SET name = :name, surname = :surname, email = :email, password = :password, auth_level = :auth_level, valid = :valid, last_login = :last_login WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':auth_level', $auth_level);
        $stmt->bindParam(':valid', $valid);
        $stmt->bindParam(':last_login', $last_login);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            response(["message" => "User updated successfully"], 200);
        } else {
            response(["error" => "Failed to update user"], 500);
        }

      
    
    break;

    case "DELETE":
        $user = verify_token($conn);
        if (!$user) {
            // The response is already handled within the function
            exit;  // Stop further execution if the token is invalid
        }

        // if the user is not an admin, return 403
        if ($user['auth_level'] != 2) {
            response(["error" => "Unauthorized"], 403);
            exit;
        }
        
        $user_id = $endpoint1;

        // check if the user exists
        $stmt = $conn->prepare("SELECT user_id auth_level FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            response(["error" => "User not found"], 204);
            return;
        }

        if ($user_id == $user['user_id']) {
            response(["error" => "You cannot delete yourself"], 400);
            return;
        }

        if ($user_id == 2) {
            response(["error" => "You cannot delete the admin"], 400);
            return;
        }

        // delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        response(["message" => "User deleted successfully"], 200);
        
        
    break;


        
    case "OPTIONS":
    break;

    default:
        response(["error" => "Method not allowed"], 405);
        break;
}
?>
