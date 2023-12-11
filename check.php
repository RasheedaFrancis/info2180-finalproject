<?php
session_start();
require "Connection.php";

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST" && (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
    die("CSRF token validation failed.");
}

$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
$password = htmlspecialchars($_POST["password"]);
$role = "Admin";

try {
    $checkQuery = "SELECT id FROM Users WHERE email=:email";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bindParam(":email", $email);
    $checkStmt->execute();

    $existingUser = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $query = "SELECT id, password FROM Users WHERE email=:email";
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
    } else {
       
        $title = "Mr";
        $firstname = "Ken";
        $lastname = "Walker";

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $insertQuery = "INSERT INTO Users (title, firstname, lastname, email, password, role, created_at)
                        VALUES (:title, :firstname, :lastname, :email, :hashedPassword, :role, NOW())";
        $insertStmt = $conn->prepare($insertQuery);

        $insertStmt->bindParam(":title", $title);
        $insertStmt->bindParam(":firstname", $firstname);
        $insertStmt->bindParam(":lastname", $lastname);
        $insertStmt->bindParam(":email", $email);
        $insertStmt->bindParam(":hashedPassword", $hashedPassword);
        $insertStmt->bindParam(":role", $role);
        $insertStmt->execute();

        $_SESSION["user_id"] = $conn->lastInsertId();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header("Location: dashboard.php");
        exit();
    }
} catch (PDOException $e) {
    $error = "Database query error: " . $e->getMessage();
    error_log($error, 0);
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
header("Location: userlogin.php");
exit();
?>
