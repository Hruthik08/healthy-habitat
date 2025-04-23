<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $business_name = $_POST['business_name'];
    $business_description = $_POST['business_description'];
    $quantity = $_POST['quantity'];
    $health_benefits = $_POST['health_benefits'];
    $pricing = $_POST['pricing'];
    $certification = $_POST['certification'];
    $contact_info = $_POST['contact_info'];

    $sql = "INSERT INTO businesses (business_name, business_description, quantity, health_benefits, pricing, certification, contact_info) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisdss", $business_name, $business_description, $quantity, $health_benefits, $pricing, $certification, $contact_info);

    if ($stmt->execute()) {
        echo "<div class='success'>🏢 Business added successfully!</div>";
    } else {
        echo "<div class='error'>❌ Error: " . $stmt->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Business - Healthy Habitat</title>
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

<h2>🏢 Register Your Business with Healthy Habitat</h2>

<form method="POST">
    <label>Business Name:</label>
    <input type="text" name="business_name" required>

    <label>Description:</label>
    <input type="text" name="business_description" required>

    <label>Quantity:</label>
    <input type="number" name="quantity" required>

    <label>Health Benefits:</label>
    <input type="text" name="health_benefits" required>

    <label>Pricing:</label>
    <input type="number" name="pricing" step="0.01" required>

    <label>Certification:</label>
    <input type="text" name="certification" required>

    <label>Contact Info:</label>
    <input type="text" name="contact_info" required>

    <input type="submit" value="Add Business">
</form>

</body>
</html>
