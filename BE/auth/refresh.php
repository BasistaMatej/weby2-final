<?php

require '../include/db_connect.php';
require_once '../vendor/autoload.php';

switch(strtoupper($_SERVER["REQUEST_METHOD"])) {
    case "GET":
        if (!isset($_COOKIE['jwt'])) {
            response(["error" => "Unauthorized - No token provided"], 401);
            return;
        }

        $refreshToken = $_COOKIE['jwt'];
        
        $user = verify_token($conn, $refreshToken);
        if (!$user) {
            // The response is already handled within the function
            exit;  // Stop further execution if the token is invalid
        }

        // If everything is valid, issue a new access token
        $accessToken = generate_jwt($user['email'], 10);  // Generate new access token for 10 minutes

        response(["accessToken" => $accessToken], 200);
        break;

    default:
        response(["error" => "Method not allowed"], 405);
        break;
}

?>