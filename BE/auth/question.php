<?php

// IMPLEMENT - NOT IMPLEMENTED YET
require '../include/db_connect.php';
require_once '../vendor/autoload.php';


$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';

switch(strtoupper($_SERVER["REQUEST_METHOD"])) {
    case "GET":
        if ($endpoint == ""){
            $user = verify_token($conn);
            if (!$user) {
                // The response is already handled within the function
                exit;  // Stop further execution if the token is invalid
            }
    
            // Determine if we need to filter by user_id based on auth_level
            $sql = "SELECT 
                        tq.template_question_text, 
                        tq.created, 
                        tq.code, 
                        s.subject_name
                    FROM 
                        template_questions tq
                    JOIN 
                        subjects s ON tq.subject_id = s.subject_id";
    
            if ($user['auth_level'] == 1) {
                $sql .= " WHERE tq.author_id = :user_id"; // Append user condition for specific auth level
            }
    
            $stmt = $conn->prepare($sql);
    
            // Bind parameter if necessary
            if ($user['auth_level'] == 1) {
                $stmt->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);
            }
            
            if (!$stmt->execute()) {
                response(['error' => 'Database error'], 500);
                exit; // Stop execution if there is a database error
            }
    
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($questions)) {
                response(['message' => 'No questions found'], 404); // Provide a not found status if no results
            } else {
                response(['questions' => $questions], 200); // Return questions if found
            }
        }
        break;

        case "POST":
            if ($endpoint == ""){
                $user = verify_token($conn);
                if (!$user) {
                    exit;  // Stop further execution if the token is invalid
                }
            }
            
            $postdata = json_decode(file_get_contents("php://input"), true);
            $template_question_text = $postdata['template_question_text'];
            $subject_name = $postdata['subject_name'];
            $active = $postdata['active'];
            $type = $postdata['type'];
            $answer_text = $postdata['answer_text'];
            if ($user['auth_level'] == 2) {
                $by_user_id = $postdata['by_user_id'];
                
            }
            if ($user['auth_level'] == 1) {
                $by_user_id = $user['user_id'];
            }

            try {
                $conn->beginTransaction();
        
                // Find the subject_id based on the subject_name
                $stmt = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_name = :subject_name");
                $stmt->bindParam(':subject_name', $subject_name);
                $stmt->execute();
                $subject = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$subject) {
                    throw new Exception("Subject not found");
                }
                
                $subject_id = $subject['subject_id'];
        
                // Insert into template questions
                $stmt = $conn->prepare("INSERT INTO template_questions (template_question_text, subject_id, active, type, author_id) VALUES (:template_question_text, :subject_id, :active, :type, :author_id)");
                $stmt->bindParam(':template_question_text', $template_question_text);
                $stmt->bindParam(':subject_id', $subject_id);
                $stmt->bindParam(':active', $active);
                $stmt->bindParam(':type', $type);
                $stmt->bindParam(':author_id', $by_user_id);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to create template question");
                }
                
                $template_question_id = $conn->lastInsertId();
        
                // Insert into questions
                $stmt = $conn->prepare("INSERT INTO questions (template_question_id) VALUES (:template_question_id)");
                $stmt->bindParam(':template_question_id', $template_question_id);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to create question");
                }
                
                $question_id = $conn->lastInsertId();
                if ($type == 1) {
                    // Insert into answers
                    $stmt = $conn->prepare("INSERT INTO answers (question_id, answer_text) VALUES (?, ?)");
                    $stmt->bindParam(1, $question_id);
                    foreach ($answer_text as $answer) {
                        $stmt->bindParam(2, $answer);
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to insert answer");
                        }
                    }
                }
               
        
                $conn->commit();
                response(["message" => "Question created successfully"], 201);
            } catch (Exception $e) {
                $conn->rollBack();
                response(["error" => $e->getMessage()], 500);
            }
        
            break;
        
    

    default:
        response(["error" => "Method not allowed"], 405);
        break;
}
?>
