<?php
session_start();
include 'db_connect.php';

// Handle filters
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

// Build queries with filters
$productQuery = "SELECT * FROM products WHERE 1=1";
$serviceQuery = "SELECT * FROM services WHERE 1=1";

if (!empty($search)) {
    $productQuery .= " AND (product_name LIKE '%$search%' OR product_description LIKE '%$search%')";
    $serviceQuery .= " AND (service_name LIKE '%$search%' OR service_description LIKE '%$search%')";
}

if (!empty($category)) {
    $productQuery .= " AND category = '$category'";
    $serviceQuery .= " AND category = '$category'";
}

$allProducts = $conn->query($productQuery);
$allServices = $conn->query($serviceQuery);
$topProducts = $conn->query("SELECT * FROM products ORDER BY vote_count DESC LIMIT 5");
$categoryQuery = $conn->query("SELECT DISTINCT category FROM products ORDER BY category ASC");

function hasVoted($product_id, $resident_id, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM votes WHERE product_id = ? AND resident_id = ?");
    $stmt->bind_param("ii", $product_id, $resident_id);
    $stmt->execute();
    $stmt->bind_result($vote_count);
    $stmt->fetch();
    $stmt->close();
    return $vote_count > 0;
}

function hasVotedService($service_id, $resident_id, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM service_votes WHERE service_id = ? AND resident_id = ?");
    $stmt->bind_param("ii", $service_id, $resident_id);
    $stmt->execute();
    $stmt->bind_result($vote_count);
    $stmt->fetch();
    $stmt->close();
    return $vote_count > 0;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>🌿 Healthy Habitat Network</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 40px;
            background-color: #f4f8f4;
            color: #2c3e50;
        }
        h1 {
            color: #1a472a;
            text-align: center;
            font-size: 80px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 15px;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            width: 45%;
            display: inline-block;
            vertical-align: top;
        }
        .results h3 {
            margin-bottom: 5px;
        }
        .search-bar {
            margin: 20px auto 40px;
            text-align: center;
        }
        .search-bar input[type="text"],
        .search-bar select {
            padding: 8px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 20px;
        }
        .search-bar button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            background-color: #2980b9;
            color: white;
            cursor: pointer;
            font-size: 20px;
        }
        .icon-button {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            margin: 10px;
            text-decoration: none;
            cursor: pointer;
        }
        .icon-button:hover {
            background-color: #14532d;
        }
        .card-clickable {
            cursor: pointer;
        }
        .disabled {
            background-color: gray;
            cursor: not-allowed;
        }
        .out-of-stock {
            background-color: #eee !important;
            color: #999 !important;
            opacity: 0.6;
        }
        .stock-label {
            font-weight: bold;
            color: red;
            margin-bottom: 8px;
        }
    </style>
    <script>
        function confirmVote(productId, residentId) {
            if (confirm("Are you sure you want to vote for this product?")) {
                document.getElementById('voteForm_' + productId).submit();
            }
        }

        function confirmVoteService(serviceId, residentId) {
            if (confirm("Are you sure you want to vote for this service?")) {
                document.getElementById('voteServiceForm_' + serviceId).submit();
            }
        }
    </script>
</head>
<body>

<h1>🌿 Healthy Habitat Network</h1>

