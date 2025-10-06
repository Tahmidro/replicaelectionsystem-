<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidate_id = isset($_POST['candidate_id']) ? intval($_POST['candidate_id']) : 0;
    $election_id  = isset($_POST['election_id']) ? intval($_POST['election_id']) : 0;

    if ($candidate_id > 0 && $election_id > 0) {
        $stmt = $conn->prepare("UPDATE candidates SET election_id = ? WHERE candidate_id = ?");
        $stmt->bind_param("ii", $election_id, $candidate_id);
        if ($stmt->execute()) {
            $message = "<span class='success'>Candidate assigned successfully.</span>";
        } else {
            $message = "<span class='error'>Assignment failed: " . htmlspecialchars($stmt->error) . "</span>";
        }
        $stmt->close();
    } else {
        $message = "<span class='error'>Please select a candidate and an election.</span>";
    }
}

$candidatesSql = "
    SELECT c.candidate_id, u.name, u.email
    FROM candidates c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.status = 'approved' AND c.election_id IS NULL
    ORDER BY u.name
";
$candidates = $conn->query($candidatesSql);

$elections = $conn->query("SELECT election_id, title, start_time, end_time FROM elections ORDER BY start_time DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Assign Candidate to Election</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f9; margin:0; padding:30px; }
        .card { max-width:560px; margin:0 auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
        h2{margin-top:0}
        label{display:block;margin:8px 0 6px;font-weight:600}
        select, button { width:100%; padding:10px; border-radius:6px; border:1px solid #ccd; margin-bottom:10px; }
        button { background:#007bff; color:#fff; border:none; cursor:pointer; }
        button:hover{background:#0056b3}
        .success{color:green}
        .error{color:red}
        .note{font-size:0.9rem;color:#666;margin-top:8px}
    </style>
</head>
<body>
  <div class="card">
    <h2>Assign Candidate to Election</h2>

    <?php if ($message) echo $message; ?>

    <form method="post">
        <label for="candidate_id">Select Candidate (approved & unassigned)</label>
        <select name="candidate_id" required>
            <option value="">-- choose candidate --</option>
            <?php
            if ($candidates && $candidates->num_rows > 0) {
                while ($r = $candidates->fetch_assoc()) {
                    $cid = (int)$r['candidate_id'];
                    echo '<option value="'.$cid.'">'.htmlspecialchars($r['name'].' ('.$r['email'].')').'</option>';
                }
            } else {
                echo '<option value="">No approved, unassigned candidates available</option>';
            }
            ?>
        </select>

        <label for="election_id">Select Election</label>
        <select name="election_id" required>
            <option value="">-- choose election --</option>
            <?php
            if ($elections && $elections->num_rows > 0) {
                while ($e = $elections->fetch_assoc()) {
                    $eid = (int)$e['election_id'];
                    $label = $e['title'] . " (" . $e['start_date'] . " → " . $e['end_date'] . ")";
                    echo '<option value="'.$eid.'">'.htmlspecialchars($label).'</option>';
                }
            } else {
                echo '<option value="">No elections found</option>';
            }
            ?>
        </select>

        <button type="submit">Assign Candidate</button>
    </form>

    <p class="note">Tip: only approved candidates who are not already assigned to an election are shown here.</p>
    <p class="note"><a href="dashboard_admin.php">← Back to Admin Dashboard</a></p>
  </div>
</body>
</html>
