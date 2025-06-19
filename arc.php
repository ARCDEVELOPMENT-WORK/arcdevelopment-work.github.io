<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$scriptDir = 'scripts';
$arcFile = $scriptDir . '/ARC.lua';
$message = '';

// Save ARC.lua
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_arc'])) {
        $content = $_POST['arc_content'] ?? '';
        file_put_contents($arcFile, $content);
        $message = "ARC.lua file saved successfully.";
    } elseif (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
        $uploadName = basename($_FILES['upload_file']['name']);
        $targetPath = $scriptDir . '/' . $uploadName;
        if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $targetPath)) {
            $message = "File uploaded successfully: " . htmlspecialchars($uploadName);
        } else {
            $message = "Failed to upload file.";
        }
    } elseif (isset($_POST['save_file'])) {
        $filename = basename($_POST['file_name']);
        $filepath = $scriptDir . '/' . $filename;
        if (file_exists($filepath)) {
            file_put_contents($filepath, $_POST['file_content'] ?? '');
            $message = "$filename saved successfully.";
        }
    } elseif (isset($_POST['delete_file'])) {
        $filename = basename($_POST['file_name']);
        $filepath = $scriptDir . '/' . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
            $message = "$filename deleted successfully.";
        }
    }
}

// Read ARC.lua content
$arcContent = file_exists($arcFile) ? file_get_contents($arcFile) : '';

// List all Lua files in scriptDir
$luaFiles = array_filter(glob($scriptDir . '/*.lua'), 'is_file');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Script Editor</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #00bcd4;
            text-shadow: 0 0 8px #00bcd4;
            text-align: center;
        }
        form, .file-block {
            background-color: #002f36;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 15px #00bcd4;
            max-width: 800px;
            margin: 20px auto;
        }
        input[type="file"], input[type="submit"], button {
            color: #004d52;
            background-color: #00bcd4;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            margin-top: 10px;
            box-shadow: 0 0 12px #00bcd4;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover, button:hover {
            background-color: #26c6da;
            color: #002f36;
        }
        textarea {
            width: 100%;
            background-color: #004d52;
            color: #e0f7fa;
            border: none;
            border-radius: 8px;
            padding: 15px;
            font-family: monospace;
            font-size: 1rem;
            resize: vertical;
            box-shadow: inset 0 0 10px #001f22;
            margin-top: 10px;
        }
        .message {
            text-align: center;
            color: #00bcd4;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 0 6px #00bcd4;
        }
        a.back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #00bcd4;
            text-decoration: none;
            font-weight: 700;
        }
        a.back-link:hover {
            color: #26c6da;
        }
        .file-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .file-content {
            display: none;
            margin-top: 10px;
        }
        .file-content.visible {
            display: block;
        }
    </style>
    <script>
        function toggleEdit(id) {
            const el = document.getElementById(id);
            el.classList.toggle('visible');
        }
    </script>
</head>
<body>
    <h1>Script Editor - ARC.lua</h1>
    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Upload Form -->
    <form method="POST" enctype="multipart/form-data">
        <label for="upload_file">Upload a file to scripts folder:</label><br />
        <input type="file" name="upload_file" id="upload_file" required />
        <input type="submit" value="Upload File" />
    </form>

    <!-- ARC.lua Edit -->
    <form method="POST">
        <label for="arc_content">Edit ARC.lua content:</label><br />
        <textarea name="arc_content" id="arc_content" rows="15"><?= htmlspecialchars($arcContent) ?></textarea>
        <input type="submit" name="save_arc" value="Save ARC.lua" />
    </form>

    <!-- Other Lua Files -->
    <?php foreach ($luaFiles as $index => $filePath): 
        $fileName = basename($filePath);
        $fileId = "file_$index";
        $fileContent = htmlspecialchars(file_get_contents($filePath));
    ?>
        <div class="file-block">
            <div class="file-title">
                <strong><?= $fileName ?></strong>
                <div>
                    <button type="button" onclick="toggleEdit('<?= $fileId ?>')">Edit</button>
                </div>
            </div>
            <div class="file-content" id="<?= $fileId ?>">
                <form method="POST">
                    <input type="hidden" name="file_name" value="<?= htmlspecialchars($fileName) ?>" />
                    <textarea name="file_content" rows="12"><?= $fileContent ?></textarea>
                    <input type="submit" name="save_file" value="Save" />
                    <input type="submit" name="delete_file" value="Delete" onclick="return confirm('Are you sure you want to delete this file?')" />
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <a href="dashboard_new.php" class="back-link">Back to Dashboard</a>
</body>
</html>
