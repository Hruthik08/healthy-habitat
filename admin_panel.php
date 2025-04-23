<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit();
}

// DB connection (based on your db config)
$conn = new mysqli("localhost", "root", "", "healthy_habitat");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Handle admin actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    if ($action == "update_price") {
        $price = floatval($_POST['price']);
        $conn->query("UPDATE products_services SET price='$price' WHERE id=$id");
    } else {
        $conn->query("UPDATE products_services SET status='$action' WHERE id=$id");
    }
}

// Fetch data
$result = $conn->query("SELECT * FROM products_services");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Admin Panel - Manage Products & Services</h2>
    <p><a href="logout.php">Logout</a></p>
    <table border="1" cellpadding="8">
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= $row['type'] ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="action" value="update_price">
                    <input type="number" step="0.01" name="price" value="<?= $row['price'] ?>" size="5">
                    <button type="submit">Update</button>
                </form>
            </td>
            <td><?= $row['status'] ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button name="action" value="available">Enable</button>
                    <button name="action" value="disabled">Disable</button>
                    <button name="action" value="rejected">Reject</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
