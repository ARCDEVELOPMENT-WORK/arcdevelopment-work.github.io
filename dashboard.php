<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$UserInfoFile = 'UserInfoo.json';

function devil99encode($Str){
    $Base = base64_encode($Str);
    $Text = "";
    for($x=0; $x < strlen($Base); $x++){
        $Text .= dechex(ord($Base[$x]) + 40);
    }
    return strtolower($Text);
}

function devil99decode($Str){
    $Count = 1;
    $Base = "";
    $len = strlen($Str);
    for($x=0; $x < intval($len/2); $x++){
        if ($Count - 1 < $len && $Count < $len) {
            $hexPair = $Str[$Count - 1].$Str[$Count];
            if (ctype_xdigit($hexPair)) {
                $Base .= chr(hexdec($hexPair) - 40);
            }
        }
        $Count += 2;
    }
    return base64_decode($Base);
}

function loadUsers() {
    global $UserInfoFile;
    if (!file_exists($UserInfoFile)) {
        file_put_contents($UserInfoFile, json_encode([]));
    }
    $content = json_decode(file_get_contents($UserInfoFile), true);
    if (!$content) {
        $content = [];
    }
    return $content;
}

$users = loadUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $is_admin = isset($_POST['is_admin']) ? true : false;
    $valid_days = intval($_POST['valid_days'] ?? 0);
    $expiry = trim($_POST['expiry'] ?? '');
    $device_count = intval($_POST['device_count'] ?? 0);
    $extend_days = intval($_POST['extend_days'] ?? 0);
    $extend_date = trim($_POST['extend_date'] ?? '');
    $ban_message = trim($_POST['ban_message'] ?? '');
    $active = isset($_POST['active']) ? 'true' : 'false';

    $adminUser = $_SESSION['admin_username'] ?? 'admin';

    function logAdminAction($adminUser, $actionDesc) {
        $logFile = __DIR__ . '/log/adminlog.txt';
        $time = date('Y-m-d H:i:s');
        $logEntry = "[$time] Admin: $adminUser - $actionDesc\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    if ($action === 'register') {
        if ($username === '' || $password === '') {
            $message = "Username and password cannot be empty.";
        } else {
            $encodedUsername = devil99encode(strtolower($username));
            if (isset($users[$encodedUsername])) {
                $message = "User already exists.";
            } else {
                $expireDate = '';
                if ($is_admin) {
                    $expireDate = devil99encode('2099-12-31');
                } else {
                    if ($expiry !== '') {
                        $expireDate = devil99encode($expiry);
                    } elseif ($valid_days > 0) {
                        $expireDate = devil99encode(date('Y-m-d', strtotime("+$valid_days days")));
                    } else {
                        $expireDate = devil99encode(date('Y-m-d', strtotime("+30 days")));
                    }
                }
                $users[$encodedUsername] = [
                    'password' => devil99encode($password),
                    'ExpireData' => $expireDate,
                    'Actived' => devil99encode($active),
                    'LastLogin' => devil99encode('null'),
                    'Owner' => devil99encode('admin'),
                    'Created' => devil99encode(date('Y-m-d')),
                    'Level' => $is_admin ? 1 : 0,
                    'IsBanned' => devil99encode('false'),
                    'BanStart' => devil99encode('null'),
                    'BanDuration' => devil99encode('0'),
                    'BanMessage' => devil99encode(''),
                    'DeviceCount' => devil99encode(strval($device_count > 0 ? $device_count : 1))
                ];
                file_put_contents($UserInfoFile, json_encode($users, JSON_PRETTY_PRINT));
                $message = "User registered successfully.";
                logAdminAction($adminUser, "Registered user: $username, Admin: " . ($is_admin ? 'Yes' : 'No') . ", Expiry: " . ($expiry ?: $valid_days . " days"));
            }
        }
    } elseif ($action === 'update_active' && $username !== '') {
        $encodedUsername = devil99encode(strtolower($username));
        if (isset($users[$encodedUsername])) {
            $users[$encodedUsername]['Actived'] = devil99encode($active);
            file_put_contents($UserInfoFile, json_encode($users, JSON_PRETTY_PRINT));
            $message = "User active status updated successfully.";
            logAdminAction($adminUser, "Updated active status for user $username to $active");
        } else {
            $message = "User not found.";
        }
    } elseif ($action === 'extend_validity_date' && $username !== '' && $extend_date !== '') {
        $encodedUsername = devil99encode(strtolower($username));
        if (isset($users[$encodedUsername])) {
            $users[$encodedUsername]['ExpireData'] = devil99encode($extend_date);
            file_put_contents($UserInfoFile, json_encode($users, JSON_PRETTY_PRINT));
            $message = "Expiry date updated successfully.";
            logAdminAction($adminUser, "Updated expiry date for user $username to $extend_date");
        } else {
            $message = "User not found.";
        }
    } elseif ($action === 'extend_validity' && $username !== '' && $extend_days > 0) {
        $encodedUsername = devil99encode(strtolower($username));
        if (isset($users[$encodedUsername])) {
            $currentExpiry = devil99decode($users[$encodedUsername]['ExpireData'] ?? '');
            $currentDate = DateTime::createFromFormat('Y-m-d', $currentExpiry);
            if (!$currentDate) {
                $currentDate = new DateTime();
            }
            $currentDate->modify("+$extend_days days");
            $newExpiry = $currentDate->format('Y-m-d');
            $users[$encodedUsername]['ExpireData'] = devil99encode($newExpiry);
            file_put_contents($UserInfoFile, json_encode($users, JSON_PRETTY_PRINT));
            $message = "Validity extended by $extend_days days.";
            logAdminAction($adminUser, "Extended validity for user $username by $extend_days days");
        } else {
            $message = "User not found.";
        }
    } elseif ($action === 'delete' && $username !== '') {
        $encodedUsername = devil99encode(strtolower($username));
        if (isset($users[$encodedUsername])) {
            unset($users[$encodedUsername]);
            file_put_contents($UserInfoFile, json_encode($users, JSON_PRETTY_PRINT));
            $message = "User deleted successfully.";
            logAdminAction($adminUser, "Deleted user $username");
        } else {
            $message = "User not found.";
        }
    } elseif ($action === 'renew' && $username !== '' && $valid_days > 0) {
        $encodedUsername = devil99encode(strtolower($username));
        if (isset($users[$encodedUsername])) {
            $newExpiry = date('Y-m-d', strtotime("+$valid_days days"));
            $users[$encodedUsername]['ExpireData'] = devil99encode($newExpiry);
            file_put_contents($UserInfoFile, json_encode($users, JSON_PRETTY_PRINT));
            $message = "User renewed for $valid_days days.";
            logAdminAction($adminUser, "Renewed user $username for $valid_days days");
        } else {
            $message = "User not found.";
        }
    } elseif ($action === 'ban' && $username !== '') {
        $encodedUsername = devil99encode(strtolower($username));
        if (isset($users[$encodedUsername])) {
            $users[$encodedUsername]['IsBanned'] = devil99encode('true');
            $users[$encodedUsername]['BanMessage'] = devil99encode($ban_message);
            $users[$encodedUsername]['BanStart'] = devil99encode(date('Y-m-d'));
            file_put_contents($UserInfoFile, json_encode($users, JSON_PRETTY_PRINT));
            $message = "User banned successfully.";
            logAdminAction($adminUser, "Banned user $username with message: $ban_message");
        } else {
            $message = "User not found.";
        }
    } elseif ($action === 'unban' && $username !== '') {
        $encodedUsername = devil99encode(strtolower($username));
        if (isset($users[$encodedUsername])) {
            $users[$encodedUsername]['IsBanned'] = devil99encode('false');
            $users[$encodedUsername]['BanMessage'] = devil99encode('');
            $users[$encodedUsername]['BanStart'] = devil99encode('null');
            file_put_contents($UserInfoFile, json_encode($users, JSON_PRETTY_PRINT));
            $message = "User unbanned successfully.";
            logAdminAction($adminUser, "Unbanned user $username");
        } else {
            $message = "User not found.";
        }
    } elseif ($action === 'increase_devices' && $username !== '' && $device_count > 0) {
        $encodedUsername = devil99encode(strtolower($username));
        if (isset($users[$encodedUsername])) {
            $users[$encodedUsername]['DeviceCount'] = devil99encode(strval($device_count));
            file_put_contents($UserInfoFile, json_encode($users, JSON_PRETTY_PRINT));
            $message = "Device count updated successfully.";
            logAdminAction($adminUser, "Updated device count for user $username to $device_count");
        } else {
            $message = "User not found.";
        }
    }
    $users = loadUsers();
}

