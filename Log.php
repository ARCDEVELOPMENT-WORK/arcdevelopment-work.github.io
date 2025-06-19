<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

function readLogFile($filename) {
    if (!file_exists($filename)) {
        return [];
    }
    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return array_reverse($lines);
}

$userLogs = readLogFile('log/userlog.txt');
$adminLogs = readLogFile('log/adminlog.txt');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Logs - User and Admin Actions</title>
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        h1 {
            color: #bb86fc;
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 0 0 8px #bb86fc;
        }
        .log-section {
            margin-bottom: 40px;
        }
        h2 {
            color: #bb86fc;
            border-bottom: 2px solid #bb86fc;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        pre {
            background-color: #1f1f1f;
            padding: 15px;
            border-radius: 8px;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            box-shadow: 0 0 15px #bb86fc;
        }
        a {
            color: #bb86fc;
            text-decoration: none;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 20px;
        }
        a:hover {
            color: #e0aaff;
        }
    </style>
</head>
<body>
    <a href="dashboard.php">Back to Dashboard</a>
    <h1>Action Logs</h1>

    <div class="log-section">
        <h2>User Actions</h2>
        <?php if (count($userLogs) > 0): ?>
            <pre><?= htmlspecialchars(implode("\n", $userLogs)) ?></pre>
        <?php else: ?>
            <p>No user actions logged yet.</p>
        <?php endif; ?>
    </div>

    <div class="log-section">
        <h2>Admin Actions</h2>
        <?php if (count($adminLogs) > 0): ?>
            <pre><?= htmlspecialchars(implode("\n", $adminLogs)) ?></pre>
        <?php else: ?>
            <p>No admin actions logged yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
