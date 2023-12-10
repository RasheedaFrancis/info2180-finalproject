<?php
session_start();
require "Connection.php";

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if ($_SERVER["REQUEST_METHOD"] == "POST" && (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
    die("CSRF token validation failed.");
}


$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
$password = htmlspecialchars($_POST["password"]);

try {
    $query = "SELECT * FROM Users WHERE email=:email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
       
        $_SESSION["user_id"] = $user["id"];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        
        header("Location: dashboard.php");
        exit();
    } else {
       
        $error = "Invalid credential";
    }
} catch (PDOException $e) {
    $error = "Database query error: " . $e->getMessage();
    error_log($error, 0);
}


$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

header("Location: userlogin.php");
exit();
?>
