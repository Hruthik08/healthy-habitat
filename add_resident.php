<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resident_name = $_POST['resident_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $age_group = $_POST['age_group'];
    $gender = $_POST['gender'];
    $location_id = $_POST['location_id'];
    $area_of_interest = $_POST['area_of_interest'];

    $sql = "INSERT INTO residents (resident_name, email, password, age_group, gender, location_id, area_of_interest) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssis", $resident_name, $email, $password, $age_group, $gender, $location_id, $area_of_interest);

    if ($stmt->execute()) {
        echo "<div class='success'>🎉 Resident added successfully!</div>";
    } else {
        echo "<div class='error'>❌ Error: " . $stmt->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Healthy Habitat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            text-align: center;
            padding: 40px;
        }
        h2 {
            color: #2c3e50;
        }
        form {
            display: inline-block;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: left;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #219150;
        }
        .success {
            color: #27ae60;
            font-weight: bold;
            margin-top: 20px;
        }
        .error {
            color: #c0392b;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>👤 Register to Join the Healthy Habitat Network</h2>

<form method="POST">
    <label>Name:</label>
    <input type="text" name="resident_name" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <label>Age Group:</label>
    <input type="text" name="age_group" required>

    <label>Gender:</label>
    <input type="text" name="gender" required>

    <label>Location ID:</label>
    <input type="number" name="location_id" required>

    <label>Area of Interest:</label>
    <input type="text" name="area_of_interest" required>

    <input type="submit" value="Register">
</form>

</body>
</html>
