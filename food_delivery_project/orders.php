<?php
include 'db_connect.php';
check_auth(); // Protect this page

$user = get_user_data($conn);

// Fetch ALL orders for the user
$orders = [];
$user_id = $user['id'];

if ($user_id) {
    $stmt = $conn->prepare("
        SELECT o.id, o.order_date, o.total_amount, o.status, r.name AS restaurant_name
        FROM orders o
        JOIN restaurants r ON o.restaurant_id = r.id
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC
    ");
    
    // Check if the query preparation failed (e.g., table structure issue)
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
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
    <title>My Orders | MunchMasters Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .page-bg { background-color: #f8fafc; }
        .bg-primary { background-color: #FF6B6B; }
        .text-primary { color: #FF6B6B; }
        
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
    </style>
</head>
<body class="font-sans page-bg">
    <!-- Navigation (Same as Dashboard) -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i data-feather="truck" class="text-primary h-8 w-8"></i>
                <span class="text-2xl font-bold text-primary">MunchMasters</span>
            </div>
            <div class="hidden md:flex space-x-8 items-center">
                <a href="dashboard.php" class="text-gray-800 hover:text-primary font-medium">Dashboard</a>
                <!-- Link to start a new order -->
                <a href="order.php" class="text-gray-800 hover:text-primary font-medium">Start New Order</a> 
                <a href="orders.php" class="text-primary font-medium">My Orders</a>
                <div class="relative group">
                    <button class="flex items-center space-x-2 focus:outline-none">
                        <img src="https://placehold.co/200x200/FF6B6B/FFFFFF?text=<?php echo strtoupper(substr($user['name'], 0, 1)); ?>" alt="User" class="w-8 h-8 rounded-full">
                        <span class="font-medium"><?php echo htmlspecialchars($user['name']); ?></span>
                        <i data-feather="chevron-down" class="h-4 w-4"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                        <a href="profile.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Profile</a>
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
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Your Order History</h2>
            
            <?php if (empty($orders)): ?>
                <div class="bg-gray-50 p-6 rounded-lg text-center">
                    <i data-feather="package" class="h-10 w-10 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-gray-600">You haven't placed any orders yet.</p>
                    <a href="order.php" class="mt-4 inline-block bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition">
                        Start Ordering
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
    <script>
        feather.replace();
    </script>
</body>
</html>