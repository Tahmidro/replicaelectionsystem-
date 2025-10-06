

<?php
session_start();
include "config.php"; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];
    $nid      = trim($_POST["nid"]);

    if (empty($name) || empty($email) || empty($password) ||  empty($nid)) {
        $error = "All fields are required.";
    } else {
        // Check duplicate NID
        $errors=[];
        $sql_check = "SELECT user_id FROM users WHERE nid = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $nid);
        $stmt_check->execute();
        $stmt_check->store_result();
        //for duplicate email
        $sql_check2 = "SELECT user_id FROM users WHERE email = ?";
        $stmt_check2 = $conn->prepare($sql_check2);
        $stmt_check2->bind_param("s", $email);
        $stmt_check2->execute();
        $stmt_check2->store_result();



        if($stmt_check2->num_rows>0){
            $errors[]="this email is already registered";
        }
        if ($stmt_check->num_rows > 0) {
            $errors[] = "This NID is already registered."; }
        if(!empty($errors)){
            $error=implode("<br>", $errors);
        }    
         else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (name, email, password_hash, nid) 
                    VALUES (?, ?, ?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $nid);

           if ($stmt->execute()){
            $user_id = $conn->insert_id;
            $stmt2 = $conn->prepare("INSERT INTO voters (user_id) VALUES (?)");
            $stmt2->bind_param("i", $user_id);
            $stmt2->execute();
            $_SESSION['success'] = "User registered successfully! Please log in.";
            header("Location: login.php");
                exit;
            } else {
                $errors[] = "Registration failed: " . $stmt->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            width: 380px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        label {
            display: block;
            margin-top: 12px;
            color: #333;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            margin-top: 20px;
            width: 100%;
            background: #2c3e50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #1a252f;
        }
        .error {
            color: red;
            margin-bottom: 10px;
            text-align: center;
        }
        .success {
            color: green;
            margin-bottom: 10px;
            text-align: center;
        }
        .link {
            margin-top: 15px;
            text-align: center;
        }
        .link a {
            color: #2c3e50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($_SESSION['success'])) { 
            echo "<p class='success'>".$_SESSION['success']."</p>"; 
            unset($_SESSION['success']); 
        } ?>

        <form method="POST" onsubmit="return validateEmail() && validateNID()">
            <label>Name:</label>
            <input type="text" name="name" required>

            <label>Email:</label>
            <input type="email" name="email" id="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>NID:</label>
            <input type="text" name="nid" id="nid" maxlength="13" minlength="13" pattern="\d{13}" title="NID must be exactly 13 digits" required >

            <button type="submit">Register</button>
        </form>

        <div class="link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

    <script>
        function validateEmail() {
          const email = document.getElementById("email").value.trim();
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

          if (!emailRegex.test(email)) {
            alert("Please enter a valid email address.");
            return false;
          }
          if (!email.toLowerCase().endsWith(".com")) {
            alert("Email must end with '.com'");
            return false;
          }
          return true; 
        }

        function validateNID() {
            const nid = document.getElementById("nid").value.trim();
            if (!/^\d{13}$/.test(nid)) {
                alert("NID must be exactly 13 digits.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>


