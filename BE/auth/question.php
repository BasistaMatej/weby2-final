<?php

// IMPLEMENT - NOT IMPLEMENTED YET
require '../include/db_connect.php';
require_once '../vendor/autoload.php';


$endpoint1 = isset($_GET['endpoint1']) ? $_GET['endpoint1'] : '';
$endpoint2 = isset($_GET['endpoint2']) ? $_GET['endpoint2'] : '';

switch(strtoupper($_SERVER["REQUEST_METHOD"])) {
    case "GET":
        if ($endpoint1 == ""){
            $user = verify_token($conn);
            if (!$user) {
                // The response is already handled within the function
                exit;  // Stop further execution if the token is invalid
            }
    
            // Determine if we need to filter by user_id based on auth_level
            $sql = "SELECT
                        tq.template_question_id, 
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
            if ($endpoint1 == ""){
                $user = verify_token($conn);
                if (!$user) {
                    exit;  // Stop further execution if the token is invalid
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
            }
            break;

        case "DELETE":
            if ($endpoint1 === "/template_question"){
                if (empty($endpoint2)) {
                    response(["error" => "Missing template question ID"], 400);
                    exit;
                }
                
                $template_question_id = $endpoint2;

                $user = verify_token($conn);
                if (!$user) {
                    exit;  // Stop further execution if the token is invalid
                }
            
                try {
                    $conn->beginTransaction();
            
                    // Check if the template question belongs to the user or if user has higher privilege
                    $stmt = $conn->prepare("SELECT author_id FROM template_questions WHERE template_question_id = :template_question_id");
                    $stmt->bindParam(':template_question_id', $template_question_id);
                    $stmt->execute();
                    $question = $stmt->fetch(PDO::FETCH_ASSOC);
            
                    if (!$question) {
                        throw new Exception("Template question not found");
                    }
            
                    if ($question['author_id'] != $user['user_id'] && $user['auth_level'] != 1) {
                        throw new Exception("Unauthorized to delete this question");
                    }


                    // Get all question_id values associated with the given template_question_id
                    $stmt = $conn->prepare("SELECT question_id FROM questions WHERE template_question_id = :template_question_id");
                    $stmt->bindParam(':template_question_id', $template_question_id);
                    $stmt->execute();
                    $question_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    // echo json_encode($question_ids);
                    // Now $question_ids is an array of all question_id values associated with the given template_question_id

                    // Delete associated answers
                    $stmt = $conn->prepare("DELETE FROM answers WHERE question_id = :question_id");
                    foreach ($question_ids as $question_id) {
                        $stmt->bindParam(':question_id', $question_id);
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to delete answers linked to question");
                        }
                    }

                    // Delete associated questions
                    $stmt = $conn->prepare("DELETE FROM questions WHERE template_question_id = :template_question_id");
                    $stmt->bindParam(':template_question_id', $template_question_id);
                    // get the question id
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to delete questions linked to template");
                    }
            
                    // Now delete the template question
                    $stmt = $conn->prepare("DELETE FROM template_questions WHERE template_question_id = :template_question_id");
                    $stmt->bindParam(':template_question_id', $template_question_id);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to delete template question");
                    }
            
                    $conn->commit();
                    response(["message" => "Template question deleted successfully"], 200);
                } catch (Exception $e) {
                    $conn->rollBack();
                    response(["error" => $e->getMessage()], 500);
                }
    
            }

            else if ($endpoint1 === "/question_history"){
                if (empty($endpoint2)) {
                    response(["error" => "Missing question ID"], 400);
                    exit;
                }
                
                $question_id = $endpoint2;

                $user = verify_token($conn);
                if (!$user) {
                    exit;  // Stop further execution if the token is invalid
                }
            
                try {
                    $conn->beginTransaction();
            
                    // Check if the question belongs to the user or if user has higher privilege
                    $stmt = $conn->prepare("SELECT tq.author_id FROM template_questions tq JOIN questions q ON tq.template_question_id = q.template_question_id WHERE q.question_id = :question_id");
                    $stmt->bindParam(':question_id', $question_id);
                    $stmt->execute();
                    $question = $stmt->fetch(PDO::FETCH_ASSOC);
            
                    if (!$question) {
                        throw new Exception("Question not found");
                    }
            
                    if ($question['author_id'] != $user['user_id'] && $user['auth_level'] != 2) {
                        throw new Exception("Unauthorized to delete this question");
                    }

                    // Delete associated answers
                    $stmt = $conn->prepare("DELETE FROM answers WHERE question_id = :question_id");
                    $stmt->bindParam(':question_id', $question_id);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to delete answers linked to question");
                    }

                    // Delete question
                    $stmt = $conn->prepare("DELETE FROM questions WHERE question_id = :question_id");
                    $stmt->bindParam(':question_id', $question_id);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to delete question");
                    }
            
                    $conn->commit();
                    response(["message" => "Question deleted successfully"], 200);
                } catch (Exception $e) {
                    $conn->rollBack();
                    response(["error" => $e->getMessage()], 500);
                }
    
            }

            
        break;
        
        case "OPTIONS":
            break;

    default:
        response(["error" => "Method not allowed"], 405);
        break;
}
?>
