<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['candidate_id'])) {
    header("Location: candidate_login.php");
    exit();
}

// Connect to the database (update credentials if necessary)
$servername = "localhost";
$username = "root";
$password = "";
$port="3307";
$dbname = "e_ballot_system";

$conn = new mysqli($servername, $username, $password, $dbname,$port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch post details from the database
$sql = "SELECT * FROM post_details";
$result = $conn->query($sql);

// Handle the form submission for applying to a post
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply'])) {
    $postName = $_POST['postName'];
    $candidateId = $_SESSION['candidate_id'];

    // Update the selected_post in candidate_details for the current candidate
    $updateSql = "UPDATE candidate_details SET selected_post = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $postName, $candidateId);

    if ($stmt->execute()) {
        // Destroy the session and redirect to home.html after applying
        session_destroy();
        header("Location: home.html");
        exit();
    } else {
        echo "<script>alert('Error applying for the post. Please try again.');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Posts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('tick.jpg');
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 900px;
        }
        h2 {
            text-align: center;
            color: chocolate;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
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
        .apply-btn {
            background-color: chocolate;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .apply-btn:hover {
            background-color: darkorange;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Available Posts</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Post ID</th>
                    <th>Post Name</th>
                    <th>Qualification</th>
                    <th>Max Seats</th>
                    <th>Election ID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['postId']; ?></td>
                        <td><?php echo $row['postName']; ?></td>
                        <td><?php echo $row['qualification']; ?></td>
                        <td><?php echo $row['maxSeats']; ?></td>
                        <td><?php echo $row['electionId']; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="postName" value="<?php echo $row['postName']; ?>">
                                <button type="submit" name="apply" class="apply-btn">Apply</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No posts available at the moment.</p>
    <?php endif; ?>
</div>

<?php
// Close the connection
$conn->close();
?>

</body>
</html>
