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

$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory List</title>
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
        .out-of-stock {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Inventory List</h2>
        <ul class="item-list">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<li class="item">';
                    echo '<strong>Item Name:</strong> ' . htmlspecialchars($row["product_name"]) . '<br>';
                    echo '<strong>Quantity:</strong> ' . htmlspecialchars($row["product_quantity"]) . '<br>';
                    echo '<strong>Price:</strong> $' . htmlspecialchars($row["product_price"]) . '<br>';
                    if ($row["product_quantity"] == 0) {
                        echo '<span class="out-of-stock">Out of Stock</span>';
                    }
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
