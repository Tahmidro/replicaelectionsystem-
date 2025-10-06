<?php
session_start();
include "config.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if user is an approved voter and get their details
$stmt = $conn->prepare("SELECT v.nid_photo_path, v.self_photo_path, u.name, u.email, u.nid 
                        FROM voters v 
                        JOIN users u ON v.user_id = u.user_id 
                        WHERE v.user_id = ? AND v.is_verified = 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Not an approved voter
    header("Location: dashboard_voter.php");
    exit;
}

$data = $result->fetch_assoc();
$already_applied = false;
$message = "";

// Check if already applied as candidate
$check = $conn->prepare("SELECT * FROM candidates WHERE user_id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$check_result = $check->get_result();

if ($check_result->num_rows > 0) {
    $row = $check_result->fetch_assoc();
    $status = $row['status'];

    if ($status === 'pending' || $status === 'approved') {
        $already_applied = true;
        $message = "You have already applied as a candidate. Status: <strong>" . htmlspecialchars($status) . "</strong>";
    } elseif ($status === 'rejected') {
        // Allow re-application, show message but don't block form
        $message = "Your previous application was rejected. You can re-apply below.";
    }
}

// Handle form submission (Insert or Update)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $party = trim($_POST["party"]);
    $manifesto = trim($_POST["manifesto"]);

    if ($check_result->num_rows > 0) {
        // Update existing record and reset status to pending
        $update = $conn->prepare("UPDATE candidates SET party = ?, manifesto = ?, status = 'pending' WHERE user_id = ?");
        $update->bind_param("ssi", $party, $manifesto, $user_id);

        if ($update->execute()) {
            $message = "Candidate application updated and submitted successfully. Awaiting admin approval.";
            $already_applied = true;
        } else {
            $message = "Something went wrong. Please try again.";
        }
    } else {
        // Insert new candidate application
        $insert = $conn->prepare("INSERT INTO candidates (user_id, party, manifesto, status) VALUES (?, ?, ?, 'pending')");
        $insert->bind_param("iss", $user_id, $party, $manifesto);

        if ($insert->execute()) {
            $message = "Candidate application submitted successfully. Awaiting admin approval.";
            $already_applied = true;
        } else {
            $message = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply as Candidate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            background-color: #fff;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        label {
            font-weight: bold;
            margin-top: 15px;
            display: block;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
        }
        .btn {
            margin-top: 20px;
            padding: 12px;
            width: 100%;
            background-color: #007BFF;
            color: white;
            border: none;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .photos {
            margin-top: 20px;
            text-align: center;
        }
        .photos img {
            max-width: 150px;
            max-height: 120px;
            margin: 10px;
            border-radius: 6px;
            box-shadow: 0 0 6px rgba(0,0,0,0.2);
        }
        .message {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            color: green;
        }
        .message.error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Apply as Candidate</h2>

        <?php if (!empty($message)): ?>
            <p class="message <?= (strpos($message, 'wrong') !== false) ? 'error' : '' ?>"><?= $message ?></p>
        <?php endif; ?>

        <?php if (!$already_applied || (isset($status) && $status === 'rejected')): ?>
        <form method="POST">
            <label>Name:</label>
            <input type="text" value="<?= htmlspecialchars($data['name']) ?>" disabled>

            <label>Email:</label>
            <input type="text" value="<?= htmlspecialchars($data['email']) ?>" disabled>

            <label>NID:</label>
            <input type="text" value="<?= htmlspecialchars($data['nid']) ?>" disabled>

            <label>Party Name:</label>
            <input type="text" name="party" required
                value="<?= isset($row['party']) ? htmlspecialchars($row['party']) : '' ?>">

            <label>Manifesto:</label>
            <textarea name="manifesto" rows="5" required><?= isset($row['manifesto']) ? htmlspecialchars($row['manifesto']) : '' ?></textarea>

            <div class="photos">
                <label>NID Photo:</label><br>
                <img src="<?= htmlspecialchars($data['nid_photo_path']) ?>" alt="NID Photo"><br>

                <label>Self Photo:</label><br>
                <img src="<?= htmlspecialchars($data['self_photo_path']) ?>" alt="Self Photo">
            </div>

            <button type="submit" class="btn">Submit Application</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
