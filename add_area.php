<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "healthy_habitat");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Add Area
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_area'])) {
    $area_name = trim($_POST['area_name']);
    if (!empty($area_name)) {
        $stmt = $conn->prepare("INSERT INTO areas (area_name) VALUES (?)");
        $stmt->bind_param("s", $area_name);
        $stmt->execute();
        $stmt->close();
    }
}

// Delete Area
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_area'])) {
    $area_id = $_POST['area_id'];
    $conn->query("DELETE FROM areas WHERE area_id = $area_id");
}

$areas = $conn->query("SELECT * FROM areas");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Areas</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        .form-box, .area-list { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        button { padding: 5px 10px; }
    </style>
</head>
<body>

<h2>🏙️ Admin Dashboard - Manage Areas</h2>

<div class="form-box">
    <h3>Add New Area</h3>
    <form method="POST">
        <input type="text" name="area_name" placeholder="Enter Area Name" required>
        <button type="submit" name="add_area">Add Area</button>
    </form>
</div>

<div class="area-list">
    <h3>Existing Areas</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Area Name</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $areas->fetch_assoc()): ?>
        <tr>
            <td><?= $row['area_id'] ?></td>
            <td><?= htmlspecialchars($row['area_name']) ?></td>
            <td>
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this area?');" style="display:inline;">
                    <input type="hidden" name="area_id" value="<?= $row['area_id'] ?>">
                    <button type="submit" name="delete_area">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<a href="admin_dashboard.php">← Back to Admin Dashboard</a>

</body>
</html>
