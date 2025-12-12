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

// Fetch candidate details from the database
$sql = "SELECT id, election_id, candidate_name, total_votes FROM candidate_details WHERE candidate_status = 1";
$result = $conn->query($sql);

// Check if the delete button is pressed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_candidates'])) {
    // SQL query to delete all candidates, posts, users, and elections
    $delete_candidates_sql = "DELETE FROM candidate_details";
    $delete_posts_sql = "DELETE FROM post_details"; // SQL query to delete all posts
    $delete_users_sql = "DELETE FROM users"; // SQL query to delete all users
    $delete_elections_sql = "DELETE FROM elections"; // SQL query to delete all elections

    // Execute deletion of candidates, posts, users, and elections
    if ($conn->query($delete_candidates_sql) === TRUE && 
        $conn->query($delete_posts_sql) === TRUE && 
        $conn->query($delete_users_sql) === TRUE && 
        $conn->query($delete_elections_sql) === TRUE) {
        echo "<script>alert('All candidates, posts, users, and elections have been deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting candidates, posts, users, or elections: " . $conn->error . "');</script>";
    }
}
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
            background-image: url('tick.jpg');
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px 20px;
            color: white;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
        }
        .navbar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }
        .delete-button {
            background-color: red;
            border: none;
            border-radius: 5px;
            color: white;
            padding: 10px 15px;
            cursor: pointer;
            text-decoration: none; /* Remove underline */
        }
        .delete-button:hover {
            background-color: darkred;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 1000px;
            border-radius: 15px;
            border: 2px solid rgba(0, 0, 0, 0.3);
            overflow-x: auto;
            margin-top: 60px; /* Space for the fixed navbar */
        }
        h2 {
            text-align: center;
            color: chocolate;
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid chocolate;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: chocolate;
            color: white;
        }
        td {
            background-color: rgba(255, 255, 255, 0.8);
        }
        .no-candidates {
            text-align: center;
            color: red;
            font-size: 18px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <span>E-BALLOT</span>
    <a href="admin_home_page.php">Dashboard</a>
    <form method="post" action="" style="display:inline;">
        <button type="submit" name="delete_candidates" class="delete-button">Delete Election</button>
    </form>
</div>

<div class="container">
    <h2>Election Results</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Candidate ID</th>
                    <th>Election ID</th>
                    <th>Candidate Name</th>
                    <th>Total Votes</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['election_id']; ?></td>
                        <td><?php echo $row['candidate_name']; ?></td>
                        <td><?php echo $row['total_votes']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-candidates">No results available.</p>
    <?php endif; ?>

</div>

<?php
// Close the connection
$conn->close();
?>

</body>
</html>
