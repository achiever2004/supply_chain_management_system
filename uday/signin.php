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
$username = "root";
$password = "";
$dbname = "supply";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

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
        if ($inputPassword === $row['password']) {
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
            width: 300px;
        }
        .container h2 {
            margin-bottom: 20px;
            text-align: center;
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
        .section {
            margin: 20px 0;
        }
        .section button {
            padding: 10px 20px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        .section button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
    <script>
        function navigateToResultPage() {
            window.location.href = 'construct.html';
        }
        function navigateToPeoplePage(){
            window.location.href = 'users.php'
        }
        function navigateToProductPage(){
            window.location.href = 'result.php'
        }
        function navigateToManagePage(){
            window.location.href = 'manage.php'
        }
        function navigateToCustomerPage(){
            window.location.href = 'customer.php'
        }
        function navigateToSupplyPage(){
            window.location.href = 'supply.php'
        }
    </script>
</head>
<body>
    <?php if ($login_successful): ?>
        <?php if ($_SESSION['position'] === 'customer'): ?>
            <div class="customer-page" id="customer-page">
                <h2>Welcome, <?php echo htmlspecialchars($decrypted_name); ?>!</h2>
                <p>You have successfully logged in as a customer.</p>
                <div class="section" id="order-history">
                    <h2>Place Order</h2>
                    <p>Place your order here </p>
                    <button onclick="navigateToCustomerPage()">Place Order</button>
                </div>
                <div class="section" id="tracking">
                    <h2>Tracking</h2>
                    <p>Track your current orders.</p>
                    <button onclick="navigateToResultPage()">Track Orders</button>
                </div>
                <div class="section" id="account-details">
                    <h2>Account Details</h2>
                    <p>Manage your account information.</p>
                    <button onclick="navigateToManagePage()">Manage Account</button>
                </div>
                <div class="section" id="support">
                    <h2>Support</h2>
                    <p>Contact support for help.</p>
                    <button onclick="navigateToResultPage()">Contact Support</button>
                </div>
            </div>
        <?php elseif ($_SESSION['position'] === 'supplier'): ?>
            <div class="supplier-page" id="supplier-page">
                <h2>Welcome, <?php echo htmlspecialchars($decrypted_name); ?>!</h2>
                <p>You have successfully logged in as a supplier.</p>
                <div class="section" id="inventory">
                    <h2>Inventory Management</h2>
                    <p>Manage your inventory and stock levels.</p>
                    <button onclick="navigateToProductPage()">Manage Inventory</button>
                </div>
                <div class="section" id="orders">
                    <h2>Orders</h2>
                    <p>View and manage incoming orders.</p>
                    <button onclick="navigateToSupplyPage()">View Orders</button>
                </div>
                <div class="section" id="account-details">
                    <h2>Account Details</h2>
                    <p>Manage your account information.</p>
                    <button onclick="navigateToManagePage()">Manage Account</button>
                </div>
                <div class="section" id="support">
                    <h2>Support</h2>
                    <p>Contact support for help.</p>
                    <button onclick="navigateToResultPage()">Contact Support</button>
                </div>
            </div>
        <?php elseif ($_SESSION['position'] === 'manager'): ?>
            <div class="manager-page" id="manager-page">
                <h2>Welcome, <?php echo htmlspecialchars($decrypted_name); ?>!</h2>
                <p>You have successfully logged in as a manager.</p>
                <div class="section" id="overview">
                    <h2>Business Overview</h2>
                    <p>View overall business performance and metrics.</p>
                    <button onclick="navigateToProductPage()">View Overview</button>
                </div>
                <div class="section" id="manage-employees">
                    <h2>Manage Employees</h2>
                    <p>View and manage employee details and roles.</p>
                    <button onclick="navigateToPeoplePage()">Manage Employees</button>
                </div>
                <div class="section" id="account-details">
                    <h2>Account Details</h2>
                    <p>Manage your account information.</p>
                    <button onclick="navigateToManagePage()">Manage Account</button>
                </div>
                <div class="section" id="support">
                    <h2>Support</h2>
                    <p>Contact support for help.</p>
                    <button onclick="navigateToResultPage()">Contact Support</button>
                </div>
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
