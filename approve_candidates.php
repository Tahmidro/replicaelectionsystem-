<?php
session_start();
include "config.php";

// Fetch pending candidates with voter info
$sql = "SELECT u.name, u.email, u.nid, 
               v.nid_photo_path, v.self_photo_path,
               c.status, c.candidate_id, 
               c.party, c.manifesto
        FROM candidates c
        JOIN users u ON c.user_id = u.user_id
        JOIN voters v ON c.user_id = v.user_id
        WHERE c.status = 'pending';";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Approve Candidates</title>
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
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            background: #fff;
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }
        th {
            background-color: #007BFF;
            color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
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
        .approved {
            color: green;
            font-weight: bold;
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
        img.preview {
            width: 80px;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 2px;
            background: #fafafa;
            cursor: pointer;
            transition: transform 0.2s;
        }
        img.preview:hover {
            transform: scale(1.05);
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            overflow: auto;
        }
        .modal-content {
            margin: 5% auto;
            display: block;
            max-width: 90%;
            max-height: 80vh;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .close {
            position: absolute;
            top: 20px;
            right: 40px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #bbb;
        }
    </style>
</head>
<body>
    <h2>Approve Candidates</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>NID</th>
            <th>NID Photo</th>
            <th>Self Photo</th>
            <th>Party</th>
            <th>Manifesto</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['nid']); ?></td>
                    <td>
                        <img src="<?php echo htmlspecialchars($row['nid_photo_path']); ?>" 
                             alt="NID Photo" class="preview" 
                             onclick="openModal(this.src)">
                    </td>
                    <td>
                        <img src="<?php echo htmlspecialchars($row['self_photo_path']); ?>" 
                             alt="Self Photo" class="preview" 
                             onclick="openModal(this.src)">
                    </td>
                    <td><?php echo htmlspecialchars($row['party']); ?></td>
                    <td style="max-width:200px; text-align:left;">
                        <?php echo nl2br(htmlspecialchars($row['manifesto'])); ?>
                    </td>
                    <td class="<?php echo $row['status']=='approved' ? 'approved' : ''; ?>">
                        <?php echo ($row['status'] == 'approved') ? "Approved" : "Pending"; ?>
                    </td>
                    <td>
                        <?php if ($row['status'] !='approved') { ?>
                            <a href="approve_candidate.php?candidate_id=<?php echo $row['candidate_id']; ?>" class="button approve">Approve</a>
                            <a href="reject_candidate.php?candidate_id=<?php echo $row['candidate_id']; ?>" class="button reject">Reject</a>
                        <?php } else { ?>
                            ✅ Already Approved
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php else: ?>
            <tr>
                <td colspan="9">No pending candidates.</td>
            </tr>
        <?php endif; ?>
    </table>
    <a href="dashboard_admin.php" class="btn-back">⬅ Back to Dashboard</a>

    <!-- Modal -->
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImg">
    </div>

    <script>
        function openModal(src) {
            document.getElementById("imageModal").style.display = "block";
            document.getElementById("modalImg").src = src;
        }
        function closeModal() {
            document.getElementById("imageModal").style.display = "none";
        }
    </script>
</body>
</html>
