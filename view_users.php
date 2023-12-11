
<?php
require "Connection.php";

include "sidebar.php"; // Assuming this file contains generateSidebar()

try {
    $sql = "SELECT title, firstname, lastname, email, role, created_at FROM users";
    $result = $conn->query($sql);

    $data = array();

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $fullName = $row["title"] . " " . $row["firstname"] . " " . $row["lastname"];
        $data[] = array(
            'fullName' => $fullName,
            'email' => $row["email"],
            'role' => $row["role"],
            'created_at' => $row["created_at"]
        );
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

unset($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Dolphin CRM</title>
    <link rel="stylesheet" href="styles.css">
    <script src="view_users.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
    <section id="loader">
        <?php generateSidebar(); ?>
        <div class="layout">
            <div class="header">
                <h1 id="User">User</h1>
                <button onclick="location.href='addUser.php'" class="button">+ AddUser</button>
            </div>

            <div>
                <br>
                <br>

                <?php if (!empty($data)): ?>
                    <table >
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $row): ?>
                                <tr>
                                    <td><?php echo $row['fullName']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['role']; ?></td>
                                    <td><?php echo $row['created_at']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</body>
</html>