<div class="search-bar">
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search products or services..." value="<?= htmlspecialchars($search) ?>">
        <select name="category">
            <option value="">All Categories</option>
            <?php while ($cat = $categoryQuery->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($cat['category']) ?>" <?= ($category == $cat['category']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['category']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">🔍 Search</button>
    </form>
</div>

<div class="results">
    <h2>🛍️ Products & Services:</h2>
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between;">

    <?php while ($row = $allProducts->fetch_assoc()) {
        $product_id = $row['product_id'];
        $is_voted = isset($_SESSION['resident_id']) ? hasVoted($product_id, $_SESSION['resident_id'], $conn) : false;
        $is_out_of_stock = $row['status'] === 'out_of_stock';
        $card_class = $is_out_of_stock ? "card out-of-stock" : "card";
    ?>
        <div class="<?= $card_class ?>">
            <div class='card-clickable' onclick="window.location='product_detail.php?id=<?= $row['product_id'] ?>'">
                <?php if ($is_out_of_stock) echo '<div class="stock-label">❌ Out of Stock</div>'; ?>
                <h3><?= $row['product_name'] ?></h3>
                <p><strong>Category:</strong> <?= $row['category'] ?></p>
                <p><strong>Description:</strong> <?= $row['product_description'] ?></p>
                <p><strong>Price:</strong> £<?= $row['min_price'] ?> - £<?= $row['max_price'] ?></p>
                <p><strong>Total Votes:</strong> <?= $row['vote_count'] ?></p>
            </div>

            <?php if (isset($_SESSION['resident_id'])) {
                if ($is_out_of_stock) { ?>
                    <button class="icon-button disabled" disabled>❌ Unavailable</button>
                <?php } elseif ($is_voted) { ?>
                    <button class="icon-button disabled" disabled>👍 Voted</button>
                <?php } else { ?>
                    <form id="voteForm_<?= $row['product_id'] ?>" method="POST" action="vote_product.php">
                        <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                        <input type="hidden" name="vote" value="yes">
                        <input type="hidden" name="resident_id" value="<?= $_SESSION['resident_id'] ?>">
                        <button type="button" class="icon-button" onclick="confirmVote(<?= $row['product_id'] ?>, <?= $_SESSION['resident_id'] ?>)">👍 Vote Now</button>
                    </form>
                <?php }
            } else { ?>
                <a href="login_new.php?redirect=dashboard.php" class="icon-button">🔐 Login to Vote</a>
            <?php } ?>
        </div>
    <?php } ?>

    <?php while ($row = $allServices->fetch_assoc()) {
        $service_id = $row['service_id'];
        $is_voted_service = isset($_SESSION['resident_id']) ? hasVotedService($service_id, $_SESSION['resident_id'], $conn) : false;
        $business_id = $row['business_id'];
        $business_stmt = $conn->prepare("SELECT business_name FROM businesses WHERE business_id = ?");
        $business_stmt->bind_param("i", $business_id);
        $business_stmt->execute();
        $business_result = $business_stmt->get_result();
        $business_name = $business_result->num_rows > 0 ? $business_result->fetch_assoc()['business_name'] : "Unknown Provider";
        $business_stmt->close();
    ?>
        <div class='card'>
            <h3><a href='service_detail.php?id=<?= $row['service_id'] ?>'><?= $row['service_name'] ?></a></h3>
            <p><strong>Category:</strong> <?= $row['category'] ?></p>
            <p><strong>Description:</strong> <?= $row['service_description'] ?></p>
            <p><strong>Price:</strong> £<?= $row['price'] ?></p>
            <p><strong>Provider:</strong> <?= $business_name ?></p>
            <p><strong>Total Votes:</strong> <?= $row['vote_count'] ?></p>

            <?php if (isset($_SESSION['resident_id'])) {
                if ($is_voted_service) { ?>
                    <button class="icon-button disabled" disabled>👍 Voted</button>
                <?php } else { ?>
                    <form id="voteServiceForm_<?= $row['service_id'] ?>" method="POST" action="vote_service.php">
                        <input type="hidden" name="service_id" value="<?= $row['service_id'] ?>">
                        <input type="hidden" name="vote" value="yes">
                        <input type="hidden" name="resident_id" value="<?= $_SESSION['resident_id'] ?>">
                        <button type="button" class="icon-button" onclick="confirmVoteService(<?= $row['service_id'] ?>, <?= $_SESSION['resident_id'] ?>)">👍 Vote Now</button>
                    </form>
                <?php }
            } else { ?>
                <a href="login_new.php?redirect=dashboard.php" class="icon-button">🔐 Login to Vote</a>
            <?php } ?>
        </div>
    <?php } ?>
    </div>
</div>

<h2>🏆 Top 5 Voted Products:</h2>
<div style="display: flex; flex-wrap: wrap; justify-content: space-between;">
<?php while ($row = $topProducts->fetch_assoc()) { ?>
    <div class='card'>
        <div class='card-clickable' onclick="window.location='product_detail.php?id=<?= $row['product_id'] ?>'">
            <h3><?= $row['product_name'] ?></h3>
            <p><strong>Category:</strong> <?= $row['category'] ?></p>
            <p><strong>Description:</strong> <?= $row['product_description'] ?></p>
            <p><strong>Price:</strong> £<?= $row['min_price'] ?> - £<?= $row['max_price'] ?></p>
            <p><strong>Total Votes:</strong> <?= $row['vote_count'] ?></p>
        </div>
    </div>
<?php } ?>
</div>

<div style="text-align: center; margin-top: 30px;">
<?php if (isset($_SESSION['resident_id'])): ?>
    <a href="logout.php" class="icon-button" style="background-color: #c0392b;">🚪 Logout</a>
<?php else: ?>
    <a href="register.php" class="icon-button">👤 Register</a>
    <a href="admin_login.php" class="icon-button" style="background-color: #e67e22;">🛠️ Admin Login</a>
    <a href="login.php" class="icon-button">🔐 Login</a>
    <a href="contact.php" class="icon-button">📞 Contact</a>
<?php endif; ?>
</div>

<a href="#top" class="icon-button" style="position:fixed; bottom:20px; right:20px; background:#2c3e50;">↑</a>

</body>
</html>
