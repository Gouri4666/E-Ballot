<?php
session_start(); // Start the session at the beginning of the file

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$port = 3307;
$dbname = "e_ballot_system";

$conn = new mysqli($servername, $username, $password, $dbname,$port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists with the given email
    $sql = "SELECT * FROM admin_details WHERE email = ?";
    $stmt = $conn->prepare($sql);//Prepares the SQL statement for execution.
    $stmt->bind_param("s", $email);//Binds the user-provided email to the SQL statement. The "s" indicates that the parameter is a string.
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify the entered password against the hashed password in the database
        if (password_verify($password, $row['password'])) {
            $_SESSION['email'] = $email; // Set session variable
            echo "<script>window.location.href='admin_home_page.php';</script>";
        } else {
            echo "<script>showAlert('Invalid password. Try Again!');</script>";
        }
    } else {
        echo "<script>showAlert('No user found with this email.');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login</title>
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
        .login-container {
            max-width: 500px;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.3);
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid white;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: white;
        }
        .login-container .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .login-container .input-group label {
            display: block;
            margin-bottom: 5px;
            color: white;
        }
        .login-container .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid white;
            border-radius: 5px;
            background-color: rgba(0, 0, 0, 0.2);
            color: white;
        }
        .login-container .input-group input::placeholder {
            color: #ccc;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: red;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color:  rgb(207, 52, 52);
        }
        .login-container a {
            display: block;
            margin-top: 10px;
            color: white;
            text-decoration: none;
        }
        .login-container a:hover {
            text-decoration: underline;
            color: red;
        }
        p {
            color: white;
        }
    </style>
    <script>
        function showAlert(message) {
            alert(message);
        }
    </script>
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>
    <p><a href="home.html">Back Home</a></p>
    <form action="" method="POST">
        <div class="input-group">
            <input type="email" id="email" name="email" required placeholder="Enter your email">
        </div>
        <div class="input-group">
            <input type="password" id="password" name="password" required placeholder="Enter your password">
        </div>
        <button type="submit" name="login">Login</button>
    </form>
    <p>Don't have an account? <a href="admin_registration.php">Register here</a></p>
</div>

</body>
</html>
