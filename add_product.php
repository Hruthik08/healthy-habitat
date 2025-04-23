<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $business_id = $_POST['business_id'];
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $category = $_POST['category'];
    $price_range = $_POST['price_range'];

    $sql = "INSERT INTO products (business_id, product_name, product_description, category, price_range) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $business_id, $product_name, $product_description, $category, $price_range);

    if ($stmt->execute()) {
        echo "<div class='success'>📦 Product added successfully!</div>";
    } else {
        echo "<div class='error'>❌ Error: " . $stmt->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Product - Healthy Habitat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            text-align: center;
            padding: 40px;
        }
        h2 {
            color: #2c3e50;
        }
        form {
            display: inline-block;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: left;
            max-width: 500px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #219150;
        }
        .success {
            color: #27ae60;
            font-weight: bold;
            margin-top: 20px;
        }
        .error {
            color: #c0392b;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>📦 Register a Product</h2>

<form method="POST">
    <label>Business ID:</label>
    <input type="number" name="business_id" required>

    <label>Product Name:</label>
    <input type="text" name="product_name" required>

    <label>Description:</label>
    <input type="text" name="product_description" required>

    <label>Category:</label>
    <input type="text" name="category" required>

    <label>Price Range:</label>
    <input type="text" name="price_range" required>

    <input type="submit" value="Add Product">
</form>

</body>
</html>
