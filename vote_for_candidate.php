<?php
session_start();

// Check if the user is logged in and the electionId is set
if (!isset($_SESSION['email']) || !isset($_SESSION['electionId'])) {
    header("Location: user_login.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'e_ballot_system',3307);

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Fetch electionId from session
$electionId = $_SESSION['electionId'];
$userEmail = $_SESSION['email'];

// Check if the user has already voted
$userQuery = $conn->prepare("SELECT has_voted FROM users WHERE email = ?");
$userQuery->bind_param("s", $userEmail);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userRow = $userResult->fetch_assoc();
$hasVoted = $userRow['has_voted'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vote']) && !$hasVoted) {
    foreach ($_POST['vote'] as $post => $candidateId) {
        // Increment the total_votes in candidate_details
        $incrementVotesQuery = $conn->prepare("UPDATE candidate_details SET total_votes = total_votes + 1 WHERE id = ?");
        $incrementVotesQuery->bind_param("i", $candidateId);
        $incrementVotesQuery->execute();
        $incrementVotesQuery->close();
    }

    // Update the user's has_voted status
    $updateVoteStatus = $conn->prepare("UPDATE users SET has_voted = 1 WHERE email = ?");
    $updateVoteStatus->bind_param("s", $userEmail);
    $updateVoteStatus->execute();
    $updateVoteStatus->close();

    header("Location: thank_you.php"); // Redirect after submitting the vote
    exit();
}

// Fetch unique posts and corresponding candidates
$postsQuery = $conn->prepare("SELECT DISTINCT selected_post FROM candidate_details WHERE election_id = ? AND post_approval_status = 1");
$postsQuery->bind_param("i", $electionId);
$postsQuery->execute();
$postsResult = $postsQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote for Candidates</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            color: black;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: red;
            color: white;
        }
        img {
            width: 100px; /* Set a fixed width for candidate photos */
            height: auto;
        }
        h1 {
            text-align: center;
            color: red;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
        .no-candidates {
            text-align: center;
            font-size: 18px;
            color: red;
            margin-top: 20px;
        }
        .vote-button {
            margin-top: 20px;
            text-align: center;
        }
        .vote-button button {
            background-color: red;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .vote-button button:hover {
            background-color: darkred;
            transform: scale(1.05);
        }
        .vote-button button:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>

<h1>Vote for Your Candidates</h1>

<?php if ($hasVoted): ?>
    <p style="color: red; text-align: center;">You have already cast your vote. Thank you for participating!</p>
<?php else: ?>
    <form action="" method="POST"> <!-- Form to submit votes -->
        <?php if ($postsResult->num_rows > 0): ?>
            <?php while ($postRow = $postsResult->fetch_assoc()): ?>
                <h2><?php echo htmlspecialchars($postRow['selected_post']); ?></h2>
                <?php
                // Fetch candidates for the current post
                $post = $postRow['selected_post'];
                $candidatesQuery = $conn->prepare("SELECT id, candidate_name, photo, qualification FROM candidate_details WHERE election_id = ? AND selected_post = ? AND post_approval_status = 1");
                $candidatesQuery->bind_param("is", $electionId, $post);
                $candidatesQuery->execute();
                $candidatesResult = $candidatesQuery->get_result();
                ?>

                <?php if ($candidatesResult->num_rows > 0): ?>
                    <table>
                        <tr>
                            <th>Select</th>
                            <th>Candidate Name</th>
                            <th>Photo</th>
                            <th>Qualification</th>
                        </tr>
                        <?php while ($candidateRow = $candidatesResult->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <input type="radio" name="vote[<?php echo htmlspecialchars($post); ?>]" value="<?php echo htmlspecialchars($candidateRow['id']); ?>" required>
                                </td>
                                <td><?php echo htmlspecialchars($candidateRow['candidate_name']); ?></td>
                                <td><img src="<?php echo htmlspecialchars($candidateRow['photo']); ?>" alt="Candidate Photo"></td>
                                <td><?php echo htmlspecialchars($candidateRow['qualification']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <div class="no-candidates">No candidates are available for this post.</div>
                <?php endif; ?>

                <?php
                $candidatesQuery->close();
                ?>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-candidates">No posts are available.</div>
        <?php endif; ?>

        <div class="vote-button">
            <button type="submit">Submit</button> <!-- Submit button for the form -->
        </div>
    </form>
<?php endif; ?>

</body>
</html>

<?php
$postsQuery->close();
$conn->close();
?>
