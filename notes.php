<?php


session_start();

require "Connection.php";

function sanitizeInput($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}


if (isset($_SESSION["user_id"])) {
    $currentUserId = $_SESSION["user_id"];
} else {
   
    die('User not logged in');
}


function userExistsInDatabase($userId) {
    global $conn; 
    try {
        $query = "SELECT COUNT(*) FROM Users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $userId);
        $stmt->execute();

        $result = $stmt->fetchColumn();

        return $result > 0; 
    } catch (PDOException $e) {
        $error = "Database query error: " . $e->getMessage();
        error_log($error . "\nSQL: " . $query . "\nParams: " . print_r([$userId], true), 0);
        return false; 
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["action"] == "add_note") {
        $contact_id = filter_input(INPUT_POST, 'contact_id', FILTER_SANITIZE_NUMBER_INT);
        $new_note = sanitizeInput($_POST["new_note"]);

        try {
            $stmt = $conn->prepare("INSERT INTO notes (contact_id, comment, created_by, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bindParam(1, $contact_id);
            $stmt->bindParam(2, $new_note);
            $stmt->bindParam(3, $currentUserId);

            if ($stmt->execute()) {
                $updateContactStmt = $conn->prepare("UPDATE Contacts SET updated_at = NOW() WHERE id = ?");
                $updateContactStmt->bindParam(1, $contact_id);
                $updateContactStmt->execute();

                echo "Note added successfully";
                header('Location: view_Contact.php?id=' . $contact_id);
            } else {
                echo "Error adding note: " . $stmt->errorInfo()[2];
            }
        } catch (PDOException $e) {
            $error = "Database query error: " . $e->getMessage();
            error_log($error . "\nSQL: " . $stmt->queryString . "\nParams: " . print_r([$contact_id, $new_note, $currentUserId], true), 0);
        }
    }
}

$contact_id = filter_input(INPUT_GET, 'contact_id', FILTER_SANITIZE_NUMBER_INT);

if (!empty($contact_id)) {
    try {
        
        if (userExistsInDatabase($currentUserId)) {
            $fetchNotesStmt = $conn->prepare("SELECT * FROM Notes WHERE contact_id = ?");
            $fetchNotesStmt->bindParam(1, $contact_id);
            $fetchNotesStmt->execute();
            $existingNotes = $fetchNotesStmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            echo "User does not exist.";
        }
    } catch (PDOException $e) {
        $error = "Database query error: " . $e->getMessage();
        error_log($error . "\nSQL: " . $fetchNotesStmt->queryString . "\nParams: " . print_r([$contact_id], true), 0);
    }
}


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



</body>
</html>
