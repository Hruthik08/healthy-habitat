<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_type = $_POST['service_type'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $cost = $_POST['cost'];
    $provider = $_POST['provider'];

    $sql = "INSERT INTO services (service_type, description, duration, cost, provider) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssds", $service_type, $description, $duration, $cost, $provider);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Service added successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Error: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Wellness Service - Healthy Habitat Network</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5fcfa; padding: 20px; }
        h1 { color: #1e656d; }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        input, textarea {
            width: 100%;
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background: #1e656d;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #144b50;
        }
        .back-link {
            margin-bottom: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>

<a href="index.php" class="back-link">← Back to Home</a>

<h1>➕ Add New Wellness Service</h1>

<form method="POST">
    Service Type: <input type="text" name="service_type" required><br>
    Description: <textarea name="description" rows="3" required></textarea><br>
    Duration (e.g., 1 hour, 30 min): <input type="text" name="duration" required><br>
    Cost (in $): <input type="number" name="cost" step="0.01" required><br>
    Provider Name: <input type="text" name="provider" required><br>
    <input type="submit" value="Add Service">
</form>

</body>
</html>
