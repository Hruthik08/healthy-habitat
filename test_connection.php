?><?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthy_habitat";
 
// Try connecting
$conn = new mysqli($servername, $username, $password, $dbname);
 
// Check
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    echo "✅ Connected to the database successfully!";
}
?>