<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "healthy_habitat");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$successProduct = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $id = $_POST['product_id'] ?? null;

    if ($action === "add") {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $min_price = $_POST['min_price'];
        $max_price = $_POST['max_price'];
        $description = $_POST['description'];

        $result = $conn->query("SELECT business_id FROM businesses ORDER BY RAND() LIMIT 1");
        $row = $result->fetch_assoc();
        $business_id = $row['business_id'];

        $stmt = $conn->prepare("INSERT INTO products (product_name, category, min_price, max_price, product_description, business_id, vote_count, status) VALUES (?, ?, ?, ?, ?, ?, 0, 'available')");
        $stmt->bind_param("ssddsi", $name, $category, $min_price, $max_price, $description, $business_id);
        if (!$stmt->execute()) {
            die("Insert failed: " . $stmt->error);
        }
        $stmt->close();

        $successProduct = [
            'name' => $name,
            'category' => $category,
            'min_price' => $min_price,
            'max_price' => $max_price,
            'description' => $description
        ];
    } elseif ($action === "update_price") {
        $min_price = $_POST['min_price'];
        $max_price = $_POST['max_price'];
        $conn->query("UPDATE products SET min_price='$min_price', max_price='$max_price' WHERE product_id=$id");
    } elseif ($action === "toggle_stock") {
        $status = $_POST['current_status'] === 'available' ? 'out_of_stock' : 'available';
        $conn->query("UPDATE products SET status='$status' WHERE product_id=$id");
    } elseif ($action === "reject") {
        $conn->query("DELETE FROM products WHERE product_id=$id");
    } elseif ($action === "add_area") {
        $area_name = $_POST['area_name'];
        $stmt = $conn->prepare("INSERT INTO areas (area_name) VALUES (?)");
        $stmt->bind_param("s", $area_name);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === "delete_area") {
        $area_id = $_POST['area_id'];
        $conn->query("DELETE FROM areas WHERE area_id=$area_id");
    }
}

$search = $_GET['search'] ?? '';
$filter_category = $_GET['filter_category'] ?? '';

$search = $conn->real_escape_string($search);
$filter_category = $conn->real_escape_string($filter_category);

$query = "SELECT * FROM products WHERE 1=1";
if ($search !== '') {
    $query .= " AND (product_name LIKE '%$search%' OR product_description LIKE '%$search%')";
}
if ($filter_category !== '') {
    $query .= " AND category = '$filter_category'";
}
$products = $conn->query($query);
$areas = $conn->query("SELECT * FROM areas");
?>

