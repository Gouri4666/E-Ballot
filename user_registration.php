<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('tick.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.3);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 500px;
            max-width: 100%;
            backdrop-filter: blur(10px);
            border: 2px solid white;
        }
        .container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: white;
        }
        .container label {
            display: block;
            margin-bottom: 5px;
            color: white;
        }
        .container input, .container select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid white;
            border-radius: 5px;
            font-size: 14px;
            background-color: rgba(0, 0, 0, 0.2);
            color: white;
        }
        .container input::placeholder {
            color: #ccc;
        }
        .container button {
            width: 100%;
            padding: 10px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .container button:hover {
            background-color: rgb(207, 52, 52);
        }
        h4 {
            text-align: center;
            margin-bottom: 20px;
        }
        h4 a {
            text-decoration: none;
            color: white;
        }
        h4 a:hover {
            text-decoration: underline;
            color: red;
        }
        p {
            color: white;
        }
        p.login-link {
            text-align: center;
            margin-top: 20px;
        }
        p.login-link a {
            text-decoration: none;
            color: white;
        }
        p.login-link a:hover {
            text-decoration: underline;
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>User Registration</h2>
    <h4><a href="home.html">Back Home</a></h4>
    <form action="" method="post">
        <input type="text" id="name" name="name" required placeholder="Enter your name">
        <input type="email" id="email" name="email" required placeholder="Enter your email">
        <select id="gender" name="gender" required>
            <option value="">Select Your Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
        <input type="text" id="phone" name="phone" required placeholder="Enter your phone number">
        <input type="text" id="address" name="address" required placeholder="Enter your address">
        <input type="number" id="election_id" name="election_id" required placeholder="Enter your election ID">
        <input type="password" id="password" name="password" required placeholder="Enter your password">
        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
        <button type="submit" name="submit">Register</button>
    </form>
    <p class="login-link">Already have an account? <a href="user_login.php">Login here</a></p>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $electionId = $_POST['election_id']; 
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if phone number contains exactly 10 digits
    if (strlen($phone) != 10 || !ctype_digit($phone)) {
        echo "<script>alert('Phone number must contain exactly 10 digits.');</script>";
    } elseif ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
    } else {
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'e_ballot_system',3307);

        if ($conn->connect_error) {
            die('Connection Failed: ' . $conn->connect_error);
        }

        // Check if the election ID exists
        $election_check = $conn->prepare("SELECT COUNT(*) FROM elections WHERE electionId = ?");
        $election_check->bind_param("i", $electionId);
        $election_check->execute();
        $election_check->bind_result($count);
        $election_check->fetch();
        $election_check->close();

        if ($count == 0) {
            echo "<script>alert('Invalid Election ID. Please enter a valid Election ID.');</script>";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO users (name, email, gender, phone, address, electionId, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssis", $name, $email, $gender, $phone, $address, $electionId, $hashed_password);

            if ($stmt->execute()) {
                echo "<script>alert('You have registered successfully!'); window.location.href = 'user_login.php';</script>";
            } else {
                echo "<script>alert('Registration failed. Please try again.');</script>";
            }

            $stmt->close();
        }

        $conn->close();
    }
}
?>

</body>
</html>
