<?php
function encrypt($data) {
    $result = '';
    $shift = 7;
    for ($i = 0; $i < strlen($data); $i++) {
        $char = $data[$i];
        if (ctype_alpha($char)) {
            $ascii = ord($char);
            $offset = ctype_upper($char) ? 65 : 97;
            $result .= chr(($ascii + $shift - $offset) % 26 + $offset);
        } else {
            $result .= $char;
        }
    }
    return $result;
}

function decrypt($data) {
    $result = '';
    $shift = 7;
    for ($i = 0; $i < strlen($data); $i++) {
        $char = $data[$i];
        if (ctype_alpha($char)) {
            $ascii = ord($char);
            $offset = ctype_upper($char) ? 65 : 97;
            $result .= chr(($ascii - $shift - $offset + 26) % 26 + $offset);
        } else {
            $result .= $char;
        }
    }
    return $result;
}

session_start();
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "supply";
$port = 3307;  // New port number you set

$signup_successful = false;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$username = encrypt($_POST['username']);
$email = encrypt($_POST['email']);
$age = encrypt($_POST['age']);
$phone = encrypt($_POST['phone']);
$position = $_POST['position'];
$password = encrypt($_POST['password']);

// SQL query to insert data into database
$sql = "INSERT INTO users (username, email, age, phone_no, position, password) VALUES ('$username', '$email', '$age', '$phone', '$position', '$password')";

if ($conn->query($sql) === TRUE) {
    http_response_code(200); // Success
    $signup_successful = true;
} else {
    http_response_code(500); // Server error
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .container h2 {
            margin-bottom: 20px;
        }
        .container input[type="text"], .container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .container input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .welcome-section {
            display: none;
            text-align: center;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <?php if ($signup_successful): ?>
        <div class="welcome-section" id="welcome-section">
            <h2>Welcome to our world!!!</h2>
            <p>You have successfully signed up </p>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('welcome-section').style.display = 'block';
            });
        </script>
    <?php else: ?>
        <div class="container">
            <h2>Log in </h2>
            <form action="signin.php" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <input type="submit" value="Sign In">
                <?php if ($error_message): ?>
                    <p class="error"><?php echo $error_message; ?></p>
                <?php endif; ?>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>

