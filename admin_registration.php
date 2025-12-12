<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Registration</title>
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
            font-family: Arial, sans-serif;
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
            font-family: Arial, sans-serif;
        }
        p.login-link a:hover {
            text-decoration: underline;
            color: red;
        }
    </style>
    <script>
        // JavaScript to show popup message for error or success
        window.onload = function() {
            <?php if (isset($error_message)): ?>
                alert("Error: <?php echo addslashes($error_message); ?>");
            <?php elseif (isset($success_message)): ?>
                alert("<?php echo addslashes($success_message); ?>");
            <?php endif; ?>
        };
    </script>
</head>
<body>

<div class="container">
    <h2>Admin Registration</h2>
    <h4><a href="home.html">Back Home</a></h4>
    <form action="" method="post" id="registrationForm">
        <input type="text" id="name" name="name" required placeholder="Enter your name">
        <input type="email" id="email" name="email" required placeholder="Enter your email">
        <input type="password" id="password" name="password" required placeholder="Enter your password">
        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
        <button type="submit" name="submit">Register</button>
    </form>
    <p class="login-link">Already have an account? <a href="admin_login.php">Login here</a></p>
</div>

<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$port = 3307;
$dbname = "e_ballot_system";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = ""; // Variable to store the error message
$success_message = ""; // Variable to store the success message

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Retrieve and sanitize form inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    }
    // Check if email ends with @gmail.com
    elseif (substr(strtolower($email), -10) !== '@gmail.com') {
        $error_message = "Email address must end with @gmail.com.";
    }
    // Check if passwords match
    elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    }
    // Proceed only if no error so far
    else {
        // Optional: Check if email is already registered
        $check_email_query = "SELECT * FROM admin_details WHERE email = ?";
        $stmt = $conn->prepare($check_email_query);
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error_message = "This email is already registered.";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert the new admin into the database using prepared statements
                $insert_query = "INSERT INTO admin_details (name, email, password) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                if ($insert_stmt) {
                    $insert_stmt->bind_param("sss", $name, $email, $hashed_password);

                    if ($insert_stmt->execute()) {
                        $success_message = "Registration successful!";
                    } else {
                        $error_message = "Error: " . $insert_stmt->error;
                    }
                    $insert_stmt->close();
                } else {
                    $error_message = "Error preparing the registration statement.";
                }
            }
            $stmt->close();
        } else {
            $error_message = "Error preparing the email check statement.";
        }
    }

    // Display error or success message using JavaScript alert
    if ($error_message) {
        echo "<script>alert('Error: " . addslashes($error_message) . "');</script>";
    } elseif ($success_message) {
        echo "<script>
            alert('" . addslashes($success_message) . "');
            document.getElementById('registrationForm').reset();
        </script>";
    }
}

$conn->close();
?>

</body>
</html>
