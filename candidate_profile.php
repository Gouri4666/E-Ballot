<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['candidate_id'])) {
    header("Location: candidate_login.php");
    exit();
}

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy(); // Destroy all session data
    header("Location: candidate_login.php"); // Redirect to login page
    exit();
}

// Candidate details
$candidate_id = $_SESSION['candidate_id'];
$candidate_name = $_SESSION['candidate_name'];

// Connect to the database (replace with your actual database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$port="3307";
$dbname = "e_ballot_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname,$port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch candidate status and post approval status from the database
$sql = "SELECT candidate_status, post_approval_status, selected_post FROM candidate_details WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$stmt->bind_result($candidate_status, $post_approval_status, $selected_post);
$stmt->fetch();
$stmt->close();

// Close the connection
$conn->close();

// Determine the status message based on the post_approval_status value
$status_message = $post_approval_status == 1 ? "You are approved for participating in the election." : "You are not approved yet.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('tick.jpg'); 
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: rgba(255, 255, 255, 0.7);
        }
        .header h1 {
            margin: 0;
            color: chocolate;
        }
        .menu {
            display: flex;
        }
        .menu a {
            margin-left: 20px;
            text-decoration: none;
            color: chocolate;
            padding: 8px 16px;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.5);
            transition: background-color 0.3s;
        }
        .menu a:hover {
            background-color: rgba(255, 255, 255, 0.8);
        }
        .container {
            padding: 20px;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8);
            margin: 20px auto;
            border-radius: 10px;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        .status-box {
            background-color: rgba(255, 255, 255, 0.9);
            border: 2px solid chocolate;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            color: chocolate;
        }
        .logout-button {
            background-color: chocolate;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>E-BALLOT</h1>
        <div class="menu">
            <a href="candidate_dashboard.php">Home</a>
            <a href="candidate_profile.php">Profile</a>
            <a href="candidate_check_result.php">Check Result</a> <!-- Added Check Result button -->
            <form method="post" style="display:inline;">
                <input type="submit" name="logout" value="Logout" class="logout-button">
            </form>
        </div>
    </div>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($candidate_name); ?>!</h2>
        <div class="status-box">
            <p><?php echo $status_message; ?></p>
        </div>
    </div>
</body>
</html>
