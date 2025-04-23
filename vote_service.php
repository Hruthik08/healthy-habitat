<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = intval($_POST['service_id']);
    $resident_id = intval($_POST['resident_id']);

    // Check if user has already voted for this service
    $check = $conn->prepare("SELECT COUNT(*) FROM service_votes WHERE service_id = ? AND resident_id = ?");
    $check->bind_param("ii", $service_id, $resident_id);
    $check->execute();
    $check->bind_result($vote_count);
    $check->fetch();
    $check->close();

    if ($vote_count > 0) {
        // Already voted, redirect back with a message
        header("Location: index.php?vote=already");
        exit();
    }

    // Record the vote in service_votes table
    $insert = $conn->prepare("INSERT INTO service_votes (service_id, resident_id) VALUES (?, ?)");
    $insert->bind_param("ii", $service_id, $resident_id);
    $insert->execute();
    $insert->close();

    // Update the vote count in services table
    $update = $conn->prepare("UPDATE services SET vote_count = vote_count + 1 WHERE service_id = ?");
    $update->bind_param("i", $service_id);
    $update->execute();
    $update->close();

    // Redirect with success message
    header("Location: index.php?vote=success");
    exit();
} else {
    // Invalid access
    header("Location: index.php?vote=invalid");
    exit();
}
?>
