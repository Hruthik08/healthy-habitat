<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = intval($_POST['product_id']);
    $resident_id = intval($_POST['resident_id']);
    $vote = $_POST['vote'];

    // Check if the resident already voted for the product (optional: to avoid multiple votes)
    $check = $conn->prepare("SELECT * FROM votes WHERE product_id = ? AND resident_id = ?");
    $check->bind_param("ii", $product_id, $resident_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        // Record the vote
        $stmt = $conn->prepare("INSERT INTO votes (product_id, resident_id, vote) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $product_id, $resident_id, $vote);
        $stmt->execute();

        // Update vote count in products table
        $update = $conn->prepare("UPDATE products SET vote_count = vote_count + 1 WHERE product_id = ?");
        $update->bind_param("i", $product_id);
        $update->execute();
    }

    // Redirect back to homepage
    header("Location: index.php?voted=1");
    exit();
} else {
    echo "❌ Invalid request.";
}
