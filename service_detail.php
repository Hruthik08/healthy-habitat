<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $service_id = $_GET['id'];

    // Fetch service details from the database
    $stmt = $conn->prepare("SELECT * FROM services WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    } else {
        // Redirect to home or show a message if service not found
        header("Location: index.php");
        exit;
    }
} else {
    // Redirect to home or show a message if no service ID is passed
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>🌿 Service Details - <?= htmlspecialchars($service['service_name']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        h1 { color: #2c3e50; }
        .nav-links a { margin-right: 15px; text-decoration: none; color: #2980b9; font-weight: bold; }
        .service-detail { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .button { display: inline-block; background: #27ae60; color: white; padding: 10px 15px; border-radius: 4px; text-decoration: none; }
    </style>
</head>
<body>

<a href="#top" class="arrow-top">↑</a>

<div class="nav-links">
    <a href="index.php">🏠 Home</a>
    <a href="register.php">👤 Register</a>
    <a href="login.php">🔐 Login</a>
    <a href="admin_login.php">🛠️ Admin</a>
    <a href="contact.php">📞 Contact</a>
</div>

<hr>

<div class="service-detail">
    <h1>Service: <?= htmlspecialchars($service['service_name']); ?></h1>
    <p><strong>Category:</strong> <?= htmlspecialchars($service['category']); ?></p>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($service['service_description'])); ?></p>
    <p><strong>Price:</strong> £<?= htmlspecialchars($service['price']); ?></p>

    <?php
    // Fetch the business provider information (assuming 'business_id' is related to a businesses table)
    $business_id = $service['business_id'];
    $business_stmt = $conn->prepare("SELECT business_name FROM businesses WHERE business_id = ?");
    $business_stmt->bind_param("i", $business_id);
    $business_stmt->execute();
    $business_result = $business_stmt->get_result();
    
    if ($business_result->num_rows > 0) {
        $business = $business_result->fetch_assoc();
        echo "<p><strong>Provider:</strong> " . htmlspecialchars($business['business_name']) . "</p>";
    }
    ?>

    <a href="cart.php?action=add&type=service&id=<?= $service['service_id']; ?>" class="button">Add to Cart</a>
    <a href="payment.php?service_id=<?= $service['service_id']; ?>" class="button">Book Now</a>
</div>

</body>
</html>
