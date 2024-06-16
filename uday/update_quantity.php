<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "supply";
    $port = 3307;

    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $productId = $_POST['product_id'];
    $quantityChange = $_POST['quantity_change'];

    $sql = "UPDATE inventory SET product_quantity = product_quantity + ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantityChange, $productId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Quantity updated successfully";
    } else {
        echo "Failed to update quantity";
    }

    $stmt->close();
    $conn->close();
}
?>
