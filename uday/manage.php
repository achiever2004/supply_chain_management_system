<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

$servername = "localhost";
$username = "root";  // Default username for XAMPP
$password = "";  // Default password for XAMPP is an empty string
$dbname = "supply";
$port = 3307;  // New port number you set

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$decrypted_name = htmlspecialchars($_SESSION['username']);
$position = htmlspecialchars($_SESSION['position']);
$user_details = ['username' => '', 'password' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updatedName = $_POST['name'];
    $updatedPassword = $_POST['password'];
    
    $encryptedName = encrypt($updatedName);
    $encryptedPassword = encrypt($updatedPassword);

    $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE username = ?");
    $stmt->bind_param("sss", $encryptedName, $encryptedPassword, $_SESSION['username']);
    if ($stmt->execute()) {
        $_SESSION['username'] = $encryptedName;
        $decrypted_name = $updatedName;
        $success_message = "Account details updated successfully.";
    } else {
        $error_message = "Failed to update account details.";
    }
    $stmt->close();
} else {
    $stmt = $conn->prepare("SELECT username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user_details = $result->fetch_assoc();
    } else {
        $error_message = "User details not found.";
    }
    $stmt->close();
}

$conn->close();

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Account</title>
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
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Account</h2>
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="manage.php" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars(decrypt($user_details['username'])); ?>" required>
    
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?php echo htmlspecialchars(decrypt($user_details['password'])); ?>" required>
    
            <input type="submit" value="Update Account">
        </form>
    </div>
</body>
</html>
