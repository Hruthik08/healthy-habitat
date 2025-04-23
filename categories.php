<!-- ✅ categories.php -->
<?php
include 'db_connect.php';
$category = $_GET['category'] ?? '';

$productQuery = $conn->prepare("SELECT * FROM products WHERE category = ?");
$productQuery->bind_param("s", $category);
$productQuery->execute();
$productResult = $productQuery->get_result();

$serviceQuery = $conn->prepare("SELECT * FROM services WHERE category = ?");
$serviceQuery->bind_param("s", $category);
$serviceQuery->execute();
$serviceResult = $serviceQuery->get_result();
?>

<h2>Results for Category: <?= htmlspecialchars($category) ?></h2>
<h3>Products</h3>
<ul>
<?php while($p = $productResult->fetch_assoc()): ?>
    <li><a href="product_detail.php?id=<?= $p['product_id'] ?>">
        <?= htmlspecialchars($p['product_name']) ?> - ₹<?= $p['min_price'] ?> to ₹<?= $p['max_price'] ?>
    </a></li>
<?php endwhile; ?>
</ul>

<h3>Services</h3>
<ul>
<?php while($s = $serviceResult->fetch_assoc()): ?>
    <li><a href="service_detail.php?id=<?= $s['service_id'] ?>">
        <?= htmlspecialchars($s['service_name']) ?> - ₹<?= $s['price'] ?>
    </a></li>
<?php endwhile; ?>
</ul>
