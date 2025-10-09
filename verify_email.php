<?php
session_start();
include "config.php";

// Redirect if no pending user
if (!isset($_SESSION['pending_user_id']) || !isset($_SESSION['pending_email'])) {
    header("Location: login.php");
    exit;
}

// Handle OTP submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp']);
    $pending_otp = $_SESSION['email_otp'];
    $otp_expiry = $_SESSION['otp_expiry'];
    $user_id = $_SESSION['pending_user_id'];

    if (time() > $otp_expiry) {
        $error = "OTP has expired! Please login again.";
        session_unset(); // Clear session to force re-login
    } elseif ($entered_otp == $pending_otp) {
        // OTP is correct, finalize login
        $_SESSION['user_id'] = $user_id;

        // Fetch user info
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $_SESSION['is_admin'] = $user['is_admin'];

        // Mark email as verified
        $stmt4 = $conn->prepare("UPDATE users SET email_verified = 1 WHERE user_id=?");
        $stmt4->bind_param("i", $user_id);
        $stmt4->execute();


        // Clear OTP session
        unset($_SESSION['pending_user_id']);
        unset($_SESSION['pending_email']);
        unset($_SESSION['email_otp']);
        unset($_SESSION['otp_expiry']);

        // Redirect based on role
        if ($_SESSION['is_admin'] == 1) {
            header("Location: dashboard_admin.php");
            exit;
        }

        // Check if approved candidate
        $stmt2 = $conn->prepare("SELECT candidate_id FROM candidates WHERE user_id=? AND status='approved'");
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();
        $res = $stmt2->get_result();

        if ($res->num_rows > 0) {
            header("Location: dashboard_candidate.php");
            exit;
        } else {
            header("Location: dashboard_voter.php");
            exit;
        }
    } else {
        $error = "Invalid OTP! Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Email - Election System</title>
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
        .info {
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
  <div class="card">
      <h2>Verify Your Email</h2>
      <p class="info">An OTP has been sent to <strong><?= htmlspecialchars($_SESSION['pending_email']); ?></strong>. It is valid for 5 minutes.</p>

      <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

      <form method="POST">
          <input type="text" name="otp" placeholder="Enter OTP" required>
          <button type="submit">Verify</button>
      </form>

      <div class="info">
          <p>Didn't receive OTP? <a href="login.php">Login again</a> to resend.</p>
      </div>
  </div>
</body>
</html>
