<?php
// Connect to database (Update these with your actual database credentials)
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

$error_message = ""; // Variable to store the error message
$success_message = ""; // Variable to store success message

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM candidate_details WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user details
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Start a session and store user information
            session_start();
            $_SESSION['candidate_id'] = $user['id']; // Store candidate ID in session
            $_SESSION['candidate_name'] = $user['candidate_name']; // Store candidate name
            header("Location: candidate_dashboard.php"); // Redirect to candidate dashboard
            exit(); // Ensure no further code is executed
        } else {
            $error_message = "Invalid email or password.";
        }
    } else {
        $error_message = "Invalid email or password.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('tick.jpg'); 
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.3); /* Transparent background */
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 450px; /* Smaller container size */
            border-radius: 15px;
            backdrop-filter: blur(5px);
            border: 2px solid rgba(0, 0, 0, 0.3); /* Add border */
        }
        h2 {
            text-align: center;
            color: chocolate;
            font-size: 22px;
        }
        label {
            font-size: 14px;
            color: chocolate;
            display: block;
            margin-bottom: 5px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
            background-color: rgba(255, 255, 255, 0.8); /* Slight transparency */
        }
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            background-color: chocolate;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: white;
            color: chocolate;
            border: 1px solid chocolate;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Candidate Login</h2>
        <p style="text-align: center;">
    <a href="home.html" style="color: chocolate; text-decoration: underline;">Back Home</a>
</p>
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <input type="submit" value="Login">
        </form>
        <div class="login-link">
            <p>Not registered? <a href="candidate_registration.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
