<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: user_login.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'e_ballot_system',3307);

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Fetch user details using the stored email from the session
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT name, electionId FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($name, $electionId);
$stmt->fetch();
$stmt->close();

// Store electionId in session
$_SESSION['electionId'] = $electionId;

// Fetch the election name corresponding to electionId
$election_stmt = $conn->prepare("SELECT electionName FROM elections WHERE electionId = ?");
$election_stmt->bind_param("i", $electionId);
$election_stmt->execute();
$election_stmt->bind_result($electionName);
$election_stmt->fetch();
$election_stmt->close();

$conn->close();

// Logout handling
if (isset($_POST['logout'])) {
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session
    header("Location: user_login.php"); // Redirect to the login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white; /* Set background to white */
            margin: 0;
            color: black; /* Set text color to black */
        }
        .navbar {
            background-color: red; /* Red background for the navbar */
            padding: 10px;
            text-align: right;
        }
        .navbar button {
            background-color: white; /* White button */
            color: red; /* Red text */
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .navbar button:hover {
            background-color: rgb(207, 52, 52); /* Darker red on hover */
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin-right: 10px; /* Space between links */
        }
        .navbar a:hover {
            background-color: rgb(207, 52, 52); /* Darker red on hover */
            border-radius: 5px;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9); /* Slightly transparent white box */
            padding: 30px; /* Increased padding */
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2); /* Enhanced shadow */
            margin: 50px auto; /* Center the container */
            width: 450px; /* Increased width for the container */
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 24px; /* Increased font size */
            font-family: cursive;
            color: red; /* Red color for the user's name */
        }
        .election-name {
            color: red; /* Red color for the election name */
            font-weight: bold; /* Bold font for emphasis */
            font-size: 22px; /* Increased font size */
            font-family: cursive;
        }
        .vote-container {
            background-color: rgba(255, 255, 255, 0.9); /* Similar transparent white box */
            padding: 20px; /* Padding for the vote box */
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2); /* Enhanced shadow */
            margin: 20px auto; /* Center the vote container */
            width: 450px; /* Same width as the other box */
            text-align: center;
        }
        .vote-button {
            background-color: red; /* Red background for the button */
            color: white; /* White text */
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .vote-button:hover {
            background-color: rgb(207, 52, 52); /* Darker red on hover */
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="user_check_result.php">Check Result</a>
    <form action="" method="post" style="display: inline;">
        <button type="submit" name="logout">Logout</button>
    </form>
</div>

<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
    <p>Your Election ID: <?php echo htmlspecialchars($electionId); ?></p>
    <p>The election you are participating in is: <span class="election-name"><?php echo htmlspecialchars($electionName); ?></span></p>
</div>

<div class="vote-container">
    <p>Click here to vote for your candidate</p>
    <form action="vote_for_candidate.php" method="post">
        <button type="submit" class="vote-button">Vote Now</button>
    </form>
</div>

</body>
</html>
