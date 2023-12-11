<?php

function generateSidebar() {

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    require "Connection.php";

    if (!isset($_SESSION["user_id"])) {
        die('User not logged in');
    }

    $userId = $_SESSION['user_id'];

    $query = "SELECT role FROM Users WHERE id = :user_id";
    
    $stmt = $conn->prepare($query);
    $stmt->execute(['user_id' => $userId]);

    $errorInfo = $stmt->errorInfo();
    if ($errorInfo[0] !== '00000') {
        die('Error fetching data: ' . print_r($errorInfo, true));
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user === false) {
        die('No user found with the provided ID');
    }

    $userRole = $user["role"];

    $html = '<section id="sideBar">
                <ul>
                    <li><a href="DashBoard.php"><img src="home.png" alt="home symbol" class="icon">Home</a></li>
                    <li><a href="newContact.php"><img src="newContact.png" alt="Symbol for new contact" class="icon">New Contact</a></li>';

    if ($userRole === "Admin") {
        $html .= '<li><a href="view_users.php"><img src="user.png" alt="Symbol for user" class="icon">Users</a></li>';
    }

    $html .= '<hr>
                    <li><a href="Userlogout.php?logout=1"><img src="logout.png" alt="Symbol for logout" class="icon">Logout</a></li>
                </ul>
            </section>';

    echo $html;
}
