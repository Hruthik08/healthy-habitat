<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

    $sql = "SELECT resident_id, password, resident_name, email FROM residents WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($resident_id, $hashed_password, $resident_name, $resident_email);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['resident_id'] = $resident_id;
            $_SESSION['full_name'] = $resident_name;
            $_SESSION['resident_email'] = $resident_email;

            header("Location: $redirect");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login - Healthy Habitat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            text-align: center;
            padding: 40px;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        form {
            display: inline-block;
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
            text-align: left;
            width: 320px;
        }
        label {
            font-size: 16px;
            color: #333;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 10px 0;
            font-size: 18px;
            border-radius: 6px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #219150;
        }
        .error {
            color: red;
            font-weight: bold;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <h2>User Login</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <input type="submit" value="Login">
    </form>

</body>
</html>
