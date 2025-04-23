<?php
include 'db_connect.php';

if (!isset($_GET['id'])) {
    echo "❌ Product ID missing.";
    exit();
}

$product_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "❌ Product not found.";
    exit();
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($product['product_name']) ?> - Product Details</title>
    <style>
        body { font-family: Arial; background-color: #f5f5f5; padding: 20px; }
        .card {
            background: white;
            padding: 20px;
            max-width: 600px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { color: #1e656d; }
        .button {
            display: inline-block;
            padding: 10px 15px;
            background: #1e656d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="card">
    <h2><?= htmlspecialchars($product['product_name']) ?></h2>
    <p><strong>Description:</strong> <?= htmlspecialchars($product['product_description']) ?></p>
    <p><strong>Category:</strong> <?= htmlspecialchars($product['category']) ?></p>
    <p><strong>Price Range:</strong> £<?= $product['min_price'] ?> - £<?= $product['max_price'] ?></p>
    <p><strong>Health Benefits:</strong> <?= htmlspecialchars($product['health_benefits'] ?? 'N/A') ?></p>
    <p><strong>Certification:</strong> <?= htmlspecialchars($product['certification'] ?? 'N/A') ?></p>

    <a href="payment.php?product_id=<?= $product['product_id'] ?>" class="button">Buy Now</a>
    <a href="index.php" class="button" style="background:#ccc; color:#333;">← Back</a>
</div>

</body>
</html>

