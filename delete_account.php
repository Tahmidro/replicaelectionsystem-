<?php
session_start();
include "config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("delete from users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    session_destroy();
}
session_start();
$_SESSION['flash_msg'] = "Account deleted successfully.";
header("Location: index.php");
exit;
?>
