<?php
require_once "db_connect.php";

// Initialize variables for filters from GET request
$product_name = isset($_GET['product_name']) ? $_GET['product_name'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : null;  // Ensure it's checked
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Start building the SQL query for products
$product_sql = "SELECT * FROM products WHERE 1";

// Prepare an array to hold parameters for binding for products
$product_params = [];
$product_types = '';

// Add filter conditions to the SQL query if values are provided for products
if ($product_name) {
    $product_sql .= " AND LOWER(product_name) LIKE LOWER(?)"; // Case insensitive match
    $product_params[] = "%" . $product_name . "%"; // Add wildcards for partial match
    $product_types .= "s"; // 's' for string
}
if ($min_price !== null && $min_price !== '') {
    $product_sql .= " AND min_price >= ?";
    $product_params[] = $min_price;
    $product_types .= "d"; // 'd' for double (decimal number)
}
if ($max_price !== null && $max_price !== '') {
    $product_sql .= " AND max_price <= ?";
    $product_params[] = $max_price;
    $product_types .= "d"; // 'd' for double
}
if ($category) {
    $product_sql .= " AND LOWER(category) LIKE LOWER(?)"; // Case insensitive match
    $product_params[] = "%" . $category . "%"; // Add wildcards for partial match
    $product_types .= "s"; // 's' for string
}

// Prepare the statement for products
$product_stmt = $conn->prepare($product_sql);

// Bind the parameters dynamically for products
if ($product_params) {
    $product_stmt->bind_param($product_types, ...$product_params);
}

// Execute the query for products
$product_stmt->execute();
$product_result = $product_stmt->get_result();

// Now, add the SQL query for services (exclude price filtering for services)
$service_sql = "SELECT * FROM services WHERE 1";
$service_params = [];
$service_types = '';

// Add filter conditions to the SQL query if values are provided for services
if ($product_name) {
    $service_sql .= " AND LOWER(service_name) LIKE LOWER(?)"; // Case insensitive match
    $service_params[] = "%" . $product_name . "%"; // Add wildcards for partial match
    $service_types .= "s"; // 's' for string
}
if ($category) {
    $service_sql .= " AND LOWER(category) LIKE LOWER(?)"; // Case insensitive match
    $service_params[] = "%" . $category . "%"; // Add wildcards for partial match
    $service_types .= "s"; // 's' for string
}

// Prepare the statement for services
$service_stmt = $conn->prepare($service_sql);

// Bind the parameters dynamically for services
if ($service_params) {
    $service_stmt->bind_param($service_types, ...$service_params);
}

// Execute the query for services
$service_stmt->execute();
$service_result = $service_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Products/Services</title>
</head>
<body>

<!-- Search Form -->
<h2>Search for Products/Services</h2>
<form method="GET" action="filter_products.php">
    <label for="product_name">Product/Service Name:</label>
    <input type="text" name="product_name" id="product_name" value="<?php echo htmlspecialchars($product_name); ?>" placeholder="Enter product/service name">

    <label for="min_price">Minimum Price:</label>
    <input type="number" name="min_price" id="min_price" value="<?php echo htmlspecialchars($min_price); ?>" placeholder="Enter minimum price" step="0.01">

    <label for="max_price">Maximum Price:</label>
    <input type="number" name="max_price" id="max_price" value="<?php echo htmlspecialchars($max_price); ?>" placeholder="Enter maximum price" step="0.01">

    <label for="category">Category:</label>
    <input type="text" name="category" id="category" value="<?php echo htmlspecialchars($category); ?>" placeholder="Enter category">

    <button type="submit">Search</button>
</form>

<hr>

<!-- Display Filtered Products -->
<?php if ($_SERVER['REQUEST_METHOD'] == 'GET' && $product_result->num_rows > 0): ?>
    <h2>Products Found:</h2>
    <?php
    while ($row = $product_result->fetch_assoc()) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 15px;'>";
        echo "<h3>" . htmlspecialchars($row['product_name']) . "</h3>";
        echo "<p><strong>Price:</strong> £" . htmlspecialchars($row['max_price']) . "</p>";
        echo "<p><strong>Category:</strong> " . htmlspecialchars($row['category']) . "</p>";
        echo "<p><strong>Description:</strong> " . htmlspecialchars($row['product_description']) . "</p>";
        echo "<a href='product_detail.php?id=" . $row['product_id'] . "'>View Details</a> | ";
        echo "<a href='payment.php?id=" . $row['product_id'] . "'>Buy Now</a>";
        echo "</div>";
    }
    ?>
<?php elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && $product_result->num_rows == 0): ?>
    <p>No products found matching your criteria.</p>
<?php endif; ?>

<!-- Display Filtered Services -->
<?php if ($_SERVER['REQUEST_METHOD'] == 'GET' && $service_result->num_rows > 0): ?>
    <h2>Services Found:</h2>
    <?php
    while ($row = $service_result->fetch_assoc()) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 15px;'>";
        echo "<h3>" . htmlspecialchars($row['service_name']) . "</h3>";
        echo "<p><strong>Category:</strong> " . htmlspecialchars($row['category']) . "</p>";
        echo "<p><strong>Description:</strong> " . htmlspecialchars($row['service_description']) . "</p>";
        echo "<a href='view_services.php?id=" . $row['service_id'] . "'>View Details</a> | ";
        echo "<a href='payment.php?id=" . $row['service_id'] . "'>Buy Now</a>";
        echo "</div>";
    }
    ?>
<?php elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && $service_result->num_rows == 0): ?>
    <p>No services found matching your criteria.</p>
<?php endif; ?>

</body>
</html>
