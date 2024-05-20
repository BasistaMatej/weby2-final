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
                        tq.type,
                        tq.code, 
                        tq.active,
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
                response(['message' => 'No questions found'], 204); // Provide a not found status if no results
            } else {
                response(['questions' => $questions], 200); // Return questions if found
            }
        } else if($endpoint1 == '/answers') { 
            if (empty($endpoint2) || !is_numeric($endpoint2)) {
              response(["error" => "Missing question ID"], 400);
              exit;
            }

            /*$user = verify_token($conn);
            if (!$user) {
                // The response is already handled within the function
                exit;  // Stop further execution if the token is invalid
            }*/
    
            $stmt = $conn->prepare("
            SELECT a.*
              FROM answers a
              JOIN questions q ON a.question_id = q.question_id
              JOIN template_questions tq ON q.template_question_id = tq.template_question_id
              WHERE tq.template_question_id = :template_question_id
              AND q.closed IS NULL
              AND tq.type = 1
            ");

            $stmt->bindParam(':template_question_id', $endpoint2, PDO::PARAM_INT);
            $stmt->execute();

            $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($answers)) {
                response(['message' => 'No answers found'], 204);
            } else {
                response(['answers' => $answers], 200);
            }
        
        }
        else if ($endpoint1 == "/question_history"){
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

                if ($question['author_id'] != $user['user_id'] && $user['auth_level'] != 2) {
                    throw new Exception("Unauthorized to read this question history");
                }
               
                // show the history of the question
                $stmt = $conn->prepare("SELECT * FROM questions WHERE template_question_id = :template_question_id  AND closed IS NOT NULL");
                $stmt->bindParam(':template_question_id', $template_question_id);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to read question history");
                }

                // get that whole history
                $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($questions) == 0){
                    throw new Exception("No history found for this question");
                }
                
                // to every question in the history, get the answers
                foreach ($questions as $key => $question) {
                    $stmt = $conn->prepare("SELECT answer_id, answer_text, count FROM answers WHERE question_id = :question_id");
                    $stmt->bindParam(':question_id', $question['question_id']);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to read answers");
                    }
                    $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $questions[$key]['answers'] = $answers;
                }

                $conn->commit();
                // response(["message" => "Question history read successfully"], 200);
                response(['questions' => $questions], 200);

            } catch (Exception $e) {
                $conn->rollBack();
                response(["error" => $e->getMessage()], 500);
            }

        }
        else if ($endpoint1 == "/export_questions"){
            // GET every question template with question with answers ans with subject name
        
            $user = verify_token($conn);
            if (!$user) {
                exit;  // Stop further execution if the token is invalid
            }
            
            try {
                $conn->beginTransaction();
        
                // Check if the user has higher privilege
                if ($user['auth_level'] < 1) {
                    throw new Exception("Unauthorized to export questions");
                }
        
                // get all questions
                $stmt = $conn->prepare("SELECT tq.template_question_id, tq.template_question_text, tq.active, tq.type, tq.code, tq.created, s.subject_name, q.question_id, q.closed, q.note FROM template_questions tq JOIN subjects s ON tq.subject_id = s.subject_id JOIN questions q ON tq.template_question_id = q.template_question_id");
                $stmt->execute();
                $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                if (!$questions) {
                    throw new Exception("No questions found");
                }
        
                // get all answers for each question
                foreach ($questions as $key => $question) {
                    $stmt = $conn->prepare("SELECT a.question_id, a.answer_text, a.count FROM answers a WHERE a.question_id = :question_id");
                    $stmt->bindParam(':question_id', $question['question_id']);
                    $stmt->execute();
                    $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                    // Add the answers to the question
                    $questions[$key]['answers'] = $answers;
                }
        
                $conn->commit();
        
                // Convert data to JSON
                $json_data = json_encode($questions);
        
                // Set headers to force download
                header('Content-Type: application/json');
                header('Content-Disposition: attachment; filename="exported_questions.json"');
                echo $json_data;
                
            } catch (Exception $e) {
                $conn->rollBack();
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(["error" => $e->getMessage()]);
            }
        } else if($endpoint1 == '/code') {
          $code = $endpoint2;

          // select from db via code
          $stmt = $conn->prepare("SELECT active, type, template_question_text, template_question_id FROM template_questions WHERE  code = :code");
          $stmt->bindParam(':code', $code);
          $stmt->execute();
          $question = $stmt->fetch(PDO::FETCH_ASSOC);

          if (!$question) {
              response(['error' => 'Question not found'], 404);
          } else {
              response(['question' => $question], 200);
          }

        } else{
            response(["error" => "Invalid endpoint"], 405);
        }
      break;

        case "POST":
            if ($endpoint1 == ""){
                $user = verify_token($conn);
                if (!$user) {
                    exit;  // Stop further execution if the token is invalid
                }
            
            
                $postdata = json_decode(file_get_contents("php://input"), true);

                if( !isset($postdata['template_question_text']) || !isset($postdata['subject_name']) || 
                    !isset($postdata['active']) || !isset($postdata['type'])  || !isset($postdata['answer_text']))
                {
                    response(['error' => 'Missing required fields'], 400);
                    exit;
                }

                $template_question_text = $postdata['template_question_text'];
                $subject_name = $postdata['subject_name'];
                $active = $postdata['active'];
                $type = $postdata['type'];
                $answer_text = $postdata['answer_text'];

                if ($user['auth_level'] == 2 && isset($postdata['by_user_id'])) {
                    $by_user_id = $postdata['by_user_id'];
                    
                }
                else{
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

                    $code_exists = null;
                    $code = null;
                    // if active is set to 1, generate a random 5 symbols code
                    if($active == 1){
                        $attempts = 0;
                        while($attempts < 1000){
                            // generate new random 5 symbols code
                            $code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);

                            // check if the code is unique
                            $stmt = $conn->prepare("SELECT code FROM template_questions WHERE code = :code");
                            $stmt->bindParam(':code', $code);
                            $stmt->execute();
                            $code_exists = $stmt->fetch(PDO::FETCH_ASSOC);

                            if (!$code_exists){
                                break;
                            }
                            $attempts++;
                        }
                        if ($attempts == 1000) {
                            throw new Exception("Failed to generate a unique code after 1000 attempts");
                        }

                       
                    }
            
                    // Insert into template questions
                    $stmt = $conn->prepare("INSERT INTO template_questions (template_question_text, subject_id, active, code, type, author_id) VALUES (:template_question_text, :subject_id, :active, :code, :type, :author_id)");
                    $stmt->bindParam(':template_question_text', $template_question_text);
                    $stmt->bindParam(':subject_id', $subject_id);
                    $stmt->bindParam(':active', $active);
                    $stmt->bindParam(':code', $code);
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
            else if ($endpoint1 === "/question_template_copy"){
                $user = verify_token($conn);
                if (!$user) {
                    exit;  // Stop further execution if the token is invalid
                }

                $template_question_id = $endpoint2;

                try {
                    $conn->beginTransaction();
                    
                    $postdata = json_decode(file_get_contents("php://input"), true);
                    
                    // if empty postdata user_id, use the user_id from the token
                    if ($user['auth_level'] == 2 && isset($postdata['by_user_id'])){
                        $by_user_id = $postdata['by_user_id'];
                    }
                    else{
                        $by_user_id = $user['user_id'];
                    }

                    // Get the template question
                    $stmt = $conn->prepare("SELECT * FROM template_questions WHERE template_question_id = :template_question_id");
                    $stmt->bindParam(':template_question_id', $template_question_id);
                    $stmt->execute();
                    $template_question = $stmt->fetch(PDO::FETCH_ASSOC);
            
                    if (!$template_question) {
                        throw new Exception("Template question not found");
                    }
            
                    if ($template_question['author_id'] != $user['user_id'] && $user['auth_level'] != 2) {
                        throw new Exception("Unauthorized to copy this question");
                    }

                    // Insert into template questions
                    $stmt = $conn->prepare("INSERT INTO template_questions (template_question_text, subject_id, active, type, author_id) VALUES (:template_question_text, :subject_id, :active, :type, :author_id)");
                    $stmt->bindParam(':template_question_text', $template_question['template_question_text']);
                    $stmt->bindParam(':subject_id', $template_question['subject_id']);
                    $stmt->bindParam(':active', $template_question['active']);
                    $stmt->bindParam(':type', $template_question['type']);
                    $stmt->bindParam(':author_id', $by_user_id);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to create template question");
                    }

                    $new_template_question_id = $conn->lastInsertId();

                    // insert into question
                    $stmt = $conn->prepare("INSERT INTO questions (template_question_id) VALUES (:template_question_id)");
                    $stmt->bindParam(':template_question_id', $new_template_question_id);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to create question");
                    }

                    $new_question_id = $conn->lastInsertId();


                    if ($template_question['type'] == 1) {
                        // get question_id from the question table
                        $stmt = $conn->prepare("SELECT question_id FROM questions WHERE template_question_id = :template_question_id");
                        $stmt->bindParam(':template_question_id', $template_question_id);
                        $stmt->execute();
                        $question_id = $stmt->fetch(PDO::FETCH_ASSOC);

                        // Get all answers associated with the question_id
                        $stmt = $conn->prepare("SELECT answer_text FROM answers WHERE question_id = :question_id");
                        $stmt->bindParam(':question_id', $question_id['question_id']);
                        $stmt->execute();
                        $answers = $stmt->fetchAll(PDO::FETCH_COLUMN);

                        // Insert into answers
                        $stmt = $conn->prepare("INSERT INTO answers (question_id, answer_text) VALUES (:question_id, :answer_text)");
                        $stmt->bindParam(':question_id', $new_question_id);
                        foreach ($answers as $answer) {
                            $stmt->bindParam(':answer_text', $answer);
                            if (!$stmt->execute()) {
                                throw new Exception("Failed to insert answer");
                            }
                        }
                    }

                    $conn->commit();
                    response(["message" => "Template question coopied successfully"], 200);
                } catch (Exception $e) {
                    $conn->rollBack();
                    response(["error" => $e->getMessage()], 500);
                }

                
            }
            else if ($endpoint1 == '/answers') {
                if (empty($endpoint2) || !is_numeric($endpoint2)) {
                    response(["error" => "Missing or invalid question ID"], 400);
                    exit;
                }
            
                $user = verify_token($conn);
                if (!$user) {
                    exit;  // Stop further execution if the token is invalid
                }
            
                $postdata = json_decode(file_get_contents("php://input"), true);
                if (!isset($postdata['all_answers']) || !is_array($postdata['all_answers'])) {
                    response(['error' => 'Missing or invalid "all_answers" field'], 400);
                    exit;
                }
            
                $question_id = $endpoint2;
                $answer_array = $postdata['all_answers'];
                $note = $postdata['note'];
                // $template_question_id = $postdata['template_question_id'];
            
                try {
                    $conn->beginTransaction();

                    // Check if the question belongs to the user or if the user has higher privileges and get type of the question
                    $stmt = $conn->prepare("SELECT tq.author_id, tq.type FROM template_questions tq JOIN questions q ON tq.template_question_id = q.template_question_id WHERE q.question_id = :question_id");
                    $stmt->bindParam(':question_id', $question_id);
                    $stmt->execute();
                    $question = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$question) {
                        throw new Exception("Question not found");
                    }
                    
                    if ($question['author_id'] != $user['user_id'] && $user['auth_level'] != 2) {
                        throw new Exception("Unauthorized to add answers to this question");
                    }
            
                    // Insert each answer into the answers table if the question type is 0

                    if ($question['type'] == 0) {
                        $stmt = $conn->prepare("INSERT INTO answers (question_id, answer_text, count) VALUES (:question_id, :answer_text, :count)");
                        foreach ($answer_array as $answer_text => $count) {
                            $stmt->bindParam(':question_id', $question_id);
                            $stmt->bindParam(':answer_text', $answer_text);
                            $stmt->bindParam(':count', $count);
                            if (!$stmt->execute()) {
                                throw new Exception("Failed to insert answer: $answer_text");
                            }
                        }
                    }
                    else if ($question['type'] == 1) {
                        // only update the count of the answers
                        $stmt = $conn->prepare("UPDATE answers SET count = :count WHERE question_id = :question_id AND answer_text = :answer_text");
                        foreach ($answer_array as $answer_text => $count) {
                            $stmt->bindParam(':question_id', $question_id);
                            $stmt->bindParam(':answer_text', $answer_text);
                            $stmt->bindParam(':count', $count);
                            if (!$stmt->execute()) {
                                throw new Exception("Failed to update answer: $answer_text");
                            }
                        }
                        
                    }
                    else {
                        throw new Exception("Invalid question type");
                    }                    

                    // set question to closed
                    $stmt = $conn->prepare("UPDATE questions SET closed = NOW(), note = :note WHERE question_id = :question_id");
                    $stmt->bindParam(':question_id', $question_id);
                    $stmt->bindParam(':note', $note);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to close the question");
                    }
                    
                    $conn->commit();
                    response(["message" => "Answers added successfully"], 201);
                } catch (Exception $e) {
                    $conn->rollBack();
                    response(["error" => $e->getMessage()], 500);
                }
            }
            else if($endpoint1== "/open"){
                // GET question with answers and create new question

                if (empty($endpoint2) || !is_numeric($endpoint2)) {
                    response(["error" => "Missing or invalid template_question_id"], 400);
                    exit;
                }
            
                $user = verify_token($conn);
                if (!$user) {
                    exit;  // Stop further execution if the token is invalid
                }

                $template_question_id = $endpoint2;

                try{
                    $conn->beginTransaction();
                    
                    // get the question_id which isn't opened
                    $stmt = $conn->prepare("SELECT question_id FROM questions WHERE template_question_id = :template_question_id AND closed IS NULL");
                    $stmt->bindParam(':template_question_id', $template_question_id);
                    $stmt->execute();
                    $question_id = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$question_id) {
                        throw new Exception("No question found");
                    }

                    if (count($question_id) >1 ) {
                        throw new Exception("Multiple not closed questions found");
                    }

                    //check if in question template is only type 
                    $stmt = $conn->prepare("SELECT type FROM template_questions WHERE template_question_id = :template_question_id");
                    $stmt->bindParam(':template_question_id', $template_question_id);
                    $stmt->execute();
                    $type = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$type) {
                        throw new Exception("Template question not found");
                    }

                    if ($type['type'] == 1) {
                         // get the answers from the question
                        $stmt = $conn->prepare("SELECT answer_id, answer_text FROM answers WHERE question_id = :question_id");
                        $stmt->bindParam(':question_id', $question_id['question_id']);
                        $stmt->execute();
                        $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                    else{
                        $answers = null;
                    }

                    // insert new question
                    $stmt = $conn->prepare("INSERT INTO questions (template_question_id) VALUES (:template_question_id)");
                    $stmt->bindParam(':template_question_id', $template_question_id);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to create question");
                    }
                    
                    $new_question_id = $conn->lastInsertId();

                    // insert answers if type is 1
                    if ($type['type'] == 1) {
                        $stmt = $conn->prepare("INSERT INTO answers (question_id, answer_text) VALUES (:question_id, :answer_text)");
                        $stmt->bindParam(':question_id', $new_question_id);
                        foreach ($answers as $answer) {
                            $stmt->bindParam(':answer_text', $answer['answer_text']);
                            if (!$stmt->execute()) {
                                throw new Exception("Failed to insert answer");
                            }
                        }
                    }

                    $conn->commit();
                    response(["message" => "Question opened successfully", "question_id" => $question_id, "answers" => $answers], 200);
                } catch (Exception $e) {
                    $conn->rollBack();
                    response(["error" => $e->getMessage()], 500);
                }
            }
            else {
                response(["error" => "Invalid endpoint"], 405);
            }
        break;
        


        case "PUT":
            if ($endpoint1 === "/set_active"){
                if (empty($endpoint2)) {
                    response(["error" => "Missing template question ID"], 400);
                    exit;
                }
                
                $template_question_id = $endpoint2;

                $user = verify_token($conn);
                if (!$user) {
                    exit;  // Stop further execution if the token is invalid
                }
                
                $postdata = json_decode(file_get_contents("php://input"), true);
                // $setActivity = $postdata['active'];
               
                if (isset($postdata['active'])){
                    $setActivity = $postdata['active'];
                }
                else{
                    response(["error" => "Missing active status"], 400);
                    exit;
                }
                
                try {
                    $conn->beginTransaction();
            
                    // Check if the template question belongs to the user or if user has higher privilege
                    $stmt = $conn->prepare("SELECT author_id, code, active FROM template_questions WHERE template_question_id = :template_question_id");
                    $stmt->bindParam(':template_question_id', $template_question_id);
                    $stmt->execute();
                    $question = $stmt->fetch(PDO::FETCH_ASSOC);
            
                    if (!$question) {
                        throw new Exception("Template question not found");
                    }
            
                    if ($question['author_id'] != $user['user_id'] && $user['auth_level'] != 2) {
                        throw new Exception("Unauthorized to update this question");
                    }

                    // check if the user set the activity to the same activity

                    if ($question['active'] == $setActivity){
                        throw new Exception("The activity is already set to the same value");
                    }

                    if($setActivity == "1"){
                        // generate new random 5 symbols code
                        while(true){
                            // check if is set some code
                            if (!isset($question['code'])){
                                // generate new random 5 symbols code
                                $code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);

                                // check if the code is unique
                                $stmt = $conn->prepare("SELECT code FROM template_questions WHERE code = :code");
                                $stmt->bindParam(':code', $code);
                                $stmt->execute();
                                $code_exists = $stmt->fetch(PDO::FETCH_ASSOC);

                                if (!$code_exists){
                                    break;
                                }
                            }
                        }
                    }
                    else{
                        $code = null;
                    }

                    // Update the active status
                    $stmt = $conn->prepare("UPDATE template_questions SET active = :active, code = :code WHERE template_question_id = :template_question_id");
                    $stmt->bindParam(':active', $setActivity);
                    $stmt->bindParam(':template_question_id', $template_question_id);
                    $stmt->bindParam(':code', $code);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to update active status");
                    }
            
                    $conn->commit();
                    response(["message" => "Template question updated successfully"], 200);
                } catch (Exception $e) {
                    $conn->rollBack();
                    response(["error" => $e->getMessage()], 500);
                }
                
            
            } else if ($endpoint1 == '/template' && !empty($endpoint2) && is_numeric($endpoint2)) {
              $user = verify_token($conn);
              if (!$user) {
                  exit;  // Stop further execution if the token is invalid
              }
          
              $template_question_id = $endpoint2;
              $postdata = json_decode(file_get_contents("php://input"), true);

              if( !isset($postdata['template_question_text']) || !isset($postdata['subject_name']) || 
                  !isset($postdata['active']) || !isset($postdata['type'])  || !isset($postdata['answer_text']))
              {
                  response(['error' => 'Missing required fields'], 400);
                  exit;
              }

              $template_question_text = $postdata['template_question_text'];
              $subject_name = $postdata['subject_name'];
              $active = $postdata['active'];
              $type = $postdata['type'];
              $answer_text = $postdata['answer_text'];

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
          
                  if ($question['author_id'] != $user['user_id'] && $user['auth_level'] != 2) {
                      throw new Exception("Unauthorized to update this question");
                  }

                  if($active == 1) {
                    $code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
                  } else {
                    $code = null;
                  }

                  // Get subject id by name 
                  $stmt = $conn->prepare("SELECT subject_id FROM subjects WHERE subject_name = :subject_name");
                  $stmt->bindParam(':subject_name', $subject_name);
                  $stmt->execute();
                  $subject = $stmt->fetch(PDO::FETCH_ASSOC);

                  if (!$subject) {
                      throw new Exception("Subject not found");
                  }
          
                  // Update the template question
                  $stmt = $conn->prepare("UPDATE template_questions SET template_question_text = :template_question_text, active = :active, type = :type, code = :code, subject_id = :subject_id WHERE template_question_id = :template_question_id");
                  $stmt->bindParam(':template_question_text', $template_question_text);
                  $stmt->bindParam(':active', $active);
                  $stmt->bindParam(':type', $type);
                  $stmt->bindParam(':subject_id', $subject['subject_id']);
                  $stmt->bindParam(':template_question_id', $template_question_id);
                  $stmt->bindParam(':code', $code);
                  if (!$stmt->execute()) {
                      throw new Exception("Failed to update template question");
                  }

                  if($type == 1) {
                    // Get the question_id
                    $stmt = $conn->prepare("SELECT question_id FROM questions WHERE template_question_id = :template_question_id AND closed is NULL");
                    $stmt->bindParam(':template_question_id', $template_question_id);
                    $stmt->execute();
                    $question_id = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Get all answers
                    $stmt = $conn->prepare("SELECT answer_id, answer_text FROM answers WHERE question_id = :question_id");
                    $stmt->bindParam(':question_id', $question_id['question_id']);
                    $stmt->execute();
                    $answersDB = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Update the answers
                    $stmt = $conn->prepare("UPDATE answers SET answer_text = :answer_text WHERE answer_id = :answer_id");
                    foreach($answer_text as $key => $answer) {
                      $stmt->bindParam(':answer_id', $answer['answer_id']);
                      $stmt->bindParam(':answer_text', $answer['answer_text']);
                      if (!$stmt->execute()) {
                        throw new Exception("Failed to update answer");
                      }
                      
                      // delete $answer from $answersDB
                      foreach($answersDB as $key2 => $answerDB) {
                        if($answerDB['answer_id'] == $answer['answer_id']) {
                          unset($answersDB[$key2]);
                        }
                      }
                    }

                    // Insert new ansers
                    $stmt = $conn->prepare("INSERT INTO answers (question_id, answer_text) VALUES (:question_id, :answer_text)");
                    foreach($answer_text as $key => $answer) {
                      if($answer['answer_id'] == null)  {
                        $stmt->bindParam(':question_id', $question_id['question_id']);
                        $stmt->bindParam(':answer_text', $answer['answer_text']);
                        if (!$stmt->execute()) {
                          throw new Exception("Failed to insert answer");
                        }
                        unset($answer_text[$key]);
                      }
                    }

                    // delete unuseed answers
                    $stmt = $conn->prepare("DELETE FROM answers WHERE answer_id = :answer_id");
                    foreach($answersDB as $answer) {
                      $stmt->bindParam(':answer_id', $answer['answer_id']);
                      if (!$stmt->execute()) {
                        throw new Exception("Failed to delete answer");
                      }
                    }
                  }
          
                  $conn->commit();
                  response(["message" => "Template question updated successfully"], 200);
              } catch (Exception $e) {
                  $conn->rollBack();
                  response(["error" => $e->getMessage()], 500);
                  response(["error" => "Server error"], 500);
              }
            }
            else{
                response(["error" => "Invalid endpoint"], 405);
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
            
                    if ($question['author_id'] != $user['user_id'] && $user['auth_level'] != 2) {
                        throw new Exception("Unauthorized to delete this question");
                    }


                    // Get all question_id values associated with the given template_question_id
                    $stmt = $conn->prepare("SELECT question_id FROM questions WHERE template_question_id = :template_question_id");
                    $stmt->bindParam(':template_question_id', $template_question_id);
                    $stmt->execute();
                    $question_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

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

            else if ($endpoint1 == "/question_history"){
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
            else{
                response(["error" => "Invalid endpoint"], 405);
            }

            
        break;
        
        case "OPTIONS":
            break;

    default:
        response(["error" => "Method not allowed"], 405);
        break;
}
?>
