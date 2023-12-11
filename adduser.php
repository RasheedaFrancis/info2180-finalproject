<?php
require "Connection.php";

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function validatePassword($password) {
    return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password);
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($_POST["action"] == "add_user") {
        $new_title = sanitizeInput($_POST["new_title"]);
        $new_firstname = sanitizeInput($_POST["new_firstname"]);
        $new_lastname = sanitizeInput($_POST["new_lastname"]);
        $new_email = sanitizeInput($_POST["new_email"]);
        $new_password = sanitizeInput($_POST["new_password"]);
        $new_role = sanitizeInput($_POST["new_role"]);

        if (validatePassword($new_password)) {
            
            $hashed_password = hashPassword($new_password);

            
            $stmt = $conn->prepare("INSERT INTO Users (title, firstname, lastname, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bindParam(1, $new_title);
            $stmt->bindParam(2, $new_firstname);
            $stmt->bindParam(3, $new_lastname);
            $stmt->bindParam(4, $new_email);
            $stmt->bindParam(5, $hashed_password);
            $stmt->bindParam(6, $new_role);

            if ($stmt->execute()) {
                echo "User added successfully";
                
                
                header("Location: DashBoard.php");
                exit();
            } else {
                echo "Error adding user: " . $stmt->errorInfo()[2];
            }

        } else {
            echo "Invalid password format. Password must be at least 8 characters long and contain at least one letter and one number.";
        }
    }
}

unset($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dolphin CRM</title>

    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
   
    <section id="loader">
        <?php include 'sidebar.php'; ?>
        <?php generateSidebar(); ?>
        <div Class="layout">
            <h1>New User</h1>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                 
                <input type="hidden" name="action" value="add_user">
        
                <label for="new_title">Title:</label>
                <select name="new_title" required><br>
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Ms">Ms</option>
                <option value="Dr">Dr</option>
                <option value="Prof">Prof</option>
                </select> <br> <br>

                <label for="new_firstname">First Name:</label>
                <input type="text" name="new_firstname" class="form-control" required>
        
                <label for="new_lastname">Last Name:</label>
                <input type="text" name="new_lastname" class="form-control" required> <br><br>
        
                <label for="new_email">Email:</label>
                <input type="email" name="new_email" class="form-control" required>
        
                <label for="new_password">Password:</label>
                <input type="password" name="new_password" class="form-control" required><br><br>
                <label for="new_type">Type:</label>
                <select name="new_type" class="form-control" required>
                <option value="Sales Lead">Sales Lead</option>
                <option value="Support">Support</option>
            </select>
            <label for="new_role">Role:</label>
                <select name="new_role" required><br>
                <option value="Admin">Admin</option>
                <option value="Member">Member</option>
                </select> <br> <br>

                <input type="submit" value="Add User">
            </form>
        </div>
    </section>
</body>
</html>
