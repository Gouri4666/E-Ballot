<?php
session_start();

// Check if the candidate is logged in
if (!isset($_SESSION['candidate_id'])) {
    header("Location: candidate_login.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'e_ballot_system',3307);

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Fetch candidate's election_id based on their ID
$candidateId = $_SESSION['candidate_id'];
$candidate_stmt = $conn->prepare("SELECT election_id FROM candidate_details WHERE id = ?");
$candidate_stmt->bind_param("i", $candidateId);
$candidate_stmt->execute();
$candidate_stmt->bind_result($electionId);
$candidate_stmt->fetch();
$candidate_stmt->close();

// Check election end date and publish result status
$election_stmt = $conn->prepare("SELECT endDate, publish_result FROM elections WHERE electionId = ?");
$election_stmt->bind_param("i", $electionId);
$election_stmt->execute();
$election_stmt->bind_result($endDate, $publishResult);
$election_stmt->fetch();
$election_stmt->close();

$currentDate = date("Y-m-d"); // Get the current date

// Check if the election has ended and results are published
if ($currentDate > $endDate && $publishResult == 1) {
    // Fetch results from candidate_details, including the photo
    $result_stmt = $conn->prepare("SELECT candidate_name, total_votes, photo FROM candidate_details WHERE election_id = ?");
    $result_stmt->bind_param("i", $electionId);
    $result_stmt->execute();
    $result_stmt->bind_result($candidate_name, $totalVotes, $photo);

    $results = [];
    while ($result_stmt->fetch()) {
        $results[] = ['name' => $candidate_name, 'votes' => $totalVotes, 'photo' => $photo];
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
    <title>Candidate Election Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            color: black;
            text-align: center;
        }
        .container {
            margin: 50px auto;
            width: 600px;
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
            width: 60px;
            height: 60px;
            border-radius: 50%;
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
                <th>Photo</th>
                <th>Candidate Name</th>
                <th>Total Votes</th>
            </tr>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($result['photo']); ?>" alt="Candidate Photo"></td>
                    <td><?php echo htmlspecialchars($result['name']); ?></td>
                    <td><?php echo htmlspecialchars($result['votes']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
