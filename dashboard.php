<?php
session_start();
if (!isset($_SESSION['resident_id'])) {
    header("Location: login.php");
    exit();
}
echo "<h1>Welcome to your Dashboard!</h1>";
echo "<a href='vote_product.php'>Vote on Products</a><br>";
echo "<a href='view_results.php'>View Voting Results</a><br>";
echo "<a href='logout.php'>Logout</a>";
?>
