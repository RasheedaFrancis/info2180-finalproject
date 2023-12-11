<?php
require "Connection.php";

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($_POST["action"] == "add_contact") {
        $new_title = sanitizeInput($_POST["new_title"]);
        $new_firstname = sanitizeInput($_POST["new_firstname"]);
        $new_lastname = sanitizeInput($_POST["new_lastname"]);
        $new_email = sanitizeInput($_POST["new_email"]);
        $new_telephone = sanitizeInput($_POST["new_telephone"]);
        $new_company = sanitizeInput($_POST["new_company"]);
        $new_type = sanitizeInput($_POST["new_type"]);
        $assigned_to = sanitizeInput($_POST["assigned_to"]); 
        $created_by = 1; 

        if (empty($new_title) || empty($new_firstname) || empty($new_lastname) || empty($new_email) || empty($new_telephone) || empty($new_company) || empty($new_type) || empty($assigned_to)) {
            echo "Please fill in all fields.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by, created_at, updated_at)
                VALUES (:title, :firstname, :lastname, :email, :telephone, :company, :type, :assigned_to, :created_by, NOW(), NOW())");

            $stmt->bindParam(':title', $new_title);
            $stmt->bindParam(':firstname', $new_firstname);
            $stmt->bindParam(':lastname', $new_lastname);
            $stmt->bindParam(':email', $new_email);
            $stmt->bindParam(':telephone', $new_telephone);
            $stmt->bindParam(':company', $new_company);
            $stmt->bindParam(':type', $new_type);
            $stmt->bindParam(':assigned_to', $assigned_to);
            $stmt->bindParam(':created_by', $created_by);

            if ($stmt->execute()) {
                echo "Contact added successfully";
            } else {
                echo "Error adding contact: " . $stmt->errorInfo()[2];
            }
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
    <script src="ajax.js"></script>
    <script src="app.js"></script>          
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
   
    <section id="loader">
        <?php include 'sidebar.php'; ?>
        <?php generateSidebar(); ?>
        <div class="layout">
        <h1>New Contact</h1>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> ">
            <input type="hidden" name="action" value="add_contact">
            
            <label for="new_title">Title:</label>
            <select name="new_title" required> <br>
                <option value="Mr">Mr</option>
                <option value="Mrs">Mrs</option>
                <option value="Ms">Ms</option>
                <option value="Dr">Dr</option>
                <option value="Prof">Prof</option>
            </select><br>
            
            <label for="new_firstname">First Name:</label>
            <input type="text" name="new_firstname" class="form-control" required><br>

            <label for="new_lastname">Last Name:</label>
            <input type="text" name="new_lastname" class="form-control" required><br>

            <label for="new_email">Email:</label>
            <input type="email" name="new_email" class="form-control" required><br>

            <label for="new_telephone">Telephone:</label>
            <input type="tel" name="new_telephone" class="form-control" required><br>

            <label for="new_company">Company:</label>
            <input type="text" name="new_company" class="form-control" required><br>

            <label for="new_type">Type:</label>
            <select name="new_type" class="form-control" required>
                <option value="Sales Lead">Sales Lead</option>
                <option value="Support">Support</option>
            </select><br>

            <label for="assigned_to">Assigned To:</label>
            <input type="text" name="assigned_to" class="form-control" required><br>

            <input type="submit" value="Add Contact" >

        </form>
        </div>
           
        
            
    </section>
</body>
</html>
