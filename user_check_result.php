<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: user_login.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'e_ballot_system',3307);

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Fetch user electionId from session
$electionId = $_SESSION['electionId'];

// Check election end date
$election_stmt = $conn->prepare("SELECT endDate, publish_result FROM elections WHERE electionId = ?");
$election_stmt->bind_param("i", $electionId);
$election_stmt->execute();
$election_stmt->bind_result($endDate, $publishResult);
$election_stmt->fetch();
$election_stmt->close();

$currentDate = date("Y-m-d"); // Get the current date

// Check if the election has ended
if ($currentDate > $endDate) {
    // Update publish_result to 1 if it's not already published
    if ($publishResult == 0) {
        $update_stmt = $conn->prepare("UPDATE elections SET publish_result = 1 WHERE electionId = ?");
        $update_stmt->bind_param("i", $electionId);
        $update_stmt->execute();
        $update_stmt->close();
    }

    // Fetch results from candidate_details, including the photo
    $result_stmt = $conn->prepare("SELECT candidate_name, total_votes, photo FROM candidate_details WHERE election_id = ?");
    $result_stmt->bind_param("i", $electionId);
    $result_stmt->execute();
    $result_stmt->bind_result($candidateName, $totalVotes, $photo);

    $results = [];
    while ($result_stmt->fetch()) {
        $results[] = [
            'name' => $candidateName,
            'votes' => $totalVotes,
            'photo' => $photo // Store the candidate's photo
        ];
    }
    $result_stmt->close();
} else {
    echo "<p>The election results are not available yet.</p>";
    exit();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            color: black;
            text-align: center;
        }
        .container {
            margin: 50px auto;
            width: 450px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        h1 {
            color: red;
            font-family: cursive;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: red;
            color: white;
        }
        img {
            width: 50px; /* Set a suitable size for the photo */
            height: 50px;
            border-radius: 50%; /* Optional: makes the image circular */
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Election Results</h1>
    <?php if (empty($results)): ?>
        <p>No votes have been cast.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Candidate Name</th>
                <th>Photo</th> <!-- New column for photo -->
                <th>Total Votes</th>
            </tr>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><?php echo htmlspecialchars($result['name']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($result['photo']); ?>" alt="Candidate Photo"></td> <!-- Display candidate photo -->
                    <td><?php echo htmlspecialchars($result['votes']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
