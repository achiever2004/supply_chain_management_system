<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "supply";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $productId = $_POST['product_id'];
    $newQuantity = $_POST['new_quantity'];

    $sql = "UPDATE inventory SET product_quantity = product_quantity + ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $newQuantity, $productId);

    if ($stmt->execute()) {
        echo "<p>Quantity updated successfully.</p>";
    } else {
        echo "<p>Error updating quantity: " . $conn->error . "</p>";
    }
    $stmt->close();
}

// Fetch products from the database
$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Products</title>
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
        .item-list {
            list-style-type: none;
            padding: 0;
        }
        .item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .item form {
            margin-top: 10px;
        }
        .item form input[type="number"] {
            width: 60px;
        }
        .item form button {
            margin-left: 10px;
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .item form button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Supply Products</h2>
        <ul class="item-list">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<li class="item">';
                    echo '<strong>Item Name:</strong> ' . htmlspecialchars($row["product_name"]) . '<br>';
                    echo '<strong>Quantity Available:</strong> ' . htmlspecialchars($row["product_quantity"]) . '<br>';
                    echo '<strong>Price:</strong> $' . htmlspecialchars($row["product_price"]) . '<br>';
                    echo '<form method="POST" action="supply.php">';
                    echo '<input type="hidden" name="product_id" value="' . $row["product_id"] . '">';
                    echo 'Supply Quantity: <input type="number" name="new_quantity" min="1" required>';
                    echo '<button type="submit" name="update_quantity">Update</button>';
                    echo '</form>';
                    echo '</li>';
                }
            } else {
                echo '<li class="item">No items found</li>';
            }
            ?>
        </ul>
    </div>
</body>
</html>
