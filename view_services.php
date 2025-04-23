<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>View Wellness Services - Healthy Habitat Network</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5fcfa; margin: 20px; }
        h1, h2 { color: #1e656d; }
        .service-card {
            background: #fff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .filter-form input {
            margin: 5px;
            padding: 5px;
        }
        .back-link { margin-bottom: 20px; display: inline-block; }
    </style>
</head>
<body>

<a href="index.php" class="back-link">← Back to Home</a>

<h1>🧘 Wellness Services</h1>
<p>Explore local services that promote a healthier lifestyle.</p>

<h2>🔍 Filter Services</h2>
<form method="GET" class="filter-form">
    Category: <input type="text" name="category">
    Max Price: <input type="number" name="max_price">
    <input type="submit" value="Search">
</form>

<?php
// --- Fetch services with filters ---
$query = "SELECT * FROM services WHERE 1=1";
$params = [];
$types = "";

// Filter: category
if (!empty($_GET['category'])) {
    $query .= " AND category LIKE ?";
    $params[] = "%" . $_GET['category'] . "%";
    $types .= "s";
}

// Filter: max_price
if (!empty($_GET['max_price'])) {
    $query .= " AND price <= ?";
    $params[] = $_GET['max_price'];
    $types .= "i";
}

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

echo "<div class='results'>";
if ($result->num_rows > 0) {
    while ($service = $result->fetch_assoc()) {
        echo "<div class='service-card'>
                <h3>{$service['service_name']}</h3>
                <p><strong>Description:</strong> {$service['service_description']}</p>
                <p><strong>Category:</strong> {$service['category']}</p>
                <p><strong>Price:</strong> £ {$service['price']}</p>
                <p><strong>Business ID:</strong> {$service['business_id']}</p>
              </div>";
    }
} else {
    echo "<p>No services match your filters.</p>";
}
echo "</div>";
?>

</body>
</html>
