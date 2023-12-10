<?php
session_start();
require "Connection.php";


$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$key = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $key;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    $password = htmlspecialchars($_POST["password"]);

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    try {
        $query = "SELECT * FROM users WHERE email=:email";
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
            $error = "Invalid credentials. Please try again.";
        }
    } catch (PDOException $e) {
        $error = "Database query error: " . $e->getMessage();
        error_log($error, 0); 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dolphin CRM</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="container">
    <main>
      <h1>Login</h1>
      <form action="check.php" method="post">
        <div class="form">
         <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
          <input type="text" name="email" id="email" class="form-control" placeholder="Email address" />
          <br>
          <br>
        </div>
        <div class="form">
          <input type="password" name="password" id="password" class="form-control" placeholder="Password" />
          <br>
          <br>
        </div>
        <div class="form">
          <button type="submit" id="login" class="form-control"><img src="blacklock.png" alt="lock Image" class="button-image">Login</button>
        </div>
      </form>
      <br>
    </main>
  </div>
  <footer>
    <?php include 'footer.php'; ?>
  </footer>
</body>
</html>
