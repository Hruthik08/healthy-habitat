<?php
include 'db_connect.php';

$sql = "
SELECT p.product_name,
       SUM(CASE WHEN v.vote = 'yes' THEN 1 ELSE 0 END) AS yes_votes,
       SUM(CASE WHEN v.vote = 'no' THEN 1 ELSE 0 END) AS no_votes
FROM products p
LEFT JOIN votes v ON p.product_id = v.product_id
GROUP BY p.product_id
";

$result = $conn->query($sql);

echo "<h1>Voting Results</h1>";
echo "<table border='1'>
<tr><th>Product</th><th>Yes Votes</th><th>No Votes</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['product_name']}</td><td>{$row['yes_votes']}</td><td>{$row['no_votes']}</td></tr>";
}
echo "</table>";
?>
