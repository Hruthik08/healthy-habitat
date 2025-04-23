<?php
require_once "db_connect.php";

// Get the product/service ID from the URL
$product_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Get the product/service details from the database
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

// If the product exists, display buy options
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    echo "<h2>Buy " . htmlspecialchars($product['product_name']) . "</h2>";
    echo "<p><strong>Price:</strong> ₹" . htmlspecialchars($product['max_price']) . "</p>";
    echo "<p><strong>Category:</strong> " . htmlspecialchars($product['category']) . "</p>";
    echo "<p><strong>Description:</strong> " . htmlspecialchars($product['product_description']) . "</p>";
    echo "<p><a href='checkout.php?id=" . $product['product_id'] . "'>Proceed to Checkout</a></p>";
} else {
    echo "<p>Product/Service not found.</p>";
}
?>
