<?php
session_start();
include "config.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #eef2f7;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .dashboard {
      background: white;
      padding: 30px;
      border-radius: 12px;
      width: 350px;
      text-align: center;
      box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }
    h2 { color: #2c3e50; }
    ul {
      list-style: none;
      padding: 0;
      margin: 20px 0;
    }
    li {
      margin: 12px 0;
    }
    a {
      display: block;
      padding: 10px;
      background: #3498db;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      transition: 0.3s;
    }
    a:hover {
      background: #2980b9;
    }
  </style>
</head>
<body>
  <div class="dashboard">
    <h2>Admin Dashboard</h2>
    <ul>
      <li><a href="approve_candidates.php">Approve Candidates</a></li>
      <li><a href="approve_voters.php">Approve voters</a></li>
      <li><a href="create_election.php">Create Election</a></li>
      <li><a href="assign_candidates.php">Assign Candidates</a></li>
      <li><a href="result.php">View Live Results</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>
</body>
</html>

