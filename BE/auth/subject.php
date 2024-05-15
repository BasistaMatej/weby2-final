<?php

// IMPLEMENT - NOT IMPLEMENTED YET
require '../include/db_connect.php';
require_once '../vendor/autoload.php';

$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';

switch(strtoupper($_SERVER["REQUEST_METHOD"])) {
    
    case "GET":
        $user = verify_token($conn);
        if (!$user) {
            // The response is already handled within the function
            exit;  // Stop further execution if the token is invalid
        }

        try {
            $stmt = $conn->prepare("SELECT subject_name FROM subjects");
            $stmt->execute();

            $subjects = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $subjects[] = $row['subject_name'];
            }

            if ($subjects) {
                response(['subjects' => $subjects], 200);
            } else {
                response(['error' => 'No subjects found'], 404);
            }
        } catch (PDOException $e) {
            response(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    break;

    case "POST":
        $user = verify_token($conn);
        if (!$user) {
            // The response is already handled within the function
            exit;  // Stop further execution if the token is invalid
        }


        $postdata = json_decode(file_get_contents("php://input"), true);
        

        if (!isset($postdata['subject_name'])) {
            response(["error" => "Missing required field: subject_name"], 400);
            return;
        }
        $subject_name = $postdata['subject_name'];

        //check if subject already exists
        $stmt = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_name = :subject_name");
        $stmt->bindParam(':subject_name', $subject_name);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            response(["error" => "Subject already exists"], 400);
            return;
        }

        $stmt = $conn->prepare("INSERT INTO subjects (subject_name) VALUES (:subject_name)");
        $stmt->bindParam(':subject_name', $subject_name);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            response(["message" => "Subject added successfully"], 200);
        } else {
            response(["error" => "Failed to add subject"], 500);
        }
        
    break;

    case "PUT":
        $user = verify_token($conn);
        if (!$user) {
            // The response is already handled within the function
            exit;  // Stop further execution if the token is invalid
        }
        if ($user['auth_level'] != 2) {
            response(["error" => "Unauthorized"], 403);
            return;
        }

        $postdata = json_decode(file_get_contents("php://input"), true);

        if (!isset($postdata['old_subject_name']) || !isset($postdata['new_subject_name'])) {
            response(["error" => "Missing required fields"], 400);
            return;
        }
        
        $old_subject_name = $postdata['old_subject_name'];
        $new_subject_name = $postdata['new_subject_name'];
        
        //check if new subject already exists
        $stmt = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_name = :new_subject_name");
        $stmt->bindParam(':new_subject_name', $new_subject_name);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            response(["error" => "Subject already exists"], 400);
            return;
        }

        //check if old subject already exists
        $stmt = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_name = :old_subject_name");
        $stmt->bindParam(':old_subject_name', $old_subject_name);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            response(["error" => "Subject not found"], 404);
            return;
        }

        //update subject
        $stmt = $conn->prepare("UPDATE subjects SET subject_name = :new_subject_name WHERE subject_name = :old_subject_name");
        $stmt->bindParam(':new_subject_name', $new_subject_name);
        $stmt->bindParam(':old_subject_name', $old_subject_name);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            response(["message" => "Subject updated successfully"], 200);
        } else {
            response(["error" => "Failed to update subject"], 500);
        }
    break;

    case "DELETE":
        $user = verify_token($conn);
        if (!$user) {
            // The response is already handled within the function
            exit;  // Stop further execution if the token is invalid
        }
        if ($user['auth_level'] != 2) {
            response(["error" => "Unauthorized"], 403);
            return;
        }

        //TODO check if subject is used in any question, if that is the case, return 400
        if ($endpoint == ''){
            response(["error" => "Missing required field: subject_name"], 400);
            return;
        }
        else{
            $subject_name = $endpoint;
        }

        //check if subject exists
        $stmt = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_name = :subject_name");
        $stmt->bindParam(':subject_name', $subject_name);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            response(["error" => "Subject not found"], 404);
            return;
        }
        else{
            $subject_id = $stmt->fetch(PDO::FETCH_ASSOC)['subject_id'];
        }

        // check if subject is used in any question
        $stmt = $conn->prepare("SELECT template_question_id FROM template_questions WHERE subject_id = :subject_id");
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            response(["error" => "Subject is used in questions"], 400);
            return;
        }

        //delete subject
        $stmt = $conn->prepare("DELETE FROM subjects WHERE subject_name = :subject_name");
        $stmt->bindParam(':subject_name', $subject_name);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            response(["message" => "Subject deleted successfully"], 200);
        } else {
            response(["error" => "Failed to delete subject"], 500);
        }

    case "OPTIONS":
        break;
    default:
        response(["error" => "Method not allowed"], 405);
        break;
}
?>
