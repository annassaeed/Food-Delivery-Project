<?php
include 'db_connect.php'; 
check_auth(); 

$user = get_user_data($conn);
$message = '';
$error = '';

// Handle POST request to change password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $user['id'];
    
    // 1. Fetch current hash from DB
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $db_user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$db_user) {
        $error = "User verification failed.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } elseif (strlen($new_password) < 8) {
        $error = "New password must be at least 8 characters long.";
    } 
    // 2. Verify current password securely
    elseif (!password_verify($current_password, $db_user['password'])) {
        $error = "The current password you entered is incorrect.";
    } else {
        // 3. Hash the new password and update the database
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_new_password, $user_id);
        
        if ($stmt->execute()) {
            $message = "Password changed successfully!";
            // Note: Keep the user logged in, but force them to log back in if you want maximum security.
        } else {
            $error = "Failed to update password: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Settings | MunchMasters Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .page-bg { background-color: #f8fafc; }
        .bg-primary { background-color: #FF6B6B; }
        .text-primary { color: #FF6B6B; }
    </style>
</head>
<body class="font-sans page-bg">
    <!-- Navigation (Standard structure) -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i data-feather="truck" class="text-primary h-8 w-8"></i>
                <span class="text-2xl font-bold text-primary">MunchMasters</span>
            </div>
            <div class="hidden md:flex space-x-8 items-center">
                <a href="dashboard.php" class="text-gray-800 hover:text-primary font-medium">Dashboard</a>
                <a href="order.php" class="text-gray-800 hover:text-primary font-medium">Order Food</a>
                <a href="orders.php" class="text-gray-800 hover:text-primary font-medium">My Orders</a>
                <a href="logout.php" class="bg-primary text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition">Logout</a>
            </div>
            <button class="md:hidden"><i data-feather="menu" class="h-6 w-6 text-gray-800"></i></button>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-3xl font-bold text-gray-900">Security & Settings</h2>
                <p class="text-gray-600">Manage your account credentials and application settings.</p>
            </div>
            
            <div class="flex flex-col md:flex-row">
                <!-- Sidebar Menu (for navigation between Profile/Settings) -->
                <div class="md:w-1/4 bg-gray-50 p-6 space-y-2">
                    <a href="profile.php" class="flex items-center space-x-3 text-gray-700 hover:text-primary p-3 rounded-lg">
                        <i data-feather="user" class="h-5 w-5"></i>
                        <span>My Details</span>
                    </a>
                    <a href="settings.php" class="flex items-center space-x-3 text-primary font-medium bg-white p-3 rounded-lg shadow-sm">
                        <i data-feather="settings" class="h-5 w-5"></i>
                        <span>Security & Settings</span>
                    </a>
                    <a href="dashboard.php" class="flex items-center space-x-3 text-gray-700 hover:text-primary p-3 rounded-lg">
                        <i data-feather="grid" class="h-5 w-5"></i>
                        <span>Back to Dashboard</span>
                    </a>
                </div>

                <!-- Main Content Form -->
                <div class="md:w-3/4 p-6">
                    <h3 class="text-xl font-bold mb-4 border-b pb-2">Change Password</h3>
                    
                    <?php if (!empty($message)): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="settings.php">
                        <div class="mb-4">
                            <label for="current_password" class="block text-gray-700 mb-2">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required 
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <div class="mb-4">
                            <label for="new_password" class="block text-gray-700 mb-2">New Password</label>
                            <input type="password" id="new_password" name="new_password" required 
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div class="mb-6">
                            <label for="confirm_password" class="block text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required 
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <button type="submit" name="change_password" class="bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-opacity-90 transition">
                            Update Password
                        </button>
                    </form>
                    
                    <h3 class="text-xl font-bold mt-8 mb-4 border-b pb-2">Account Management</h3>
                    <p class="text-gray-600 mb-4">
                        If you need to permanently close your account, please click the button below. 
                        This action cannot be undone.
                    </p>
                    <button class="bg-red-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-red-700 transition">
                        Deactivate Account
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script> feather.replace(); </script>
</body>
</html>