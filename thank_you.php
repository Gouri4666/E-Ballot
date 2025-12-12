<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: user_login.php"); // Redirect to login page if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            color: black;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        h1 {
            color: green;
            font-size: 28px;
        }
        p {
            font-size: 18px;
        }
        .home-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .home-button:hover {
            background-color: darkgreen;
        }
    </style>
</head>
<body>

<h1>Thank You for Your Vote!</h1>
<p>Your vote has been successfully submitted.</p>
<a href="user_home_page.php"><button class="home-button">Go to Home</button></a>

</body>
</html>
