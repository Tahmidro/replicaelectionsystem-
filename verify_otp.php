<?php
session_start();
include "config.php";

if (!isset($_SESSION['pending_user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];
    $user_id = $_SESSION['pending_user_id'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id=? AND otp_code=? AND otp_expires >= NOW()");
    $stmt->bind_param("is", $user_id, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Clear OTP
        $stmt2 = $conn->prepare("UPDATE users SET otp_code=NULL, otp_expires=NULL WHERE user_id=?");
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();

        // Final login
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['is_admin'] = $user['is_admin'];
        unset($_SESSION['pending_user_id']);

        if ($_SESSION['is_admin'] == 1) {
            header("Location: dashboard_admin.php");
            exit;
        }

        // Check candidate
        $stmt3 = $conn->prepare("SELECT candidate_id FROM candidates WHERE user_id=? AND status='approved'");
        $stmt3->bind_param("i", $user_id);
        $stmt3->execute();
        $res = $stmt3->get_result();

        if ($res->num_rows > 0) {
            header("Location: dashboard_candidate.php");
            exit;
        } else {
            header("Location: dashboard_voter.php");
            exit;
        }
    } else {
        $error = "Invalid or expired OTP!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
</head>
<body>
    <h2>Enter OTP</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>
