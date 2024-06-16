<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "supply";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .user-list {
            list-style-type: none;
            padding: 0;
        }
        .user {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Details</h2>
        <ul class="user-list">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<li class="user">';
                    echo '<strong>Username:</strong> ' . htmlspecialchars($row["username"]) . '<br>';
                    echo '<strong>Username:</strong> ' . htmlspecialchars($row["email"]) . '<br>';
                    echo '<strong>Username:</strong> ' . htmlspecialchars($row["age"]) . '<br>';
                    echo '<strong>Position:</strong> ' . htmlspecialchars($row["phone_no"]) . '<br>';
                    echo '</li>';
                }
            } else {
                echo '<li class="user">No users found</li>';
            }
            ?>
        </ul>
    </div>
</body>
</html>
