<?php
// Connect to database (Update these with your actual database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$port='3307';
$dbname = "e_ballot_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname,$port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle accept or reject button clicks
if (isset($_POST['accept'])) {
    $candidate_id = $_POST['candidate_id'];
    $sql = "UPDATE candidate_details SET candidate_status = 1 WHERE id = $candidate_id";
    $conn->query($sql);
} elseif (isset($_POST['reject'])) {
    $candidate_id = $_POST['candidate_id'];
    $sql = "UPDATE candidate_details SET candidate_status = 0 WHERE id = $candidate_id";
    $conn->query($sql);
} elseif (isset($_POST['approve_post'])) {
    $candidate_id = $_POST['candidate_id'];
    $sql = "UPDATE candidate_details SET post_approval_status = 1 WHERE id = $candidate_id";
    $conn->query($sql);
}

// Fetch candidate details from the database
$sql = "SELECT * FROM candidate_details";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Candidates</title>
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
            margin-left: auto;
        }
        .navbar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
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
        .btn {
            padding: 5px 10px;
            background-color: chocolate;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: darkorange;
        }
        @media (max-width: 768px) {
            .container {
                padding: 15px;
                width: 100%;
            }
            table, th, td {
                font-size: 14px;
                padding: 8px;
            }
            h2 {
                font-size: 20px;
            }
            .btn {
                padding: 4px 8px;
                font-size: 12px;
            }
        }
        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }
            th, td {
                font-size: 12px;
                padding: 6px;
            }
            h2 {
                font-size: 18px;
            }
            .btn {
                padding: 3px 6px;
                font-size: 10px;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <span>E-BALLOT</span>
    <a href="admin_home_page.php">Dashboard</a>
</div>

<div class="container">
    <h2>Candidate Details</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Election ID</th>
                    <th>Candidate Name</th>
                    <th>Photo</th>
                    <th>Address</th>
                    <th>Date of Birth</th>
                    <th>Qualification</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>Selected Post</th>
                    <th>Post Status</th>
                    <th>Post Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['election_id']; ?></td>
                        <td><?php echo $row['candidate_name']; ?></td>
                        <td><img src="<?php echo $row['photo']; ?>" alt="Candidate Photo" width="50" height="50"></td>
                        <td><?php echo $row['address']; ?></td>
                        <td><?php echo $row['dob']; ?></td>
                        <td><?php echo $row['qualification']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['candidate_status'] == 1 ? "Accepted" : "Rejected"; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="candidate_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="accept" class="btn">Accept</button>
                                <button type="submit" name="reject" class="btn">Reject</button>
                            </form>
                        </td>
                        <td><?php echo !empty($row['selected_post']) ? $row['selected_post'] : "Not selected yet"; ?></td>
                        <td><?php echo $row['post_approval_status'] == 1 ? "Approved" : "Pending"; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="candidate_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="approve_post" class="btn">Approve</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-candidates">No candidates are available.</p>
    <?php endif; ?>

</div>

<?php
// Close the connection
$conn->close();
?>

</body>
</html>
