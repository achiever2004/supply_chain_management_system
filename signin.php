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

$servername = "localhost";
$username = "root";  // Default username for XAMPP
$password = "";  // Default password for XAMPP is an empty string
$dbname = "supply";
$port = 3307;  // New port number you set

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$login_successful = false;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = encrypt($_POST['username']);
    $inputPassword = encrypt($_POST['password']);

    $sql = "SELECT username, password, position FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($inputPassword === $row['password']) {  // Plaintext comparison
            $_SESSION['username'] = $inputUsername;
            $_SESSION['position'] = $row['position'];
            $login_successful = true;
            $decrypted_name = decrypt($row["username"]);
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No user found with that username.";
    }
    $stmt->close();
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
        .welcome-section, .customer-page, .supplier-page, .manager-page {
            display: none;
            text-align: center;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <?php if ($login_successful): ?>
        <?php if ($_SESSION['position'] === 'customer'): ?>
            <div class="customer-page" id="customer-page">
                <h2>Welcome, <?php echo htmlspecialchars($decrypted_name); ?>!</h2>
                <p>You have successfully logged in as a customer.</p>
                <!-- Customer page content here -->
                <section id="order-history">
                    <h2>Order History</h2>
                    <button>View your past orders and their statuses.</button>
                    <!-- Order history details here -->
                </section>

                <section id="tracking">
                    <h2>Tracking</h2>
                    <button>Track your current orders.</button>
                    <!-- Tracking details here -->
                </section>

                <section id="account-details">
                    <h2>Account Details</h2>
                    <button>Manage your account information.</button>
                    <!-- Account details form here -->
                </section>

                <section id="support">
                    <h2>Support</h2>
                    <button>Contact</button>
                    <!-- Support contact details here -->
                </section>
            </div>
        <?php elseif ($_SESSION['position'] === 'supplier'): ?>
            <div class="supplier-page" id="supplier-page">
                <h2>Welcome, <?php echo htmlspecialchars($decrypted_name); ?>!</h2>
                <p>You have successfully logged in as a supplier.</p>
                <!-- Supplier page content here -->
                <section id="inventory">
                    <h2>Inventory Management</h2>
                    <button>Manage your inventory and stock levels.</button>
                    <!-- Inventory management details here -->
                </section>

                <section id="orders">
                    <h2>Orders</h2>
                    <button>View and manage incoming orders.</button>
                    <!-- Order management details here -->
                </section>

                <section id="account-details">
                    <h2>Account Details</h2>
                    <button>Manage your account information.</button>
                    <!-- Account details form here -->
                </section>

                <section id="support">
                    <h2>Support</h2>
                    <button>Contact</button>
                    <!-- Support contact details here -->
                </section>
            </div>
        <?php elseif ($_SESSION['position'] === 'manager'): ?>
            <div class="manager-page" id="manager-page">
                <h2>Welcome, <?php echo htmlspecialchars($decrypted_name); ?>!</h2>
                <p>You have successfully logged in as a manager.</p>
                <!-- Manager page content here -->
                <section id="overview">
                    <h2>Business Overview</h2>
                    <button>View overall business performance and metrics.</button>
                    <!-- Business overview details here -->
                </section>

                <section id="manage-employees">
                    <h2>Manage Employees</h2>
                    <button>View and manage employee details and roles.</button>
                    <!-- Employee management details here -->
                </section>

                <section id="account-details">
                    <h2>Account Details</h2>
                    <button>Manage your account information.</button>
                    <!-- Account details form here -->
                </section>

                <section id="support">
                    <h2>Support</h2>
                    <button>Contact</button>
                    <!-- Support contact details here -->
                </section>
            </div>
        <?php else: ?>
            <div class="welcome-section" id="welcome-section">
                <h2>Welcome, <?php echo htmlspecialchars($decrypted_name); ?>!</h2>
                <p>You have successfully logged in.</p>
            </div>
        <?php endif; ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                <?php if ($_SESSION['position'] === 'customer'): ?>
                    document.getElementById('customer-page').style.display = 'block';
                <?php elseif ($_SESSION['position'] === 'supplier'): ?>
                    document.getElementById('supplier-page').style.display = 'block';
                <?php elseif ($_SESSION['position'] === 'manager'): ?>
                    document.getElementById('manager-page').style.display = 'block';
                <?php else: ?>
                    document.getElementById('welcome-section').style.display = 'block';
                <?php endif; ?>
            });
        </script>
    <?php else: ?>
        <div class="container">
            <h2>Log in</h2>
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
