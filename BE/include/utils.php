<?php
    require_once str_replace('include','',__DIR__) . 'vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    use \Firebase\JWT\JWT;

    function response($message, $code = 200) {
      http_response_code($code);
      $message = json_encode($message);
      die($message);
    }

    function send_email($subject, $body, $email) {
        //Server settings
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'mailproxy.nameserver.sk';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'webyfinalne@basista.sk';
        $mail->Password   = 'fHg4g2rV';
        $mail->Port       = 25;

        //Recipients
        $mail->setFrom('webyfinalne@basista.sk', 'Classroom Interact');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
    }

    function generate_jwt($email, $expiration_time_minutes) {
      $key = "w€byF1n4ln3";  // Use the key directly if not using base64 everywhere
      $payload = [
          'email' => $email,
          'exp' => time() + ($expiration_time_minutes * 60)
      ];
      return JWT::encode($payload, $key, 'HS256');
  }
  
  function verify_jwt($jwt) {
    $key = "w€byF1n4ln3";  // Use the correct key
    try {
        // Attempt to decode the token using the same key and algorithm
        return Firebase\JWT\JWT::decode($jwt, new Firebase\JWT\Key($key, 'HS256'));
    } catch (Firebase\JWT\ExpiredException $e) {
        // Token has expired; return a specific error or false
        return 'expired';  // Indicates token is expired
    } catch (\DomainException $e) {
        // This catch block is for corrupted JSON specifically
        return 'malformed';  // Indicates malformed token
    } catch (\UnexpectedValueException $e) {
        // This exception is thrown if the token is otherwise invalid
        return 'malformed';  // General malformed token catch-all
    } catch (Exception $e) {
        // Other errors (e.g., invalid signature, general decoding error)
        return false;
    }
}


function verify_token($conn, $token = null, $valid = true) {
  // Check if the token is provided in the function call if not, check the Authorization header
  if (!$token) {
      if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
          response(['error' => 'Authorization header not found'], 401);
          return false;
      }

      if (preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
          $token = $matches[1];
      } else {
          response(['error' => 'Bearer token not found'], 401);
          return false;
      }
  }

  // Verify the token
  $decoded = verify_jwt($token);
  switch ($decoded) {
      case 'expired':
          response(['error' => 'Token expired'], 401);
      case 'malformed':
          response(['error' => 'Malformed token'], 401);
          return false;
      case false:
          response(['error' => 'Invalid token'], 401);
          return false;
  }

  // If the token is valid, fetch user details
  $stmt = $conn->prepare("SELECT user_id, email, valid, auth_level FROM users WHERE email = :email");
  $stmt->bindParam(':email', $decoded->email);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
      response(['error' => 'User not found'], 404);
      return false;
  }

  if ($user['valid'] == 0 && $valid) {
      response(['error' => 'Account not verified'], 401);
      return false;
  }

  if ($user['auth_level'] == -1) {
      response(['error' => 'Account blocked'], 401);
      return false;
  }

  return $user;  // return the user array with user details
}


?>