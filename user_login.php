<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
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
            width: 400px;
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
        .container input {
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
        p {
            color: white;
            text-align: center;
        }
        p.register-link {
            margin-top: 20px;
        }
        p.register-link a {
            color: white;
            text-decoration: none;
        }
        p.register-link a:hover {
            text-decoration: underline;
            color: red;
        }
        #home{
            justify-content: center;
            align-items: center;
        }
        #home a{
            text-decoration: none;
            color: white;
        }
        #home :hover{
            text-decoration: underline;
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>User Login</h2>
    <p id="home"><a href="home.html">Back Home</a></p>
    
    <form action="" method="post">
        <input type="email" id="email" name="email" required placeholder="Enter your email">
        <input type="password" id="password" name="password" required placeholder="Enter your password">
        <button type="submit" name="submit">Login</button>
    </form>
    <p class="register-link">Don't have an account? <a href="user_registration.php">Register here</a></p>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'e_ballot_system',3307);

    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    }

    // Prepare and execute statement
    $stmt = $conn->prepare("SELECT password, electionId FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($hashed_password, $electionId);
    $stmt->fetch();

    if ($hashed_password && password_verify($password, $hashed_password)) {
        // Password matches, start session and store email and electionId
        session_start();
        $_SESSION['email'] = $email;
        $_SESSION['electionId'] = $electionId; // Store electionId in session
        header("Location: user_home_page.php");
        exit();
    } else {
        echo "<script>alert('Invalid email or password. Please try again.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

</body>
</html>
