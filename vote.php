<?php
session_start();
include "config.php";

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Get voter record
$stmt = $conn->prepare("SELECT voter_id, is_verified FROM voters WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$voter_result = $stmt->get_result();
$voter = $voter_result->fetch_assoc();

if (!$voter || $voter['is_verified'] != 1) {
    die("❌ You are not a verified voter.");
}

$voter_id = $voter['voter_id'];

// ✅ Get active election
$election_sql = "SELECT * FROM elections WHERE status='ongoing';";
$election_result = $conn->query($election_sql);
$election = $election_result->fetch_assoc();

if (!$election) {
    die("❌ No active election right now.");
}

$election_id = $election['election_id'];

// ✅ Check if already voted
$vote_check = $conn->prepare("SELECT * FROM votes WHERE voter_id=? AND election_id=?");
$vote_check->bind_param("ii", $voter_id, $election_id);
$vote_check->execute();
$already_voted = $vote_check->get_result()->num_rows > 0;

// ✅ Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$already_voted) {
    $candidate_id = intval($_POST['candidate_id']);

    $stmt = $conn->prepare("INSERT INTO votes (voter_id, candidate_id, election_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $voter_id, $candidate_id, $election_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Your vote has been recorded!";
        header("Location: vote.php");
        exit;
    } else {
        $error = "❌ Error submitting vote.";
    }
}

// ✅ Fetch candidates for election
$candidate_sql = "
    SELECT c.candidate_id, u.name, c.party, c.manifesto 
    FROM candidates c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.status='approved' AND c.election_id = ?";
$stmt = $conn->prepare($candidate_sql);
$stmt->bind_param("i", $election_id);
$stmt->execute();
$candidates = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vote - Election System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }
        .container {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #333; }
        .success { color: green; font-weight: bold; text-align: center; }
        .error { color: red; font-weight: bold; text-align: center; }
        .candidate {
            border: 1px solid #ddd;
            margin: 12px 0;
            padding: 15px;
            border-radius: 8px;
        }
        button {
            background: #007bff;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #0056b3;
        }
        .logout {
            display: block;
            margin-top: 20px;
            text-align: center;
            background: #dc3545;
            padding: 10px;
            border-radius: 6px;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Vote in <?= htmlspecialchars($election['title']) ?></h2>

    <?php if (isset($_SESSION['success'])): ?>
        <p class="success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <?php if ($already_voted): ?>
        <p class="success">✅ You have already voted in this election.</p>
    <?php else: ?>
        <form method="POST">
            <?php while ($row = $candidates->fetch_assoc()): ?>
                <div class="candidate">
                    <h3><?= htmlspecialchars($row['name']); ?> (<?= htmlspecialchars($row['party']); ?>)</h3>
                    <p><strong>Manifesto:</strong> <?= htmlspecialchars($row['manifesto']); ?></p>
                    <button type="submit" name="candidate_id" value="<?= $row['candidate_id']; ?>">Vote</button>
                </div>
            <?php endwhile; ?>
        </form>
    <?php endif; ?>

    <a href="dashboard_voter.php" class="logout">⬅ Back to Dashboard</a>
</div>
</body>
</html>
