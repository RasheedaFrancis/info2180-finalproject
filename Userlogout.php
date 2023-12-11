<?php
session_start();
require "Connection.php";


if (isset($_GET['logout']) && ($_GET['logout'] == 1)) {
   
    $_SESSION = array();

    
    session_destroy();

    
    header('Location: UserLogin.php?logout=1');

    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin DRm</title>
    <script src="logout.js"></script>        
</head>
<body>
    
</body>
</html>
