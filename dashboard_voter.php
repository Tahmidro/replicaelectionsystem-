<?php
session_start();
include "config.php";

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if user is an approved voter
// Only fetch status, do NOT auto-update
$stmt = $conn->prepare("SELECT is_verified FROM voters WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$voter_result = $stmt->get_result();
$voter_row = $voter_result->fetch_assoc();
$voter_status = ($voter_row != null) ? (int)$voter_row['is_verified'] : 0;
// Check if candidate record exists & get status
$stmt2 = $conn->prepare("SELECT status FROM candidates WHERE user_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$candidate_result = $stmt2->get_result();
$candidate_row = $candidate_result->fetch_assoc();
$candidate_status = $candidate_row ? $candidate_row['status'] : null;
?>



?>
<!DOCTYPE html>
<html>
<head>
    <title>Voter Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        h2 {
            color: #444;
            margin-bottom: 30px;
        }
        .button {
            display: block;
            width: 220px;
            text-align: center;
            padding: 12px;
            margin: 12px 0;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .logout {
            background-color: #dc3545;
        }
        .logout:hover {
            background-color: #c82333;
        }
        .container {
            background-color: #fff;
            padding: 40px 60px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome Voter</h2>

        <?php if ($voter_status === 1): ?>
    <a href="face_auth.php" class="button">Verify Face & Vote</a>
    <a href="view_results.php" class="button">View Results</a>

    <?php if (!$candidate_status || $candidate_status === 'rejected'): ?>
        <!-- Show apply candidate button only if no candidate record or rejected -->
        <a href="apply_candidate.php" class="button">Register as a Candidate</a>
    <?php endif; ?>

<?php else: ?>
    <a href="voter_register.php" class="button">Register as a Voter</a>
<?php endif; ?>

        <a href="logout.php" class="button logout">Logout</a>
    </div>
</body>
</html>
