<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resident_id = $_POST["resident_id"];
    $product_id = $_POST["product_id"];
    $vote = $_POST["vote"];

    $stmt = $conn->prepare("INSERT INTO votes (resident_id, product_id, vote) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $resident_id, $product_id, $vote);

    if ($stmt->execute()) {
        echo "Vote recorded!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

