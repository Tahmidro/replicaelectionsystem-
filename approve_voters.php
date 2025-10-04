<?php
session_start();
include "config.php";

// Get pending voters (not verified yet)
$sql = "SELECT v.voter_id, u.name, u.email, u.nid, v.nid_photo_path, v.self_photo_path, v.is_verified,v.user_id
        FROM voters v 
        JOIN users u ON v.user_id = u.user_id
        WHERE v.is_verified = 0";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Approve Voters</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f4f4f9;
            color: #333;
        }
        h2 {
            text-align: center;
            color: #444;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007BFF;
            color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        img {
            max-width: 120px;
            max-height: 80px;
            border-radius: 4px;
        }
        a.button {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            margin: 2px;
        }
        a.approve {
            background-color: #28a745;
        }
        a.approve:hover {
            background-color: #218838;
        }
        a.reject {
            background-color: #dc3545;
        }
        a.reject:hover {
            background-color: #c82333;
        }
        .btn-back {
            display: block;
            width: 200px;
            margin: 20px auto;
            text-align: center;
            padding: 10px;
            background-color: #007BFF;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Approve Voters</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>NID</th>
            <th>NID Photo</th>
            <th>Self Photo</th>
            <th>Action</th>
        </tr>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['nid']); ?></td>
                    <td>
                        <?php if ($row['nid_photo_path']) { ?>
                            <img src="<?php echo htmlspecialchars($row['nid_photo_path']); ?>" alt="NID Photo">
                        <?php } else { echo "No file"; } ?>
                    </td>
                    <td>
                        <?php if ($row['self_photo_path']) { ?>
                            <img src="<?php echo htmlspecialchars($row['self_photo_path']); ?>" alt="Self Photo">
                        <?php } else { echo "No file"; } ?>
                    </td>
                    <td>
                        <a href="approve_voter.php?user_id=<?php echo $row['user_id']; ?>" class="button approve">Approve</a>
                        <a href="reject_voter.php?user_id=<?php echo $row['user_id']; ?>" class="button reject">Reject</a>
                    </td>
                </tr>
            <?php } ?>
        <?php else: ?>
            <tr><td colspan="6">No pending voters.</td></tr>
        <?php endif; ?>
    </table>
    <a href="dashboard_admin.php" class="btn-back">⬅ Back to Dashboard</a>
</body>
</html>
