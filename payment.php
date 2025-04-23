<?php
$product_id = $_GET['product_id'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Secure Checkout</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f8f4;
            color: #2c3e50;
            padding: 40px;
        }
        h2 {
            color: #1a472a;
            font-size: 40px;
            text-align: center;
            margin-bottom: 30px;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        input[type="text"],
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 18px;
        }
        input[type="submit"] {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 18px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #14532d;
        }
        p {
            text-align: center;
            font-size: 20px;
        }
    </style>
</head>
<body>

<h2>Secure Checkout</h2>
<p>You're buying product ID: <?= htmlspecialchars($product_id) ?></p>
<form method="POST" action="payment_success.php">
    <label for="name">Name:</label>
    <input type="text" name="name" required>

    <label for="address">Address:</label>
    <input type="text" name="address" required>

    <label for="method">Payment Method:</label>
    <select name="method">
        <option value="card">Credit/Debit Card</option>
        <option value="upi">UPI</option>
        <option value="cod">Cash on Delivery</option>
    </select>

    <input type="submit" value="Pay Now">
</form>

</body>
</html>

