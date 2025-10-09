<?php
session_start();
include "config.php";



require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {

            // Check if email is verified
            if ($user['email_verified'] == 0) {
                $_SESSION['pending_user_id'] = $user['user_id'];
                $_SESSION['pending_email'] = $user['email'];

                // Generate OTP
                $otp = rand(100000, 999999);
                $_SESSION['email_otp'] = $otp;
                $_SESSION['otp_expiry'] = time() + 300; // valid for 5 min

                // Send OTP via PHPMailer
                $mail = $mail = new PHPMailer\PHPMailer\PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';  // Replace with your SMTP host
                    $mail->SMTPAuth = true;
                    $mail->Username = 'tahmidosmani.uiu@gmail.com'; // SMTP username
                    $mail->Password = 'yjoh tzkr dfki aokw';      // SMTP password
                    $mail->SMTPSecure = 'tls';                    // or 'ssl'
                    $mail->Port = 587;                            // or 465 for ssl

                    // Recipients
                    $mail->setFrom('tahmidosmani.uiu@gmail.comm', 'Election System');
                    $mail->addAddress($user['email'], $user['email']);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Your Election System Verification Code';
                    $mail->Body    = "<p>Your OTP code is: <strong>$otp</strong></p>
                                      <p>This code will expire in 5 minutes.</p>";
                    $mail->AltBody = "Your OTP code is: $otp\nThis code will expire in 5 minutes.";

                    $mail->send();
                } catch (Exception $e) {
                    $error = "OTP could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

                if (!isset($error)) {
                    header("Location: verify_email.php");
                    exit;
                }
            }

            // Normal login flow
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['is_admin'] = $user['is_admin'];

            if ($_SESSION['is_admin'] == 1) {
                header("Location: dashboard_admin.php");
                exit;
            }

            $stmt = $conn->prepare("SELECT candidate_id FROM candidates WHERE user_id=? AND status='approved'");
            $stmt->bind_param("i", $user['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                header("Location: dashboard_candidate.php");
                exit;
            } else {
                header("Location: dashboard_voter.php");
                exit;
            }
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Election System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background: #fff;
            padding: 30px 25px;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            width: 350px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-weight: bold;
            font-size: 14px;
            color: #444;
        }
        input {
            width: 95%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: none;
            background: #007bff;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            font-size: 15px;
        }
        button:hover {
            background: #0056b3;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .link {
            margin-top: 12px;
            font-size: 14px;
        }
        .link a {
            text-decoration: none;
            color: #007bff;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
  <div class="card">
    <?php if (isset($_SESSION['success'])): ?>
      <p class="success"><?= htmlspecialchars($_SESSION['success']); ?></p>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
      <h2>Login</h2>

      <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

      <form method="POST">
          <label>Email</label>
          <input type="email" name="email" required>

          <label>Password</label>
          <input type="password" name="password" required>

          <button type="submit">Login</button>
      </form>

      <div class="link">
          <p>Don't have an account? <a href="register.php">Register here</a></p>
      </div>
  </div>
</body>
</html>
