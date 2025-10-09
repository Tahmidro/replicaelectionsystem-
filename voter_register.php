<?php
session_start();
include "config.php";


$user_id = $_SESSION['user_id'];
$message = "";

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nidPhoto = $_FILES['nid_photo'];
    $selfPhoto = $_FILES['self_photo'];

    // Create upload folder if not exists
    $uploadDir = "uploads/voters/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // File paths
    $nidPath = $uploadDir . "nid_" . $user_id . "_" . basename($nidPhoto['name']);
    $selfPath = $uploadDir . "self_" . $user_id . "_" . basename($selfPhoto['name']);

    // Upload files
    if (move_uploaded_file($nidPhoto['tmp_name'], $nidPath) &&
        move_uploaded_file($selfPhoto['tmp_name'], $selfPath)) {

        // Check if voter already exists
       $stmtCheck = $conn->prepare("SELECT voter_id FROM voters WHERE user_id=?");
       $stmtCheck->bind_param("i", $user_id);
       $stmtCheck->execute();
       $result = $stmtCheck->get_result();

        if ($result->num_rows > 0) {
    // Update existing
        $stmt = $conn->prepare("UPDATE voters SET nid_photo_path=?, self_photo_path=? WHERE user_id=?");
        $stmt->bind_param("ssi", $nidPath, $selfPath, $user_id);
       }       
        else {
    // Insert new
         $stmt = $conn->prepare("INSERT INTO voters (user_id, nid_photo_path, self_photo_path, is_verified) VALUES (?, ?, ?, 0)");
          $stmt->bind_param("iss", $user_id, $nidPath, $selfPath);
       }

        
        if ($stmt->execute()) {
            $message = "Registration submitted successfully. Awaiting admin approval.";
        } else {
            $message = "Database error: " . $stmt->error;
        }
    } else {
        $message = "File upload failed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voter Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f4f4f9;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            width: 50%;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
        }
        input[type="file"], input[type="submit"] {
            display: block;
            width: 100%;
            margin: 15px 0;
            padding: 10px;
        }
        .message {
            text-align: center;
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
    </style>
</head>
<body>
    <h2>Register as Voter</h2>
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Upload NID Photo:</label>
        <input type="file" name="nid_photo" required>
        
        <label>Upload Your Photo:</label>
        <input type="file" name="self_photo" required>

        <input type="submit" value="Submit Registration">
    </form>

    <a href="dashboard_voter.php" class="btn-back">⬅ Back to Dashboard</a>
</body>
</html>
