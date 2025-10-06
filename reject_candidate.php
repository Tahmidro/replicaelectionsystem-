<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['candidate_id'])) {
    $candidate_id = intval($_GET['candidate_id']);
    $stmt = $conn->prepare("update candidates set status='rejected' WHERE candidate_id = ?");
    $stmt->bind_param("i", $candidate_id);
    $stmt->execute();
}

header("Location: approve_candidates.php");
exit;
?>
