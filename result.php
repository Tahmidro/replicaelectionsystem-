<?php
session_start();
include "config.php";

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all elections that are either ongoing or completed
$electionsSql = "SELECT election_id, title, start_time, end_time, status 
                 FROM elections 
                 WHERE status IN ('ongoing','completed')
                 ORDER BY start_time DESC";
$elections = $conn->query($electionsSql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Election Results</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f9; margin:0; padding:30px; }
        .card { max-width:900px; margin:20px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
        h2 { margin-top:0; color:#333; }
        table { width:100%; border-collapse: collapse; margin-top:15px; }
        th, td { border:1px solid #ddd; padding:10px; text-align:center; }
        th { background:#007bff; color:#fff; }
        tr:nth-child(even) { background:#f9f9f9; }
        .bar { height:18px; background:#007bff; text-align:right; color:#fff; font-size:12px; border-radius:4px; padding-right:5px; }
        .note { font-size:0.9rem; color:#666; margin-top:10px; }
        .btn-back { display:inline-block; margin:20px auto; padding:10px 18px; background:#007bff; color:#fff; border-radius:6px; text-decoration:none; }
        .btn-back:hover { background:#0056b3; }
    </style>
</head>
<body>

    <h2>Election Results</h2>

    <?php
    if ($elections && $elections->num_rows > 0) {
        while ($e = $elections->fetch_assoc()) {
            $eid = (int)$e['election_id'];
            echo "<div class='card'>";
            echo "<h3>".htmlspecialchars($e['title'])." (".htmlspecialchars($e['status']).")</h3>";

            // Fetch candidates & vote counts
            $sql = "SELECT u.name, c.candidate_id, 
                           COUNT(v.vote_id) as total_votes
                    FROM candidates c
                    JOIN users u ON c.user_id = u.user_id
                    LEFT JOIN votes v ON v.candidate_id = c.candidate_id
                    WHERE c.election_id = ?
                    GROUP BY c.candidate_id, u.name";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $eid);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {
                // Get total votes for percentages
                $total_votes = 0;
                $data = [];
                while ($row = $res->fetch_assoc()) {
                    $total_votes += $row['total_votes'];
                    $data[] = $row;
                }

                echo "<table><tr><th>Candidate</th><th>Votes</th><th>Percentage</th></tr>";
                foreach ($data as $r) {
                    $percent = $total_votes > 0 ? round(($r['total_votes'] / $total_votes) * 100, 2) : 0;
                    echo "<tr>";
                    echo "<td>".htmlspecialchars($r['name'])."</td>";
                    echo "<td>".$r['total_votes']."</td>";
                    echo "<td>
                            <div class='bar' style='width:".$percent."%'>".$percent."%</div>
                          </td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "<p class='note'>Total Votes: ".$total_votes."</p>";
            } else {
                echo "<p>No candidates or votes found for this election.</p>";
            }

            echo "</div>";
        }
    } else {
        echo "<p>No elections found.</p>";
    }
    ?>

    <a href="dashboard_voter.php" class="btn-back">⬅ Back to Dashboard</a>

</body>
</html>
