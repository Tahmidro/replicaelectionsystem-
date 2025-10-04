<?php
session_start();
if (isset($_SESSION['flash_msg'])) {
    echo "<p style='color:white;'>".htmlspecialchars($_SESSION['flash_msg'])."</p>";
    unset($_SESSION['flash_msg']); 
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Election Management System</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
        }
        h1 {
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        p {
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            background: #fff;
            color: #3498db;
            padding: 12px 25px;
            margin: 10px;
            border-radius: 30px;
            font-weight: bold;
            transition: 0.3s ease;
        }
        .btn:hover {
            background: #3498db;
            color: #fff;
            transform: scale(1.05);
        }
        footer {
            position: absolute;
            bottom: 15px;
            font-size: 0.9rem;
            color: #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Election Management System</h1>
        <p>Secure • Transparent • Reliable</p>
        <a href="login.php" class="btn">Login</a>
        <a href="register.php" class="btn">Register</a>
    </div>
    <footer>
        &copy; <?php echo date("Y"); ?> Election System. All rights reserved.
    </footer>
</body>
</html>