function daysLeft($expiryDate) {
    $today = new DateTime();
    // Try to parse expiry date in Y-m-d format
    $expiry = DateTime::createFromFormat('Y-m-d', $expiryDate);
    if (!$expiry) {
        // Try alternative format d-m-Y
        $expiry = DateTime::createFromFormat('d-m-Y', $expiryDate);
    }
    if (!$expiry) return '';
    $interval = $today->diff($expiry);
    if ($interval->invert === 1) {
        return 'Expired';
    }
    return $interval->days . ' days left';
}

?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #121212;
        color: #e0e0e0;
        margin: 20px;
    }
    a.logout {
        float: right;
        color: #ff4081;
        text-decoration: none;
        font-weight: bold;
        margin-bottom: 10px;
    }
    h1 {
        color: #ff4081;
        margin-bottom: 20px;
    }
    form#registerForm {
        margin-bottom: 40px;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
    }
    form#registerForm label {
        display: flex;
        flex-direction: column;
        font-weight: normal;
        margin-bottom: 0;
    }
    form#registerForm input[type="text"],
    form#registerForm input[type="password"],
    form#registerForm input[type="number"],
    form#registerForm input[type="date"] {
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #444;
        background-color: #222;
        color: #e0e0e0;
        width: 200px;
        font-size: 14px;
    }
    form#registerForm input[type="checkbox"] {
        transform: scale(1.2);
        margin-top: 8px;
        align-self: flex-start;
    }
    form#registerForm input[type="submit"] {
        background-color: #ff4081;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease;
        flex: 1 1 100px;
        max-width: 150px;
    }
    form#registerForm input[type="submit"]:hover {
        background-color: #e040fb;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #1e1e1e;
        font-size: 14px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 15px #000;
    }
    table th, table td {
        border: 1px solid #333;
        padding: 10px 12px;
        text-align: left;
        vertical-align: middle;
    }
    table th {
        background-color: #333;
        color: #ff4081;
    }
    table tr:nth-child(even) {
        background-color: #2a2a2a;
    }
    table tr:hover {
        background-color: #3a3a3a;
    }
    button, input[type="submit"] {
        background-color: #ff4081;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }
    button:hover, input[type="submit"]:hover {
        background-color: #e040fb;
    }
    input[type="number"], input[type="date"], input[type="text"], input[type="password"] {
        background-color: #222;
        color: #e0e0e0;
        border: 1px solid #444;
        border-radius: 4px;
        padding: 5px;
        font-size: 14px;
    }
    .message {
        color: #ff4081;
        font-weight: bold;
        margin-bottom: 10px;
    }
</style>

<a class="logout" href="logout.php">Logout</a>
<h1>Dashboard</h1>

<?php if (isset($message)): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form id="registerForm" method="POST" action="">
    <input type="hidden" name="action" value="register" />
    <label>Username: <input type="text" name="username" required /></label><br/>
    <label>Password: <input type="password" name="password" required /></label><br/>
    <label>Is Admin: <input type="checkbox" name="is_admin" /></label><br/>
    <label>Valid Days: <input type="number" name="valid_days" min="0" /></label><br/>
    <label>Expiry Date: <input type="date" name="expiry" /></label><br/>
    <label>Device Count: <input type="number" name="device_count" min="1" value="1" /></label><br/>
    <label>Active: <input type="checkbox" name="active" checked /></label><br/>
    <input type="submit" value="Register User" />
</form>

<hr/>

<table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>Username</th>
            <th>Expiry Date</th>
            <th>Raw Expiry</th>
            <th>Days Left</th>
            <th>Active</th>
            <th>Last Login</th>
            <th>Is Admin</th>
            <th>Is Banned</th>
            <th>Ban Message</th>
            <th>Devices</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $encUser => $data): 
            $username = devil99decode($encUser);
            $expiry = devil99decode($data['ExpireData'] ?? '');
            $active = devil99decode($data['Actived'] ?? '');
            $lastLogin = devil99decode($data['LastLogin'] ?? '');
            $isAdmin = (isset($data['Level']) && $data['Level'] == 1);
            $isAdminText = $isAdmin ? 'Yes' : 'No';
            $isBanned = devil99decode($data['IsBanned'] ?? 'false');
            $banMessage = devil99decode($data['BanMessage'] ?? '');
            $deviceCount = devil99decode($data['DeviceCount'] ?? '1');
            $expiryDisplay = $isAdmin ? 'Lifetime' : htmlspecialchars($expiry);
            $daysLeft = daysLeft($expiry);
        ?>
        <tr<?= $isBanned === 'true' ? ' style="background-color:#7f0000; color:#fff;"' : '' ?>>
            <td><?= htmlspecialchars($username) ?></td>
            <td><?= $expiryDisplay ?></td>
            <td><?= htmlspecialchars($expiry) ?></td>
            <td><?= htmlspecialchars($daysLeft) ?></td>
            <td><?= htmlspecialchars($active) ?></td>
            <td><?= htmlspecialchars($lastLogin) ?></td>
            <td><?= htmlspecialchars($isAdminText) ?></td>
            <td><?= htmlspecialchars($isBanned === 'true' ? 'Yes' : 'No') ?></td>
            <td><?= htmlspecialchars($banMessage) ?></td>
            <td><?= htmlspecialchars($deviceCount) ?></td>
            <td>
                <button type="button" onclick="toggleActions('actions-<?= htmlspecialchars($username) ?>')">Edit</button>
                <div id="actions-<?= htmlspecialchars($username) ?>" style="display:none; margin-top:5px;">
                    <form method="POST" action="" style="margin-bottom:5px;">
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>" />
                        <input type="submit" value="Delete" onclick="return confirm('Delete user <?= htmlspecialchars($username) ?>?');" />
                    </form>
                    <form method="POST" action="" style="margin-bottom:5px;">
                        <input type="hidden" name="action" value="renew" />
                        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>" />
                        <input type="number" name="valid_days" min="1" placeholder="Days" required />
                        <input type="submit" value="Renew" />
                    </form>
                    <form method="POST" action="" style="margin-bottom:5px;">
                        <input type="hidden" name="action" value="ban" />
                        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>" />
                        <input type="text" name="ban_message" placeholder="Ban message" />
                        <input type="submit" value="Ban" />
                    </form>
                    <form method="POST" action="" style="margin-bottom:5px;">
                        <input type="hidden" name="action" value="unban" />
                        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>" />
                        <input type="submit" value="Unban" />
                    </form>
                    <form method="POST" action="" style="margin-bottom:5px;">
                        <input type="hidden" name="action" value="increase_devices" />
                        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>" />
                        <input type="number" name="device_count" min="1" placeholder="Devices" required style="width: 80px;" />
                        <input type="submit" value="Set Devices" />
                    </form>
                    <form method="POST" action="" style="margin-bottom:5px;">
                        <input type="hidden" name="action" value="extend_validity" />
                        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>" />
                        <input type="number" name="extend_days" min="1" placeholder="Days" required style="width: 80px;" />
                        <input type="submit" value="Extend Validity" />
                    </form>
                    <form method="POST" action="" style="margin-bottom:5px;">
                        <input type="hidden" name="action" value="extend_validity_date" />
                        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>" />
                        <input type="date" name="extend_date" required />
                        <input type="submit" value="Set Expiry Date" />
                    </form>
                    <form method="POST" action="" style="margin-bottom:5px;">
                        <input type="hidden" name="action" value="update_active" />
                        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>" />
                        <label>Active: <input type="checkbox" name="active" <?= $active === 'true' ? 'checked' : '' ?> /></label>
                        <input type="submit" value="Update Active" />
                    </form>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
