<?php
// Database connection details
$servername = "localhost"; //  database server
$username = "root"; //  database username
$password = ""; //  database password
$dbname = "e_ballot_system"; //  database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and validate input
$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$feedback = htmlspecialchars($_POST['feedback']);

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO feedbacks (name, email, feedback) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $feedback);

// Execute the statement
if ($stmt->execute()) {
    // Redirect to the same page or a thank you page
    header("Location: home.html"); // Redirect to home page or thank you page
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
