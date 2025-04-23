<?php
session_start();
$success = "";
$error = "";

$conn = new mysqli("localhost", "root", "", "healthy_habitat");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Admin with this email already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO admins (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hashedPassword);
        if ($stmt->execute()) {
            header("Location: login.php?registered=1");
            exit();
        } else {
            $error = "Error registering admin.";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Admin - Healthy Habitat</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; text-align: center; padding: 40px; }
        form { display: inline-block; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input { padding: 10px; margin: 10px 0; width: 100%; border-radius: 5px; border: 1px solid #ccc; }
        input[type="submit"] { background: #27ae60; color: #fff; cursor: pointer; }
        .msg { font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Register New Admin</h2>
    <?php if ($error): ?><div class="msg" style="color: red;"><?= $error ?></div><?php endif; ?>
    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br>

        <input type="submit" value="Register Admin">
    </form>
</body>
</html>
