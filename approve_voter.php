<?php
session_start();
include "config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $stmt = $conn->prepare("UPDATE voters SET is_verified=1 WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

header("Location: approve_voters.php");
exit;
