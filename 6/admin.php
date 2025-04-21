<?php
session_start();
include("../db.php");

// HTTP Basic Authentication
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
    header('WWW-Authenticate: Basic realm="Admin Area"');
    header('HTTP/1.0 401 Unauthorized');
    die('Unauthorized');
}

try {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE admin_login = ?");
    $stmt->execute([$_SERVER['PHP_AUTH_USER']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin || !password_verify($_SERVER['PHP_AUTH_PW'], $admin['password_hash'])) {
        header('HTTP/1.0 403 Forbidden');
        die('Forbidden');
    }
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM user_applications WHERE application_id = ?");
        $stmt->execute([$_POST['delete_id']]);
        header('Location: admin.php');
        exit();
    } catch (PDOException $e) {
        $error = 'Error deleting user: ' . $e->getMessage();
    }
}

// Fetch all user data
try {
    $stmt = $db->query("SELECT ua.application_id, ua.full_name, ua.phone_number, ua.email_address, ua.birth_date, ua.gender, ua.biography, GROUP_CONCAT(pl.language_name SEPARATOR ', ') AS languages
                        FROM user_applications ua
                        LEFT JOIN application_languages al ON ua.application_id = al.application_id
                        LEFT JOIN programming_languages pl ON al.language_id = pl.language_id
                        GROUP BY ua.application_id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch statistics
    $stmt = $db->query("SELECT pl.language_name, COUNT(al.language_id) AS user_count
                        FROM programming_languages pl
                        LEFT JOIN application_languages al ON pl.language_id = al.language_id
                        GROUP BY pl.language_id");
    $statistics = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-wide">
        <h1 class="text-center my-4">Admin Panel</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <h2>Users</h2>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Birth Date</th>
                    <th>Gender</th>
                    <th>Biography</th>
                    <th>Languages</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['application_id']) ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['phone_number']) ?></td>
                        <td><?= htmlspecialchars($user['email_address']) ?></td>
                        <td><?= htmlspecialchars($user['birth_date']) ?></td>
                        <td><?= $user['gender'] == 0 ? 'Male' : 'Female' ?></td>
                        <td><?= htmlspecialchars($user['biography']) ?></td>
                        <td><?= htmlspecialchars($user['languages']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $user['application_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?= $user['application_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Statistics</h2>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Language</th>
                    <th>User Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($statistics as $stat): ?>
                    <tr>
                        <td><?= htmlspecialchars($stat['language_name']) ?></td>
                        <td><?= htmlspecialchars($stat['user_count']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
