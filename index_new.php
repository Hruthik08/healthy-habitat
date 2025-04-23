<?php
include 'db_connect.php';
session_start();
$resident_id = $_SESSION['resident_id'] ?? 1; // Replace with actual session ID in production

// Get all products and services
$allProducts = $conn->query("SELECT * FROM products");
$allServices = $conn->query("SELECT * FROM services");

// Top Voted Products
$topProducts = $conn->query("SELECT p.*, COUNT(v.vote_id) AS vote_count 
                             FROM products p 
                             LEFT JOIN votes v ON p.product_id = v.product_id AND v.vote = 'yes'
                             GROUP BY p.product_id 
                             ORDER BY vote_count DESC 
                             LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
    <title>🌿 Healthy Habitat Network</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        h1 { color: #2c3e50; }
        .card { background: white; padding: 15px; margin: 10px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); width: 45%; display: inline-block; vertical-align: top; }
        .bottom-nav { margin-top: 40px; text-align: center; }
        .bottom-nav a { margin: 0 15px; text-decoration: none; color: #27ae60; font-weight: bold; }
        .icon-button { background: #27ae60; color: white; padding: 10px 20px; border-radius: 6px; margin: 10px; text-decoration: none; cursor: pointer; display: inline-block; }
        .icon-button:hover { background-color: #2c3e50; color: white; }
        .vote-btn { background-color: #2980b9; color: white; padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; margin-top: 8px; }
        .vote-btn:hover { background-color: #21618c; }
    </style>
</head>
<body>
<a name="top"></a>

<h1>🌿 Healthy Habitat Network</h1>

<div class="results">
    <h2>🛍️ Products & Services:</h2>
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between;">
    <?php
    while ($row = $allProducts->fetch_assoc()) {
        echo "<div class='card'>
                <h3><a href='product_detail.php?id={$row['product_id']}'>{$row['product_name']}</a></h3>
                <p><strong>Category:</strong> {$row['category']}</p>
                <p><strong>Description:</strong> {$row['product_description']}</p>
                <p><strong>Price:</strong> ₹{$row['min_price']} - ₹{$row['max_price']}</p>
            </div>";
    }

    while ($row = $allServices->fetch_assoc()) {
        $business_id = $row['business_id'];
        $stmt = $conn->prepare("SELECT business_name FROM businesses WHERE business_id = ?");
        $stmt->bind_param("i", $business_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $business_name = $result->num_rows > 0 ? $result->fetch_assoc()['business_name'] : "Unknown Provider";

        echo "<div class='card'>
                <h3><a href='service_detail.php?id={$row['service_id']}'>{$row['service_name']}</a></h3>
                <p><strong>Category:</strong> {$row['category']}</p>
                <p><strong>Description:</strong> {$row['service_description']}</p>
                <p><strong>Price:</strong> ₹{$row['price']}</p>
                <p><strong>Provider:</strong> {$business_name}</p>
            </div>";
    }
    ?>
    </div>

    <!-- Top Voted Products -->
    <h2>🌟 Top Voted Products:</h2>
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between;">
    <?php while ($row = $topProducts->fetch_assoc()): ?>
        <div class="card" id="product-<?= $row['product_id'] ?>">
            <h3><a href='product_detail.php?id=<?= $row['product_id'] ?>'><?= htmlspecialchars($row['product_name']) ?></a></h3>
            <p><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($row['product_description']) ?></p>
            <p><strong>Price:</strong> ₹<?= $row['min_price'] ?> - ₹<?= $row['max_price'] ?></p>
            <p><strong>Votes:</strong> <span id="votes-<?= $row['product_id'] ?>"><?= $row['vote_count'] ?></span></p>
            <button class="vote-btn" onclick="voteForProduct(<?= $row['product_id'] ?>)">👍 Vote Yes</button>
        </div>
    <?php endwhile; ?>
    </div>
</div>

<div class="bottom-nav">
    <a href="filter_products.php" class="icon-button">🔍 Search</a>
    <a href="register.php" class="icon-button">👤 Register</a>
    <a href="login_new.php" class="icon-button">🔐 Login</a>
    <a href="contact.php" class="icon-button">📞 Contact</a>
</div>

<a href="#top" class="icon-button" style="position:fixed; bottom:20px; right:20px; background:#2c3e50;">↑</a>

<script>
function voteForProduct(productId) {
    const residentId = <?= $resident_id ?>;

    fetch("vote_product.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `resident_id=${residentId}&product_id=${productId}&vote=yes`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            document.getElementById("votes-" + productId).innerText = data.new_count;
            alert("✅ Vote submitted!");
        } else if (data.status === "already_voted") {
            alert("⚠️ You already voted for this product.");
        } else {
            alert("❌ Error: " + (data.message || "Try again later."));
        }
    });
}
</script>
</body>
</html>
