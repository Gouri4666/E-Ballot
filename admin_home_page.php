<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['email'])) {
    header("Location: admin_login.php"); // Redirect to login page if not logged in
    exit();
}
//logout action
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();//Removes all session variables
    session_destroy();
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: white;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color:#333;
            padding: 10px 20px;
        }
        h1 {
            color: black;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 10px 15px;
        }
        .navbar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }
        .navbar .menu {
            display: flex;
            gap: 10px;
        }
        .hero-content {
            text-align: center;
            margin-top: 20%;
        }
        .hero-content h1 {
            font-size: 50px;
            margin: 0;
        }
        .hero-content p {
            font-size: 20px;
            margin: 10px 0 0;
        }
        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }
        .menu-toggle div {
            width: 25px;
            height: 3px;
            background-color: white;
            margin: 4px 0;
        }
        @media (max-width: 768px) {
            .navbar .menu {
                display: none;
                flex-direction: column;
                width: 100%;
            }
            .navbar .menu.active {
                display: flex;
            }
            .menu-toggle {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="brand">E-BALLOT</div>
        <div class="menu-toggle" id="menu-toggle">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <div class="menu" id="menu">
            <a href="add_new_election.php">ADD NEW ELECTION</a>
            <a href="view_voters.php">VIEW VOTERS</a>
            <a href="view_candidates.php">VIEW CANDIDATES</a>
            <a href="view_result.php">VIEW RESULTS</a>
            <a href="?action=logout" id="logout">LOGOUT</a>
        </div>
    </div>
    <div class="hero-content">
        <h1>Admin Dashboard</h1>
    </div>
    <script>
        const menuToggle = document.getElementById('menu-toggle');//oggle functionality for the responsive navigation menu.
        const menu = document.getElementById('menu');

        menuToggle.addEventListener('click', () => {
            menu.classList.toggle('active');
        });
    </script>
</body>
</html>
