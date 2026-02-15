<?php
include 'db_connect.php'; 
check_auth(); 

$user = get_user_data($conn);
$message = '';
$error = '';

// Handle POST request to update profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $user_id = $user['id'];

    if (empty($name)) {
        $error = "Full Name cannot be empty.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $phone, $address, $user_id);
        
        if ($stmt->execute()) {
            $message = "Profile updated successfully!";
            // Refresh user data after successful update
            $user = get_user_data($conn);
        } else {
            $error = "Failed to update profile: " . $conn->error;
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
    <title>Profile | MunchMasters Express</title>
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
                <h2 class="text-3xl font-bold text-gray-900">User Profile</h2>
                <p class="text-gray-600">Manage your personal details and delivery information.</p>
            </div>
            
            <div class="flex flex-col md:flex-row">
                <!-- Sidebar Menu (for navigation between Profile/Settings) -->
                <div class="md:w-1/4 bg-gray-50 p-6 space-y-2">
                    <a href="profile.php" class="flex items-center space-x-3 text-primary font-medium bg-white p-3 rounded-lg shadow-sm">
                        <i data-feather="user" class="h-5 w-5"></i>
                        <span>My Details</span>
                    </a>
                    <a href="settings.php" class="flex items-center space-x-3 text-gray-700 hover:text-primary p-3 rounded-lg">
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
                    <?php if (!empty($message)): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="profile.php">
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 mb-2">Full Name</label>
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 mb-2">Email Address (Read Only)</label>
                            <input type="email" id="email" name="email" readonly disabled
                                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border bg-gray-100 rounded-lg">
                        </div>
                        
                        <div class="mb-4">
                            <label for="phone" class="block text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div class="mb-6">
                            <label for="address" class="block text-gray-700 mb-2">Delivery Address</label>
                            <textarea id="address" name="address" rows="3"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-opacity-90 transition">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script> feather.replace(); </script>
</body>
</html>