<?php
include 'db_connect.php';
check_auth(); // Protect this page

$user = get_user_data($conn);
$restaurant_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$restaurant = null;
$menu_items = [];
$categories = [];

if ($restaurant_id > 0) {
    // 1. Fetch Restaurant Details
    $stmt = $conn->prepare("SELECT * FROM restaurants WHERE id = ? AND is_active = TRUE LIMIT 1");
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $restaurant = $result->fetch_assoc();
    }
    $stmt->close();
    
    // 2. Fetch Menu Items and Group by Category
    if ($restaurant) {
        // NOTE: The query below uses 'image_url' for the menu items as well, assuming it exists.
        $stmt = $conn->prepare("SELECT id, name, description, price, category, image_url FROM menu_items WHERE restaurant_id = ? ORDER BY category, name");
        $stmt->bind_param("i", $restaurant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($item = $result->fetch_assoc()) {
            $menu_items[$item['category']][] = $item;
            if (!in_array($item['category'], $categories)) {
                $categories[] = $item['category'];
            }
        }
        $stmt->close();
    }
}

// Handle invalid restaurant ID
if (!$restaurant) {
    header("Location: order.php");
    exit();
}

// Pass PHP variables to JavaScript
$delivery_charge_js = $restaurant['delivery_charge'];
$restaurant_id_js = $restaurant['id'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['name']); ?> | MunchMasters Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .restaurant-bg { background-color: #f8fafc; }
        .bg-primary { background-color: #FF6B6B; }
        .text-primary { color: #FF6B6B; }
        
        .menu-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .active-category {
            border-bottom: 2px solid #FF6B6B;
            color: #FF6B6B;
        }
        .cart-item { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn {
            from { transform: translateX(20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="font-sans restaurant-bg">
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
        <!-- Restaurant Header -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
            <div class="relative">
                <!-- Use image_url column -->
                <img src="<?php echo htmlspecialchars($restaurant['image_url']); ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/1000x300/4ECDC4/FFFFFF?text=Restaurant+Image'" class="w-full h-64 object-cover">
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-6">
                    <h1 class="text-3xl font-bold text-white mb-2"><?php echo htmlspecialchars($restaurant['name']); ?></h1>
                    <div class="flex items-center space-x-4 text-white">
                        <div class="flex items-center">
                            <i data-feather="star" class="h-5 w-5 text-yellow-400 mr-1"></i>
                            <span><?php echo htmlspecialchars($restaurant['rating']); ?></span>
                        </div>
                        <span>•</span>
                        <!-- Use cuisine_type column -->
                        <span><?php echo htmlspecialchars($restaurant['cuisine_type']); ?></span>
                        <span>•</span>
                        <span><?php echo htmlspecialchars($restaurant['delivery_time']); ?> min delivery</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-gray-600"><?php echo htmlspecialchars($restaurant['description']); ?></p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <div class="flex items-center">
                        <i data-feather="map-pin" class="h-4 w-4 text-gray-500 mr-2"></i>
                        <span class="text-gray-600">Address: <?php echo htmlspecialchars($restaurant['address']) . ', ' . htmlspecialchars($restaurant['city']); ?></span>
                    </div>
                    <div class="flex items-center">
                        <i data-feather="dollar-sign" class="h-4 w-4 text-gray-500 mr-2"></i>
                        <span class="text-gray-600">Delivery Fee: $<?php echo number_format($restaurant['delivery_charge'], 2); ?></span>
                    </div>
                    <div class="flex items-center">
                        <i data-feather="credit-card" class="h-4 w-4 text-gray-500 mr-2"></i>
                        <span class="text-gray-600">Minimum Order: $<?php echo number_format($restaurant['minimum_order'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Menu -->
            <div class="lg:w-2/3">
                <!-- Category Tabs -->
                <div class="sticky top-20 z-10 bg-white py-4 mb-6 flex overflow-x-auto rounded-xl shadow-sm">
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $index => $category): ?>
                            <button class="category-tab px-4 py-2 whitespace-nowrap font-medium text-gray-600 hover:text-primary transition <?php echo $index === 0 ? 'active-category' : ''; ?>" data-category-name="<?php echo htmlspecialchars($category); ?>">
                                <?php echo htmlspecialchars($category); ?>
                            </button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-gray-500 px-4 py-2">No menu categories available.</div>
                    <?php endif; ?>
                </div>
                
                <!-- Menu Items List -->
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $index => $category): ?>
                        <div class="category-content <?php echo $index === 0 ? 'block' : 'hidden'; ?>" data-category="<?php echo htmlspecialchars($category); ?>">
                            <h2 class="text-2xl font-bold mb-6"><?php echo htmlspecialchars($category); ?></h2>
                            
                            <div class="grid grid-cols-1 gap-6 mb-12">
                                <?php foreach ($menu_items[$category] as $item): ?>
                                    <div class="menu-item bg-white rounded-lg shadow-sm p-6 transition cursor-pointer flex justify-between items-start" data-id="<?php echo $item['id']; ?>" data-name="<?php echo htmlspecialchars($item['name']); ?>" data-price="<?php echo $item['price']; ?>">
                                        <div class="flex-1">
                                            <h3 class="font-bold text-lg mb-1"><?php echo htmlspecialchars($item['name']); ?></h3>
                                            <p class="text-gray-600 text-sm mb-3"><?php echo htmlspecialchars($item['description']); ?></p>
                                            <p class="text-primary font-bold">$<?php echo number_format($item['price'], 2); ?></p>
                                        </div>
                                        <?php if ($item['image_url']): ?>
                                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/80x80/FF6B6B/FFFFFF?text=Dish'" class="w-20 h-20 object-cover rounded-lg ml-4 flex-shrink-0">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-xl shadow-md p-8 text-center">
                        <i data-feather="alert-triangle" class="h-8 w-8 text-yellow-500 mx-auto mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Menu Not Available</h3>
                        <p class="text-gray-600">This restaurant has not yet uploaded its menu items.</p>
                        <a href="order.php" class="mt-4 inline-block text-primary hover:underline">Browse other restaurants</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Order Summary (Sticky Sidebar) -->
            <div class="lg:w-1/3">
                <div class="sticky top-24 bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold mb-4">Your Order</h2>
                        <div id="cart-items" class="space-y-4 max-h-96 overflow-y-auto">
                            <div class="text-center py-8 text-gray-500" id="empty-cart-message">
                                <i data-feather="shopping-cart" class="h-8 w-8 mx-auto mb-4"></i>
                                <p>Your cart is empty</p>
                                <p class="text-sm">Add items to get started</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Subtotal</span>
                            <span id="subtotal" class="font-medium">$0.00</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Delivery Fee</span>
                            <span class="font-medium">$<?php echo number_format($restaurant['delivery_charge'], 2); ?></span>
                            <input type="hidden" id="delivery-fee-value" value="<?php echo $restaurant['delivery_charge']; ?>">
                        </div>
                        <div class="flex justify-between mb-4">
                            <span class="text-gray-600">Tax (0%)</span>
                            <span class="font-medium">$0.00</span>
                            <input type="hidden" id="tax-rate-value" value="0.00">
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t pt-4">
                            <span>Total</span>
                            <span id="total">$0.00</span>
                        </div>
                        
                        <!-- NOTE: Checkout logic is currently front-end only (no order creation in PHP) -->
                        <button id="checkout-btn" class="w-full bg-primary text-white py-3 rounded-lg font-bold hover:bg-opacity-90 transition mt-6 disabled:opacity-50 disabled:cursor-not-allowed">
                            Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Pass PHP variables to JavaScript
        const PHP_DELIVERY_CHARGE = <?php echo json_encode($delivery_charge_js); ?>;
        const PHP_RESTAURANT_ID = <?php echo json_encode($restaurant_id_js); ?>;
        const PHP_USER_ADDRESS = <?php echo json_encode($user['address']); ?>;

        feather.replace();
        
        document.addEventListener('DOMContentLoaded', function() {
            // ... (existing category tabs logic) ...
            const categoryTabs = document.querySelectorAll('.category-tab');
            const categoryContents = document.querySelectorAll('.category-content');
            
            categoryTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    categoryTabs.forEach(t => t.classList.remove('active-category'));
                    this.classList.add('active-category');
                    
                    const selectedCategory = this.getAttribute('data-category-name');
                    categoryContents.forEach(content => {
                        if (content.getAttribute('data-category') === selectedCategory) {
                            content.classList.remove('hidden');
                        } else {
                            content.classList.add('hidden');
                        }
                    });
                });
            });


            // Cart functionality
            const cartItemsContainer = document.getElementById('cart-items');
            const emptyCartMessage = document.getElementById('empty-cart-message');
            const subtotalElement = document.getElementById('subtotal');
            const totalElement = document.getElementById('total');
            const checkoutBtn = document.getElementById('checkout-btn');
            
            let cart = [];
            const deliveryFee = PHP_DELIVERY_CHARGE;
            const taxRate = 0.00; // Assuming 0% tax for simplicity
            
            // Add to cart on menu item click
            document.querySelectorAll('.menu-item').forEach(item => {
                item.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const price = parseFloat(this.getAttribute('data-price'));
                    
                    const existingItem = cart.find(item => item.id === id);
                    
                    if (existingItem) {
                        existingItem.quantity += 1;
                    } else {
                        cart.push({ id, name, price, quantity: 1 });
                    }
                    
                    updateCart();
                });
            });
            
            // Event delegation for quantity buttons (since items are dynamically added)
            cartItemsContainer.addEventListener('click', function(e) {
                const target = e.target.closest('button');
                if (!target) return;

                const itemId = target.getAttribute('data-id');
                let item = cart.find(i => i.id === itemId);

                if (!item) return;

                if (target.classList.contains('increase-item')) {
                    item.quantity += 1;
                } else if (target.classList.contains('decrease-item')) {
                    if (item.quantity > 1) {
                        item.quantity -= 1;
                    } else {
                        // Remove item if quantity hits zero
                        cart = cart.filter(i => i.id !== itemId);
                    }
                } else if (target.classList.contains('remove-item')) {
                     cart = cart.filter(i => i.id !== itemId);
                }
                updateCart();
            });

            // Update cart display and totals
            function updateCart() {
                let subtotal = 0;
                cartItemsContainer.innerHTML = '';
                
                if (cart.length === 0) {
                    emptyCartMessage.classList.remove('hidden');
                    checkoutBtn.disabled = true;
                } else {
                    emptyCartMessage.classList.add('hidden');
                    checkoutBtn.disabled = false;
                    
                    cart.forEach(item => {
                        const itemTotal = item.price * item.quantity;
                        subtotal += itemTotal;
                        
                        const cartItem = document.createElement('div');
                        cartItem.className = 'cart-item bg-gray-50 rounded-lg p-4';
                        cartItem.innerHTML = `
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-medium">${item.name}</h3>
                                <span class="font-medium">$${itemTotal.toFixed(2)}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center border rounded-lg">
                                    <button class="decrease-item px-2 py-1 text-gray-600 hover:bg-gray-200" data-id="${item.id}">
                                        <i data-feather="minus" class="h-4 w-4"></i>
                                    </button>
                                    <span class="px-3">${item.quantity}</span>
                                    <button class="increase-item px-2 py-1 text-gray-600 hover:bg-gray-200" data-id="${item.id}">
                                        <i data-feather="plus" class="h-4 w-4"></i>
                                    </button>
                                </div>
                                <button class="remove-item text-red-500 text-sm" data-id="${item.id}">
                                    <i data-feather="trash-2" class="h-4 w-4"></i>
                                </button>
                            </div>
                        `;
                        cartItemsContainer.appendChild(cartItem);
                    });
                }
                
                const totalTax = subtotal * taxRate;
                const total = subtotal + deliveryFee + totalTax;
                
                subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
                totalElement.textContent = `$${total.toFixed(2)}`;
                
                // Re-attach feather icons for dynamically added elements
                feather.replace();
            }
            
            // ==========================================================
            // *** CRITICAL CHANGE: Implement Order Submission via Fetch ***
            // ==========================================================
            checkoutBtn.addEventListener('click', async function() {
                if (cart.length === 0) {
                    alert('Your cart is empty. Please add items to order.');
                    return;
                }

                // Show a loading state
                checkoutBtn.disabled = true;
                checkoutBtn.textContent = 'Processing...';

                const totalAmount = parseFloat(totalElement.textContent.replace('$', ''));
                
                const orderData = {
                    restaurant_id: PHP_RESTAURANT_ID,
                    delivery_address: PHP_USER_ADDRESS,
                    total_amount: totalAmount.toFixed(2),
                    items: cart
                };
                
                try {
                    const response = await fetch('process_order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(orderData)
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        // Success message (using custom method instead of alert)
                        showTemporaryMessage('Order #' + result.order_id + ' placed successfully!', 'green');

                        // Clear cart
                        cart = [];
                        updateCart();
                        
                        // Optional: Redirect to orders page
                        setTimeout(() => {
                            window.location.href = 'orders.php';
                        }, 2000); 

                    } else {
                        showTemporaryMessage('Order failed: ' + result.message, 'red');
                    }
                } catch (error) {
                    console.error('Checkout error:', error);
                    showTemporaryMessage('Network error. Could not place order.', 'red');
                } finally {
                    checkoutBtn.disabled = false;
                    checkoutBtn.textContent = 'Checkout';
                }
            });

            // Custom message function (since we cannot use alert)
            function showTemporaryMessage(message, type) {
                const nav = document.querySelector('nav');
                let messageDiv = document.getElementById('temp-message');
                if (!messageDiv) {
                    messageDiv = document.createElement('div');
                    messageDiv.id = 'temp-message';
                    messageDiv.className = 'fixed top-0 left-0 right-0 z-50 text-center py-3 font-bold transition-transform transform duration-300 translate-y-0 shadow-lg';
                    nav.parentNode.insertBefore(messageDiv, nav.nextSibling);
                }
                
                messageDiv.textContent = message;
                messageDiv.style.backgroundColor = type === 'green' ? '#D1FAE5' : '#FECACA';
                messageDiv.style.color = type === 'green' ? '#065F46' : '#991B1B';

                setTimeout(() => {
                    messageDiv.style.transform = 'translateY(-100%)';
                    setTimeout(() => messageDiv.remove(), 300);
                }, 4000);
            }
        });
    </script>
</body>
</html>