<?php
session_start();

// Check if logged in
if (!isset($_SESSION['email'])) {
    header("Location: admin_login.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'e_ballot_system',3307);

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Fetch user details
$stmt = $conn->prepare("SELECT id, name, email, gender, phone, address FROM users");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Voters</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('tick.jpg'); /* Set your background image path here */
            background-size: cover; /* Cover the entire background */
            background-position: center; /* Center the background */
            color: black;
            margin: 0;
            padding: 0; /* Remove padding to avoid space around the body */
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px 20px; /* Adjust padding for consistency */
            color: white;
            position: fixed; /* Fix the navbar at the top */
            top: 0; /* Align to the top */
            width: 100%; /* Full width */
            z-index: 1000; /* Ensure it stays above other content */
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin-left: auto; /* Pushes the Dashboard link to the right */
        }
        .navbar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }
        .content {
            margin-top: 60px; /* Add margin to prevent content from being hidden behind the navbar */
            padding: 20px; /* Add padding to the content */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent background for the table */
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: chocolate;
            color: white;
        }
        h1 {
            text-align: center; /* Center the heading */
            color: chocolate;
            text-decoration: underline;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <span>E-BALLOT</span>
    <a href="admin_home_page.php">Dashboard</a>
</div>

<div class="content">
    <h1>Voter Details</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Address</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No voters found.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
