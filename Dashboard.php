<?php

session_start();

require "Connection.php";

function fetchContacts()
{
    global $conn;

    $query = "SELECT id, title, firstname, email, company, type FROM Contacts";
    $stmt = $conn->query($query);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function filterContacts($filterType, $currentUserId)
{
    global $conn;

    switch ($filterType) {
        case 'all':
            $query = "SELECT id, title, firstname, email, company, type FROM Contacts";
            break;
        case 'saleLeads':
            $query = "SELECT id, title, firstname, email, company, type FROM Contacts WHERE type = 'Sales Lead'";
            break;
        case 'support':
            $query = "SELECT id, title, firstname, email, company, type FROM Contacts WHERE type = 'Support'";
            break;
        case 'assignedToMe':
            $query = "SELECT id, title, firstname, email, company, type FROM Contacts WHERE assigned_to = ?";
            break;
        default:
            $query = "SELECT id, title, firstname, email, company, type FROM Contacts";
            break;
    }

    $stmt = $conn->prepare($query);

   
    if ($filterType == 'assignedToMe') {
        $stmt->bindParam(1, $currentUserId, PDO::PARAM_INT);
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';


$currentUserId = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

$contacts = ($filter == 'all') ? fetchContacts() : filterContacts($filter, $currentUserId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Dolphin CRM</title>
    <link rel="stylesheet" href="styles.css">
    <script src="app.js" defer></script>
    
      
       
    
</head>
<body>
    <section id="loader">
        <?php include 'sidebar.php'; ?>
        <?php generateSidebar(); ?>
        <div class="layout">
            <div class="header">
                <h1 id="dashboard">Dashboard</h1>
                <button onclick="location.href='newContact.php'" class="button" style="float: right;">+ Add New Contact</button>
            </div>
            <div class="tab">
                <h3><img src="filter.png" id="filter" alt="Filter Icon"> Filter By:</h3>
                <a href="#" id="filterOptionAll" class="links" onclick="filterContacts('all')">All </a>
                <a href="#" id="filterOptionSaleLeads" class="links" onclick="filterContacts('saleLeads')">Sales Leads</a>
                <a href="#" id="filterOptionSupport" class="links" onclick="filterContacts('support')">Support</a>
                <a href="#" id="filterOptionAssignedToMe" class="links" onclick="filterContacts('assignedToMe')">Assigned to me</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>First Name</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($contacts as $contact) {
                        echo '<tr>';
                        echo '<td>' . $contact['title'] . '</td>';
                        echo '<td>' . $contact['firstname'] . '</td>';
                        echo '<td>' . $contact['email'] . '</td>';
                        echo '<td>' . $contact['company'] . '</td>';
                        $typeClass = ($contact['type'] == 'Sales Lead') ? 'type-sales-lead' : (($contact['type'] == 'Support') ? 'type-support' : '');
                        echo '<td class="' . $typeClass . '">' . $contact['type'] . '</td>';
                        echo '<td><a href="view_Contact.php?id=' . $contact['id'] . '" class="view-link">View </a></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>
