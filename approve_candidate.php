<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['candidate_id'])) {
    $id = intval($_GET['candidate_id']);
    $stmt = $conn->prepare("UPDATE candidates SET status= 'approved' WHERE candidate_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: approve_candidates.php");
exit;
?>

