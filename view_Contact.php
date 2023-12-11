<?php
require "Connection.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    die('User not logged in');
}

$currentUserId = $_SESSION["user_id"];

if (isset($_GET['id'])) {
    $contact_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT C.*, U.firstname AS created_firstname, U.lastname AS created_lastname, 
                                  A.firstname AS assigned_firstname, A.lastname AS assigned_lastname
                            FROM Contacts C
                            LEFT JOIN Users U ON C.created_by = U.id
                            LEFT JOIN Users A ON C.assigned_to = A.id
                            WHERE C.id = ?");
    $stmt->bindParam(1, $contact_id);
    $stmt->execute();
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);

    $fetchNotesStmt = $conn->prepare("SELECT N.*, U.firstname AS created_firstname, U.lastname AS created_lastname 
                                      FROM Notes N
                                      LEFT JOIN Users U ON N.created_by = U.id
                                      WHERE N.contact_id = ?");
    $fetchNotesStmt->bindParam(1, $contact_id);
    $fetchNotesStmt->execute();
    $notes = $fetchNotesStmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["assign_to_me"])) {
        $assignStmt = $conn->prepare("UPDATE Contacts SET assigned_to = ?, updated_at = NOW() WHERE id = ?");
        $assignStmt->bindParam(1, $currentUserId);
        $assignStmt->bindParam(2, $contact_id);

        if ($assignStmt->execute()) {
            echo "Assigned to you successfully";
          
            header("Location: view_contact.php?id=" . $contact_id);
            exit;
        } else {
            echo "Error assigning to you: " . $assignStmt->errorInfo()[2];
        }
    } elseif (isset($_POST["switch_type"])) {
       
        $currentType = $contact['type'];
        $newType = ($currentType == 'Sales Lead') ? 'Support' : 'Sales Lead';

        $switchTypeStmt = $conn->prepare("UPDATE Contacts SET type = ?, updated_at = NOW() WHERE id = ?");
        $switchTypeStmt->bindParam(1, $newType);
        $switchTypeStmt->bindParam(2, $contact_id);

        if ($switchTypeStmt->execute()) {
            echo "Type switched successfully";
            
            header("Location: view_contact.php?id=" . $contact_id);
            exit;
        } else {
            echo "Error switching type: " . $switchTypeStmt->errorInfo()[2];
        }
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

<section id="loader">
    <?php include 'sidebar.php'; ?>
    <?php generateSidebar(); ?>
    <div class="layout">
    <?php if (!empty($contact)): ?>
        <div class=newclass>
        <h1><img src="ContactIcon.png" alt="Symbol for new contact" class="icon1"><?php echo $contact['firstname'] . ' ' . $contact['lastname']; ?></h1>

    <form method="post" action="view_Contact.php?id=<?php echo $contact_id; ?>">
    <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>">
    <button type="submit" class='button' name="assign_to_me">Assign to Me</button>
    <button type="submit" class='button'name="switch_type">Switch Type</button>
    </form>
    </div>
        <h5>Created on <?php echo date('F j, Y', strtotime($contact['created_at'])) . ' by ' . $contact['created_firstname'] . ' ' . $contact['created_lastname']; ?></h5>
        <h5>Updated on <?php echo date('F j, Y', strtotime($contact['updated_at'])); ?></h5>
        <div class="newform-container">
                <div class="newform">
                    <h4>Email:<br><?php echo $contact['email'] ?></h4>
                </div>
                <div class="newform">
                    <h4>Telephone:<br><?php echo $contact['telephone'] ?></h4>
                </div>
                <div class="newform">
                    <h4>Company:<br><?php echo $contact['company'] ?></h4>
                </div>
                <div class="newform">
                    <h4>Assigned to:<br><?php echo $contact['assigned_firstname'] . ' ' . $contact['assigned_lastname']; ?></h4>
                </div>
            </div>
    <?php else: ?>
        <h1>Contact Details</h1>
    <?php endif; ?>

    

    <div>
        <h2>Notes</h2>

        <?php if (!empty($notes)): ?>
            <ul>
                <?php foreach ($notes as $note): ?>
                    <li>
                        <p>Name: <?php echo isset($note['created_firstname']) ? $note['created_firstname'] . ' ' . $note['created_lastname'] : 'No name available'; ?></p>
                        <p>Comment: <?php echo isset($note['comment']) ? $note['comment'] : ''; ?></p>
                        <p>Date: <?php echo isset($note['created_at']) ? $note['created_at'] : ''; ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No notes available.</p>
        <?php endif; ?>

        <form method="post" action="notes.php?id=<?php echo $contact_id; ?>">
            <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>">
            <label for="note_comment">Add Note:</label>
            <textarea name="new_note" class="textarea"  required></textarea>
            <input type="hidden" name="action" value="add_note">
            <input type="submit" class =notes value="Add Note">
        </form>
    </div>
    </div>
</section>
</body>
</html>
