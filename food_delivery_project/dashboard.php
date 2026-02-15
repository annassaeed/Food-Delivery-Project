<?php
include 'db_connect.php';
check_auth(); // Ensure user is logged in

// Fetch user data
$user = get_user_data($conn);
if (!$user) {
    // If session user_id is invalid, redirect to login
    session_destroy();
    header("Location: login.php");
    exit();
}

// Fetch recent orders (limit 3)
$orders = [];
$user_id = $user['id'];

$stmt = $conn->prepare("
    SELECT o.id, o.order_date, o.total_amount, o.status, r.name AS restaurant_name
    FROM orders o
    JOIN restaurants r ON o.restaurant_id = r.id
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
    LIMIT 3
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | MunchMasters Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .dashboard-bg { background-color: #f8fafc; }
        .bg-primary { background-color: #FF6B6B; }
        .text-primary { color: #FF6B6B; }
        .hover\:text-primary:hover { color: #FF6B6B; }
        
        .order-status {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-preparing { background-color: #FEF3C7; color: #92400E; }
        .status-ontheway { background-color: #BFDBFE; color: #1E40AF; }
        .status-delivered { background-color: #D1FAE5; color: #065F46; }

        /* Custom style to force the dropdown menu open when clicked */
        .dropdown-open { display: block !important; }
    </style>
</head>
<body class="font-sans dashboard-bg">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <!-- LOGO: Made clickable to return to dashboard -->
            <a href="dashboard.php" class="flex items-center space-x-2 hover:opacity-80 transition">
                <i data-feather="truck" class="text-primary h-8 w-8"></i>
                <span class="text-2xl font-bold text-primary">MunchMasters</span>
            </a>
            <!-- End LOGO -->
            <div class="hidden md:flex space-x-8 items-center">
                <a href="dashboard.php" class="text-primary font-medium">Dashboard</a>
                <a href="order.php" class="text-gray-800 hover:text-primary font-medium">Order Food</a>
                <a href="orders.php" class="text-gray-800 hover:text-primary font-medium">My Orders</a>
                <div class="relative group">
                    <!-- Button to open/close the dropdown on click -->
                    <button id="user-menu-button" onclick="toggleDropdown(event)" class="flex items-center space-x-2 focus:outline-none">
                        <!-- Use a standard placeholder for profile image -->
                        <img src="https://placehold.co/200x200/FF6B6B/FFFFFF?text=<?php echo strtoupper(substr($user['name'], 0, 1)); ?>" alt="User" class="w-8 h-8 rounded-full">
                        <span class="font-medium"><?php echo htmlspecialchars($user['name']); ?></span>
                        <i data-feather="chevron-down" class="h-4 w-4"></i>
                    </button>
                    <!-- Dropdown menu -->
                    <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                        <a href="profile.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Profile</a>
                        <a href="settings.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Settings</a>
                        <a href="logout.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
            </div>
            <button class="md:hidden">
                <i data-feather="menu" class="h-6 w-6 text-gray-800"></i>
            </button>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar -->
            <div class="md:w-1/4">
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <div class="flex items-center space-x-4 mb-6">
                        <img src="https://placehold.co/200x200/4ECDC4/FFFFFF?text=<?php echo strtoupper(substr($user['name'], 0, 1)); ?>" alt="User" class="w-16 h-16 rounded-full">
                        <div>
                            <h3 class="font-bold text-lg"><?php echo htmlspecialchars($user['name']); ?></h3>
                            <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <a href="index.html" class="flex items-center space-x-3 text-gray-700 hover:text-primary">
                            <i data-feather="home" class="h-5 w-5"></i>
                            <span>Home</span>
                        </a>
                        <a href="dashboard.php" class="flex items-center space-x-3 text-primary font-medium">
                            <i data-feather="grid" class="h-5 w-5"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="order.php" class="flex items-center space-x-3 text-gray-700 hover:text-primary">
                            <i data-feather="shopping-cart" class="h-5 w-5"></i>
                            <span>Order Food</span>
                        </a>
                        <a href="orders.php" class="flex items-center space-x-3 text-gray-700 hover:text-primary">
                            <i data-feather="list" class="h-5 w-5"></i>
                            <span>My Orders</span>
                        </a>
                        <!-- Placeholder links, implementation not included in this batch -->
                        <a href="profile.php" class="flex items-center space-x-3 text-gray-700 hover:text-primary">
                            <i data-feather="user" class="h-5 w-5"></i>
                            <span>Profile</span>
                        </a>
                        <a href="logout.php" class="flex items-center space-x-3 text-gray-700 hover:text-primary">
                            <i data-feather="log-out" class="h-5 w-5"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold">Delivery Address</h3>
                        <a href="profile.php" class="text-primary text-sm hover:underline">Edit</a>
                    </div>
                    <p class="text-gray-600 text-sm"><?php echo nl2br(htmlspecialchars($user['address'])); ?></p>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="md:w-3/4">
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-2xl font-bold mb-6">Welcome back, <?php echo htmlspecialchars(explode(' ', $user['name'])[0]); ?>!</h2>
                    
                    <!-- Quick Actions -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                        <a href="order.php" class="bg-primary bg-opacity-10 p-4 rounded-lg text-center hover:bg-opacity-20 transition">
                            <div class="bg-primary p-3 rounded-full inline-flex mb-3">
                                <i data-feather="shopping-cart" class="h-6 w-6 text-white"></i>
                            </div>
                            <h3 class="font-bold mb-1">Order Food</h3>
                            <p class="text-gray-600 text-sm">Browse restaurants</p>
                        </a>
                        <a href="orders.php" class="bg-primary bg-opacity-10 p-4 rounded-lg text-center hover:bg-opacity-20 transition">
                            <div class="bg-primary p-3 rounded-full inline-flex mb-3">
                                <i data-feather="list" class="h-6 w-6 text-white"></i>
                            </div>
                            <h3 class="font-bold mb-1">My Orders</h3>
                            <p class="text-gray-600 text-sm">Track your orders</p>
                        </a>
                        <a href="favorites.php" class="bg-primary bg-opacity-10 p-4 rounded-lg text-center hover:bg-opacity-20 transition">
                            <div class="bg-primary p-3 rounded-full inline-flex mb-3">
                                <i data-feather="heart" class="h-6 w-6 text-white"></i>
                            </div>
                            <h3 class="font-bold mb-1">Favorites</h3>
                            <p class="text-gray-600 text-sm">Your saved items</p>
                        </a>
                    </div>
                    
                    <!-- Recent Orders -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg">Recent Orders</h3>
                            <a href="orders.php" class="text-primary text-sm hover:underline">View All</a>
                        </div>
                        
                        <?php if (empty($orders)): ?>
                            <div class="bg-gray-50 p-6 rounded-lg text-center">
                                <i data-feather="package" class="h-10 w-10 text-gray-400 mx-auto mb-4"></i>
                                <p class="text-gray-600">You haven't placed any orders yet</p>
                                <a href="order.php" class="mt-4 inline-block bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition">
                                    Order Now
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="bg-gray-50 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restaurant</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($order['restaurant_name']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="order-status status-<?php echo strtolower(str_replace(' ', '', $order['status'])); ?>">
                                                    <?php echo htmlspecialchars($order['status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="order-details.php?id=<?php echo $order['id']; ?>" class="text-primary hover:text-red-700">Details</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        feather.replace();

        // New JavaScript for persistent dropdown menu on click/tap
        function toggleDropdown(event) {
            // Prevent the navigation event if clicking the button itself
            event.stopPropagation();
            
            const menu = document.getElementById('user-menu');
            const isMenuOpen = menu.classList.contains('dropdown-open');

            // Close all other dropdowns (optional, but good practice)
            document.querySelectorAll('#user-menu').forEach(m => {
                 if (m !== menu) {
                    m.classList.remove('dropdown-open');
                 }
            });

            // Toggle the current menu
            if (isMenuOpen) {
                menu.classList.remove('dropdown-open');
                // Re-add the Tailwind class for desktop hover if needed, 
                // but since the CSS only adds .dropdown-open, removing it suffices.
            } else {
                menu.classList.add('dropdown-open');
            }
        }
        
        // Close dropdown when clicking outside of it
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('user-menu');
            const button = document.getElementById('user-menu-button');
            
            // Check if the click occurred outside both the menu and the button
            if (menu && !menu.contains(event.target) && button && !button.contains(event.target)) {
                menu.classList.remove('dropdown-open');
            }
        });

        // Existing JS for order status cards
        document.addEventListener('DOMContentLoaded', function() {
            const statusCards = document.querySelectorAll('.order-status');
            statusCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.classList.add('animate-pulse');
                });
                card.addEventListener('mouseleave', function() {
                    this.classList.remove('animate-pulse');
                });
            });
        });
    </script>
</body>
</html>