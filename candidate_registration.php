<?php
// Connect to database 
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

$success_message = ""; // Variable to store the success message
$error_message = ""; // Variable to store the error message

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form inputs
    $election_id = trim($_POST['election_id']);
    $candidate_name = trim($_POST['candidate_name']);
    $address = trim($_POST['address']);
    $dob = $_POST['dob'];
    $qualification = trim($_POST['qualification']);
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
    // Proceed only if no error so far
    else {
        // Verify if electionId exists in the elections table
        $check_election_query = "SELECT * FROM elections WHERE electionId = ?";
        $stmt = $conn->prepare($check_election_query);
        $stmt->bind_param("s", $election_id);
        $stmt->execute();
        $election_result = $stmt->get_result();

        if ($election_result->num_rows == 0) {
            // If no such electionId exists, set error message
            $error_message = "Invalid Election ID";
        } elseif ($password !== $confirm_password) {
            // Check if passwords match
            $error_message = "Passwords do not match";
        } else {
            // Handle file upload for the photo
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                $photo = basename($_FILES['photo']['name']);
                $target_dir = "uploads/";
                $target_file = $target_dir . $photo;
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Check if image file is a real image or fake
                $check = getimagesize($_FILES['photo']['tmp_name']);
                if ($check !== false) {
                    $uploadOk = 1;
                } else {
                    $error_message = "File is not an image.";
                    $uploadOk = 0;
                }

                // Check file size (5MB maximum)
                if ($_FILES['photo']['size'] > 5000000) {
                    $error_message = "Sorry, your file is too large.";
                    $uploadOk = 0;
                }

                // Allow only certain file formats
                if (!in_array($imageFileType, ["jpg", "jpeg", "png"])) {
                    $error_message = "Sorry, only JPG, JPEG, and PNG files are allowed.";
                    $uploadOk = 0;
                }

                // If all checks pass, move the uploaded file
                if ($uploadOk == 1) {
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                        // Hash the password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        // Insert data into the database with hashed password using prepared statements
                        $sql = "INSERT INTO candidate_details (election_id, candidate_name, photo, address, dob, qualification, email, password)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $insert_stmt = $conn->prepare($sql);
                        $insert_stmt->bind_param("ssssssss", $election_id, $candidate_name, $target_file, $address, $dob, $qualification, $email, $hashed_password);

                        if ($insert_stmt->execute()) {
                            // Redirect to candidate login page after successful registration
                            header("Location: candidate_login.php");
                            exit(); // Ensure no further code is executed
                        } else {
                            $error_message = "Error: " . $insert_stmt->error;
                        }
                    } else {
                        $error_message = "Sorry, there was an error uploading your file.";
                    }
                }
            } else {
                $error_message = "Please upload a photo.";
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- (Your existing head content remains unchanged) -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Registration</title>
    <style>
        /* (Your existing CSS remains unchanged) */
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
            height: 170vh;
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
        input[type="text"], input[type="date"], input[type="file"], input[type="email"], input[type="password"], textarea {
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
        /* Add subtle form shadow and border animations */
        input, textarea {
            transition: box-shadow 0.3s, border-color 0.3s;
        }
        input:focus, textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }

        /* Responsive design */
        @media screen and (max-width: 600px) {
            .container {
                width: 100%;
                max-width: 300px;
                padding: 15px;
            }
            h2 {
                font-size: 20px;
            }
            input[type="submit"] {
                padding: 10px;
            }
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
        .login-link a {
            color: chocolate;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        // JavaScript to show popup message for error
        window.onload = function() {
            <?php if ($error_message): ?>
                alert("Error: <?php echo addslashes($error_message); ?>");
            <?php endif; ?>
        };
    </script>
</head>
<body>
    <div class="container">
        <h2>Candidate Registration</h2>
        <p style="text-align: center;">
            <a href="home.html" style="color: chocolate; text-decoration:underline;">Back Home</a>
        </p>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="election_id">Election ID</label>
            <input type="text" name="election_id" id="election_id" required>

            <label for="candidate_name">Candidate Name</label>
            <input type="text" name="candidate_name" id="candidate_name" required>

            <label for="photo">Upload Photo</label>
            <input type="file" name="photo" id="photo" accept="image/*" required>

            <label for="address">Address</label>
            <textarea name="address" id="address" rows="3" required></textarea>

            <label for="dob">Date of Birth</label>
            <input type="date" name="dob" id="dob" required>

            <label for="qualification">Qualification</label>
            <textarea name="qualification" id="qualification" rows="3" required></textarea>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <input type="submit" value="Register">
        </form>
        <div class="login-link">
            <p>Already registered? <a href="candidate_login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
