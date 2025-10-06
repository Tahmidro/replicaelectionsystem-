<?php
session_start();
include "config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['voter_id'])) {
    $voter_id = intval($_GET['voter_id']);
    $stmt = $conn->prepare("UPDATE voters SET is_verified=1 WHERE voter_id=?");
    $stmt->bind_param("i", $voter_id);
    $stmt->execute();
}

header("Location: approve_voters.php");
exit;
