<?php
session_start();

// Check if electionId is set in the session
if (!isset($_SESSION['electionId'])) {
    header("Location: add_new_election.php");
    exit();
}

$electionId = $_SESSION['electionId'];

// Database connection
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

// Insert new post
if (isset($_POST['add_post'])) {
    $postName = $_POST['postName'];
    $qualification = $_POST['qualification'];
    $maxSeats = $_POST['maxSeats'];

    // Ensure electionId is included in the insert query
    $sql = "INSERT INTO post_details (postName, qualification, maxSeats, electionId) VALUES ('$postName', '$qualification', '$maxSeats', '$electionId')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('New post added successfully');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Delete post
if (isset($_POST['delete_post'])) {
    $postId = $_POST['postId'];

    $sql = "DELETE FROM post_details WHERE postId=$postId";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Post deleted successfully');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch all posts
$sql = "SELECT * FROM post_details WHERE electionId = '$electionId'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .delete-btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            background-color: #f44336;
            color: white;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px;
            color: white;
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

        footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #333;
            color: white;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="admin_home_page.php">Dashboard</a>
    <span>E-BALLOT</span>
</div>

<div class="container">
    <h2>Add New Post</h2>
    <form method="POST" action="">
        <label for="postName">Post Name:</label>
        <input type="text" id="postName" name="postName" required>

        <label for="qualification">Qualification:</label>
        <input type="text" id="qualification" name="qualification" required>

        <label for="maxSeats">Maximum Seats:</label>
        <input type="number" id="maxSeats" name="maxSeats" required>

        <button type="submit" name="add_post">Add Post</button>
    </form>

    <h2>Manage Posts</h2>
    <table>
        <thead>
            <tr>
                <th>Post Name</th>
                <th>Qualification</th>
                <th>Max Seats</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['postName']); ?></td>
                <td><?php echo htmlspecialchars($row['qualification']); ?></td>
                <td><?php echo htmlspecialchars($row['maxSeats']); ?></td>
                <td>
                    <form method="POST" action="" style="display:inline-block;">
                        <input type="hidden" name="postId" value="<?php echo htmlspecialchars($row['postId']); ?>">
                        <button type="submit" name="delete_post" class="delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No posts found</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<footer>
    <p>&copy; 2024 E-VOTE. All rights reserved.</p>
</footer>

</body>
</html>

<?php
$conn->close();
?>
