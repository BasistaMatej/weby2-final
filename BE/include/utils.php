<?php
    require_once str_replace('include','',__DIR__) . 'vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    function response($message, $code = 200) {
      http_response_code($code);
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
      $key = "w€byF1n4ln3";
      $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
      $payload = json_encode(['email' => $email, 'exp' => time() + ($expiration_time_minutes * 60)]);
      $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
      $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
      $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $key, true);
      $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
      $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
      return $jwt;
    }
    
    function verify_jwt($jwt) {
      $key = "w€byF1n4ln3";
      $jwt_values = explode('.', $jwt);
      $recieved_signature = $jwt_values[2];
      $recieved_header_and_payload = $jwt_values[0] . '.' . $jwt_values[1];
      $expected_signature = hash_hmac('sha256', $recieved_header_and_payload, $key, true);
      $expected_signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expected_signature));
      if($recieved_signature == $expected_signature) {
        $payload = json_decode(base64_decode($jwt_values[1]), true);
        if(isset($payload['exp']) && $payload['exp'] >= time()) {
          if(isset($payload['email'])) {
            return $payload['email'];
          }
        }
      }
      return false;
    }

?>