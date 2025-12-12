<?php
session_start();

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();//Clears all session variables.
    session_destroy();//Destroys the session 
    header("Location: admin_login.php");
    exit();
}

$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $port = 3307;
    $dbname = "e_ballot_system";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname,$port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve form data
    $electionId = $_POST['electionId'];
    $electionName = $_POST['electionName'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // Check if the ID already exists
    $sqlCheck = "SELECT * FROM elections WHERE electionId='$electionId'";
    $result = $conn->query($sqlCheck);

    if ($result->num_rows > 0) {
        $successMessage = "Election ID already exists. Please use a different ID.";
    } else {
        // Insert new election record with start and end dates
        $sql = "INSERT INTO elections (electionId, electionName, startDate, endDate) VALUES ('$electionId', '$electionName', '$startDate', '$endDate')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['electionId'] = $electionId; // Store electionId in session
            $successMessage = "New election created successfully.";
        } else {
            $successMessage = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Election</title>
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 20px;
            color: white;
        }

        .navbar-brand a {
            color: white;
            text-decoration: none;
            font-size: 24px;
        }

        .navbar-menu a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-size: 18px;
        }

        .navbar-menu {
            display: flex;
        }

        /* Main section styles */
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Centered box section */
        .box-section {
            text-align: center;
            border: 2px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            max-width: 500px;
            width: 100%;
            margin: 20px;
        }

        /* Form styles */
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
        }

        input {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        #message {
            color: red;
            font-family: cursive;
        }

        button {
            margin-top: 20px;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #555;
        }

        /* Section styles */
        section {
            padding: 20px;
            border-bottom: 1px solid #ccc;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
        }

        /* Unique number generator styles */
        .unique-number-box {
            text-align: center;
            border: 2px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            max-width: 500px;
            width: 100%;
            margin: 20px;
        }

        .generator-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .generator-container .box-section, .generator-container .unique-number-box {
            flex: 1 1 300px;
            margin: 10px;
        }

        @media (max-width: 768px) {
            .navbar-menu {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
    <script>
        function generateNumber() {
            const randomNumber = Math.floor(10000000 + Math.random() * 90000000);//generating 8 digit number
            document.getElementById('numberDisplay').innerText = `Your election ID is: ${randomNumber}`;
            document.getElementById('message').innerText = `DO SHARE THIS CODE WITH YOUR VOTERS.`;
            document.getElementById('generateButton').disabled = true;
            localStorage.setItem('uniqueNumber', randomNumber.toString());// allows the number to persist even after the user refreshes or navigates away from the page, as localStorage retains data across sessions unless it's manually cleared.
        }

        // Function to show a popup message
        function showMessage(message) {
            alert(message);
        }

        // Check if there is a success message to display
        window.onload = function() {
            const successMessage = document.getElementById('successMessage').innerText;
            if (successMessage) {
                showMessage(successMessage);
                // Redirect to add_post.php if election created successfully
                if (successMessage.includes('successfully')) {
                    window.location.href = 'add_post.php';
                }
            }
        };
    </script>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="navbar-brand">
                <a href="#">E-BALLOT</a>
            </div>
            <div class="navbar-menu">
                <a href="admin_home_page.php">Dashboard</a>
                <a href="?action=logout">Logout</a>
            </div>
        </nav>
    </header>
    <main>
        <div class="generator-container">
            <section id="home" class="box-section">
                <h1>Create New Election</h1>
                <form id="electionForm" action="" method="POST" onsubmit="return handleSubmit()">
                    <label for="electionId">Election ID</label>
                    <input type="text" id="electionId" name="electionId" placeholder="Enter Election ID" required>
                    <label for="electionName">Election Name</label>
                    <input type="text" id="electionName" name="electionName" placeholder="Enter Election Name" required>
                    <label for="startDate">Start Date</label>
                    <input type="date" id="startDate" name="startDate" required>
                    <label for="endDate">End Date</label>
                    <input type="date" id="endDate" name="endDate" required>
                    <button type="submit">SUBMIT</button>
                </form>
            </section>

            <section class="unique-number-box">
                <h2>To Generate A Random Election ID</h2>
                <button onclick="generateNumber()" id="generateButton">Click Here</button>
                <p id="numberDisplay"></p>
                <p id="message"></p>
            </section>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 E-Ballot system. All rights reserved.</p>
    </footer>
    <!-- Hidden element to hold the success message -->
    <div id="successMessage" style="display: none;"><?php echo htmlspecialchars($successMessage); ?></div>
    <script>
        function handleSubmit() {
            // Prevent default form submission
            event.preventDefault();
            
            // Submit the form via AJAX
            const form = document.getElementById('electionForm');
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Handle response data
                const successMessage = data.includes('New election created successfully.') ? 'New election created successfully.' : 'Error: ' + data;
                document.getElementById('successMessage').innerText = successMessage;

                if (successMessage.includes('successfully')) {
                    window.location.href = 'add_post.php';
                } else {
                    showMessage(successMessage);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
