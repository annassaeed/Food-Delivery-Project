<?php
include 'db_connect.php';
check_auth(); // Protect this page

$user = get_user_data($conn);

// Fetch all restaurants
$restaurants = [];
$search_results = [];
$search_query = "";

$base_query = "SELECT * FROM restaurants WHERE is_active = TRUE";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = "%" . trim($_GET['search']) . "%";
    $stmt = $conn->prepare($base_query . " AND (name LIKE ? OR cuisine_type LIKE ?) ORDER BY rating DESC");
    $stmt->bind_param("ss", $search_query, $search_query);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }
    $stmt->close();
} else {
    $result = $conn->query($base_query . " ORDER BY rating DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $restaurants[] = $row;
        }
    }
}

// Data source to loop over
$data_to_display = !empty($search_results) ? $search_results : $restaurants;

// Filter restaurants into featured (top 3) and others if not searching
$featured_restaurants = !$search_query ? array_slice($restaurants, 0, 3) : [];
$all_restaurants_list = !$search_query ? array_slice($restaurants, 3) : $search_results;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Food | MunchMasters Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .order-bg { background-color: #f8fafc; }
        .bg-primary { background-color: #FF6B6B; }
        .text-primary { color: #FF6B6B; }
        .focus\:ring-primary:focus { --tw-ring-color: #FF6B6B; }
        
        .restaurant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .price-range { display: inline-block; font-size: 0.75rem; font-weight: 500; }
        .price-1 { color: #065F46; } /* $ */
        .price-2 { color: #065F46; } /* $$ */
        .price-3 { color: #92400E; } /* $$$ */
        .price-4 { color: #92400E; } /* $$$$ */
    </style>
</head>
<body class="font-sans order-bg">
    <!-- Navigation (Same as Dashboard) -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i data-feather="truck" class="text-primary h-8 w-8"></i>
                <span class="text-2xl font-bold text-primary">MunchMasters</span>
            </div>
            <div class="hidden md:flex space-x-8 items-center">
                <a href="dashboard.php" class="text-gray-800 hover:text-primary font-medium">Dashboard</a>
                <a href="order.php" class="text-primary font-medium">Order Food</a>
                <a href="orders.php" class="text-gray-800 hover:text-primary font-medium">My Orders</a>
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
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar -->
            <div class="md:w-1/4">
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <div class="mb-4 flex justify-between">
                        <h3 class="font-bold text-lg">Filters</h3>
                    </div>
                    
                    <!-- Search -->
                    <form method="GET" action="order.php" class="mb-6">
                        <div class="relative">
                            <input type="text" name="search" placeholder="Search restaurants..." 
                                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                            <button type="submit" class="absolute right-3 top-2 text-gray-500 hover:text-primary">
                                <i data-feather="search" class="h-5 w-5"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Simplified Filters (For now, just a placeholder, as the actual logic requires more complex PHP/JS interaction) -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Cuisine</h4>
                        <div class="space-y-2 text-gray-700">
                            <span>Italian, Mexican, Japanese, American...</span>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Delivery Time</h4>
                        <div class="space-y-2 text-gray-700">
                            <span>Under 30 min, 30-45 min, 45-60 min...</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="font-bold text-lg mb-4">Delivery Address</h3>
                    <p class="text-gray-600 mb-4"><?php echo nl2br(htmlspecialchars($user['address'])); ?></p>
                    <a href="profile.php" class="text-primary text-sm font-medium hover:underline">Change Address</a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="md:w-3/4">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <?php if (!empty($search_query) && empty($search_results)): ?>
                        <div class="text-center py-12">
                            <i data-feather="search" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                            <h3 class="text-xl font-bold mb-2">No results found</h3>
                            <p class="text-gray-600 mb-4">We couldn't find any restaurants matching "<?php echo htmlspecialchars(trim($_GET['search'])); ?>"</p>
                            <a href="order.php" class="inline-block bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition">
                                Back to Restaurants
                            </a>
                        </div>
                    
                    <?php else: ?>
                        <h2 class="text-2xl font-bold mb-6">
                            <?php echo $search_query ? 'Search Results' : 'Restaurants Near You'; ?>
                        </h2>
                        
                        <!-- Featured Restaurants (Only if not searching) -->
                        <?php if (empty($search_query) && !empty($featured_restaurants)): ?>
                            <div class="mb-8">
                                <h3 class="font-bold text-lg mb-4">Featured</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <?php foreach ($featured_restaurants as $restaurant): ?>
                                        <a href="restaurant.php?id=<?php echo $restaurant['id']; ?>" class="restaurant-card bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                                            <div class="relative">
                                                <img src="<?php echo htmlspecialchars($restaurant['image_url']); ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/640x360/4ECDC4/FFFFFF?text=Food'" class="w-full h-48 object-cover">
                                                <div class="absolute top-2 left-2 bg-primary text-white px-2 py-1 rounded text-xs font-bold">
                                                    Featured
                                                </div>
                                            </div>
                                            <div class="p-4">
                                                <div class="flex justify-between items-start mb-2">
                                                    <h3 class="font-bold"><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded"><?php echo htmlspecialchars($restaurant['rating']); ?> <i data-feather="star" class="h-3 w-3 inline"></i></span>
                                                </div>
                                                <div class="flex items-center text-sm text-gray-600 mb-2">
                                                    <span class="price-range price-<?php echo strlen($restaurant['price_range']); ?>"><?php echo htmlspecialchars($restaurant['price_range']); ?></span>
                                                    <span class="mx-1">•</span>
                                                    <!-- Use cuisine_type column -->
                                                    <span><?php echo htmlspecialchars($restaurant['cuisine_type']); ?></span>
                                                    <span class="mx-1">•</span>
                                                    <span><?php echo htmlspecialchars($restaurant['delivery_time']); ?> min</span>
                                                </div>
                                                <p class="text-primary text-sm font-medium">View Menu</p>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- All Restaurants / Search Results -->
                        <div>
                            <h3 class="font-bold text-lg mb-4"><?php echo $search_query ? 'Search Results' : 'All Restaurants'; ?></h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
                                <?php foreach ($all_restaurants_list as $restaurant): ?>
                                    <a href="restaurant.php?id=<?php echo $restaurant['id']; ?>" class="restaurant-card bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                                        <img src="<?php echo htmlspecialchars($restaurant['image_url']); ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/640x360/4ECDC4/FFFFFF?text=Food'" class="w-full h-48 object-cover">
                                        <div class="p-4">
                                            <div class="flex justify-between items-start mb-2">
                                                <h3 class="font-bold"><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded"><?php echo htmlspecialchars($restaurant['rating']); ?> <i data-feather="star" class="h-3 w-3 inline"></i></span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                                <span class="price-range price-<?php echo strlen($restaurant['price_range']); ?>"><?php echo htmlspecialchars($restaurant['price_range']); ?></span>
                                                <span class="mx-1">•</span>
                                                <!-- Use cuisine_type column -->
                                                <span><?php echo htmlspecialchars($restaurant['cuisine_type']); ?></span>
                                                <span class="mx-1">•</span>
                                                <span><?php echo htmlspecialchars($restaurant['delivery_time']); ?> min</span>
                                            </div>
                                            <p class="text-primary text-sm font-medium">View Menu</p>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        feather.replace();
    </script>
</body>
</html>