function toggleActions(id) {
    var elem = document.getElementById(id);
    if (elem.style.display === "none" || elem.style.display === "") {
        elem.style.display = "block";
    } else {
        elem.style.display = "none";
    }
}
</script>

<!-- Add Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
// Calculate user counts for donut graph
$activeCount = 0;
$bannedCount = 0;
$expiredCount = 0;
$today = new DateTime();

foreach ($users as $encUser => $data) {
    $expiry = devil99decode($data['ExpireData'] ?? '');
    $isBanned = devil99decode($data['IsBanned'] ?? 'false');
    $active = devil99decode($data['Actived'] ?? 'false');
    $isAdmin = (isset($data['Level']) && $data['Level'] == 1);

    $expiryDate = DateTime::createFromFormat('Y-m-d', $expiry);
    if (!$expiryDate) {
        $expiryDate = DateTime::createFromFormat('d-m-Y', $expiry);
    }

    if ($isBanned === 'true') {
        $bannedCount++;
    } elseif (!$isAdmin && $expiryDate && $expiryDate < $today) {
        $expiredCount++;
    } elseif ($active === 'true') {
        $activeCount++;
    }
}
?>

<div style="width: 400px; max-width: 100%; margin: 20px auto;">
    <canvas id="userStatusDonut"></canvas>
</div>

<script>
const ctx = document.getElementById('userStatusDonut').getContext('2d');
const userStatusDonut = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Active Users', 'Banned Users', 'Expired Users'],
        datasets: [{
            label: 'User Status',
            data: [<?= $activeCount ?>, <?= $bannedCount ?>, <?= $expiredCount ?>],
            backgroundColor: [
                'rgba(75, 192, 192, 0.7)',
                'rgba(255, 99, 132, 0.7)',
                'rgba(255, 206, 86, 0.7)'
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#e0e0e0'
                }
            }
        }
    }
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>