<!DOCTYPE html>
<html>
<head>
    <title>🌿 Admin Dashboard | Healthy Habitat Network</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; color: #2c3e50; margin: 20px; }
        h1, h2, h3 { color: #2c3e50; text-align: center; }
        .icon-button { background: #27ae60; color: white; padding: 10px 20px; border-radius: 9px; text-decoration: none; cursor: pointer; display: inline-block; margin: 5px 0; }
        .icon-button:hover { background-color: #2c3e50; color: white; }
        .card { background: white; padding: 20px; margin-bottom: 30px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; background-color: white; margin-bottom: 40px; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: center; }
        th { background-color: #ecf0f1; }
        form.inline { display: inline-block; }
        input[type="text"], input[type="number"], textarea { width: 100%; padding: 6px; margin: 5px 0; box-sizing: border-box; }
        .side-by-side { display: flex; gap: 50px; flex-wrap: wrap; }
        .disabled { background-color: gray !important; cursor: not-allowed !important; }
        .modal-bg { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.6); display: none; justify-content: center; align-items: center; z-index: 1000; }
        .modal { background: white; padding: 30px 40px; border-radius: 10px; text-align: center; font-size: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        .modal button { margin: 20px 10px 0; padding: 10px 20px; font-size: 18px; }
        .admin-info { position: absolute; top: 20px; left: 20px; background: #ecf0f1; padding: 10px 20px; border-radius: 8px; font-size: 16px; color: #2c3e50; }
        .success-box { background-color: #e0f7e9; border: 1px solid #2ecc71; padding: 15px; margin-top: 20px; border-radius: 10px; font-size: 16px; }
    </style>
</head>
<body>

<div class="admin-info">
    👤 Logged in as: <strong><?= htmlspecialchars($_SESSION['admin']) ?></strong>
</div>

<h1>🌿 Healthy Habitat Network</h1>
<h2>🛠️ Admin Dashboard</h2>

<div class="side-by-side">
    <div class="card" style="flex: 1;">
        <h3>Add New Product</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <label>Name:</label>
            <input type="text" name="name" required>
            <label>Category:</label>
            <input type="text" name="category" required>
            <label>Min Price (£):</label>
            <input type="number" step="0.01" name="min_price" required>
            <label>Max Price (£):</label>
            <input type="number" step="0.01" name="max_price" required>
            <label>Description:</label>
            <textarea name="description" required></textarea>
            <button class="icon-button" type="submit">➕ Add Product</button>
        </form>

        <?php if ($successProduct): ?>
            <div class="success-box" id="success-box">
                ✅ <strong>Product added successfully!</strong><br><br>
                <strong>Name:</strong> <?= htmlspecialchars($successProduct['name']) ?><br>
                <strong>Category:</strong> <?= htmlspecialchars($successProduct['category']) ?><br>
                <strong>Price:</strong> £<?= $successProduct['min_price'] ?> - £<?= $successProduct['max_price'] ?><br>
                <strong>Description:</strong> <?= htmlspecialchars($successProduct['description']) ?>
            </div>
            <script>
                setTimeout(() => {
                    const box = document.getElementById('success-box');
                    if (box) box.style.display = 'none';
                }, 20000);
            </script>
        <?php endif; ?>
    </div>

    <div class="card" style="flex: 1;">
        <h3>Manage Areas</h3>
        <form method="POST" style="margin-bottom: 20px;">
            <input type="hidden" name="action" value="add_area">
            <label>Area Name:</label>
            <input type="text" name="area_name" required>
            <button class="icon-button" type="submit">➕ Add Area</button>
        </form>

        <table>
            <tr><th>Area</th><th>Action</th></tr>
            <?php while ($area = $areas->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($area['area_name']) ?></td>
                    <td>
                        <form method="POST" class="inline" onsubmit="return confirm('Delete this area?');">
                            <input type="hidden" name="action" value="delete_area">
                            <input type="hidden" name="area_id" value="<?= $area['area_id'] ?>">
                            <button class="icon-button" type="submit">🗑️ Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<!-- SEARCH AND FILTER FORM -->
<form method="GET" style="margin-bottom: 20px; text-align: center;">
    <input type="text" name="search" placeholder="🔍 Search product..." value="<?= htmlspecialchars($search) ?>" style="padding: 10px; width: 250px; font-size: 16px; border-radius: 6px; border: 1px solid #ccc;">
    <select name="filter_category" style="padding: 10px; font-size: 16px; border-radius: 6px; border: 1px solid #ccc;">
        <option value="">All Categories</option>
        <?php
        $categoryRes = $conn->query("SELECT DISTINCT category FROM products");
        while ($cat = $categoryRes->fetch_assoc()):
            $selected = ($filter_category === $cat['category']) ? 'selected' : '';
        ?>
            <option value="<?= htmlspecialchars($cat['category']) ?>" <?= $selected ?>><?= htmlspecialchars($cat['category']) ?></option>
        <?php endwhile; ?>
    </select>
    <button class="icon-button" type="submit">Filter</button>
</form>

<!-- FILTERED PRODUCTS TABLE -->
<h3 style="text-align:center;">📦 Products</h3>
<table>
    <tr>
        <th>Name</th>
        <th>Category</th>
        <th>Min Price</th>
        <th>Max Price</th>
        <th>Description</th>
        <th>Status</th>
        <th>Update Price</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $products->fetch_assoc()): ?>
        <tr style="<?= $row['status'] === 'out_of_stock' ? 'background-color:#eee; color:#999;' : '' ?>">
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td>£<?= $row['min_price'] ?></td>
            <td>£<?= $row['max_price'] ?></td>
            <td><?= htmlspecialchars($row['product_description']) ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <form method="POST" class="inline">
                    <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                    <input type="hidden" name="action" value="update_price">
                    <input type="number" step="0.01" name="min_price" value="<?= $row['min_price'] ?>" required><br>
                    <input type="number" step="0.01" name="max_price" value="<?= $row['max_price'] ?>" required><br>
                    <button class="icon-button" type="submit">💰 Change</button>
                </form>
            </td>
            <td>
                <form method="POST" class="inline" style="margin-bottom: 5px;">
                    <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                    <input type="hidden" name="action" value="toggle_stock">
                    <input type="hidden" name="current_status" value="<?= $row['status'] ?>">
                    <button class="icon-button"><?= $row['status'] === 'available' ? '❌ Out of Stock' : '✅ In Stock' ?></button>
                </form>
                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                    <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                    <input type="hidden" name="action" value="reject">
                    <button class="icon-button">🗑️ Delete</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<h3 style="text-align:center; color:#2c3e50;">✨ Thank you for managing the Healthy Habitat Network</h3>
<div style="text-align:center; margin-top: 30px;">
    <a href="logout.php" class="icon-button">🚪 Logout</a>
</div>

</body>
</html>
