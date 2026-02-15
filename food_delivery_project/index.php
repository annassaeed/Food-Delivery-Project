<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MunchMasters Express | Food Delivery</title>
    <link rel="icon" type="image/x-icon" href="/static/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.net.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF6B6B',
                        secondary: '#4ECDC4',
                        accent: '#FFE66D'
                    }
                }
            }
        }
    </script>
    <style>
        .hero-bg {
            background: linear-gradient(135deg, rgba(255,107,107,0.9) 0%, rgba(78,205,196,0.9) 100%);
        }
        .dish-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .floating-btn {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="font-sans bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i data-feather="truck" class="text-primary h-8 w-8"></i>
                <span class="text-2xl font-bold text-primary">MunchMasters</span>
            </div>
            <div class="hidden md:flex space-x-8">
                <a href="index.html" class="text-gray-800 hover:text-primary font-medium">Home</a>
                <a href="#menu" class="text-gray-800 hover:text-primary font-medium">Menu</a>
                <a href="#how-it-works" class="text-gray-800 hover:text-primary font-medium">How It Works</a>
                <a href="#testimonials" class="text-gray-800 hover:text-primary font-medium">Reviews</a>
                <a href="login.php" class="text-gray-800 hover:text-primary font-medium">Login</a>
                <a href="register.php" class="text-gray-800 hover:text-primary font-medium">Register</a>
                <a href="dashboard.php" class="text-gray-800 hover:text-primary font-medium">Dashboard</a>
            </div>
            <div class="flex items-center space-x-4">
                <button class="md:hidden">
                    <i data-feather="menu" class="h-6 w-6 text-gray-800"></i>
                </button>
                <a href="login.php" class="hidden md:block bg-primary text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition">
                    Sign In
                </a>
                <a href="order.php" class="bg-secondary text-white px-4 py-2 rounded-full hover:bg-opacity-90 transition">
                    Order Now
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-bg text-white">
        <div class="container mx-auto px-4 py-20 md:py-32 flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">Delicious Food Delivered To Your Doorstep</h1>
                <p class="text-xl mb-8">Order from your favorite local restaurants with just a few taps</p>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="order.php" class="bg-accent text-gray-900 px-6 py-3 rounded-full font-bold hover:bg-opacity-90 transition">
                        Order Now
                    </a>
                    <button class="bg-white bg-opacity-20 px-6 py-3 rounded-full font-bold hover:bg-opacity-30 transition border border-white">
                        Download App
                    </button>
                </div>
            </div>
            <div class="md:w-1/2 relative">
                <img src="http://static.photos/food/1024x576/5" alt="Delicious Food" class="rounded-lg shadow-xl w-full max-w-md mx-auto">
                <div class="absolute -bottom-5 -right-5 bg-white p-3 rounded-full shadow-lg floating-btn">
                    <i data-feather="shopping-cart" class="text-primary h-6 w-6"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Dishes -->
    <section id="menu" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Popular Dishes</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Browse our selection of delicious meals prepared by top chefs in your area</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Dish 1 -->
                <div class="dish-card bg-white rounded-xl shadow-md overflow-hidden transition duration-300">
                    <div class="relative">
                        <img src="http://static.photos/food/640x360/1" alt="Margherita Pizza" class="w-full h-48 object-cover">
                        <div class="absolute top-2 right-2 bg-primary text-white px-2 py-1 rounded-full text-xs font-bold">
                            $12.99
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">Margherita Pizza</h3>
                        <p class="text-gray-600 text-sm mb-3">Classic pizza with tomato sauce, mozzarella, and basil</p>
                        <div class="flex justify-between items-center">
                            <div class="flex">
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <span class="text-sm ml-1">4.8 (120)</span>
                            </div>
                            <button class="text-primary hover:text-primary-dark text-sm font-medium">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Dish 2 -->
                <div class="dish-card bg-white rounded-xl shadow-md overflow-hidden transition duration-300">
                    <div class="relative">
                        <img src="http://static.photos/food/640x360/2" alt="Chicken Burger" class="w-full h-48 object-cover">
                        <div class="absolute top-2 right-2 bg-primary text-white px-2 py-1 rounded-full text-xs font-bold">
                            $9.99
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">Chicken Burger</h3>
                        <p class="text-gray-600 text-sm mb-3">Juicy chicken patty with lettuce and special sauce</p>
                        <div class="flex justify-between items-center">
                            <div class="flex">
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <span class="text-sm ml-1">4.7 (95)</span>
                            </div>
                            <button class="text-primary hover:text-primary-dark text-sm font-medium">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Dish 3 -->
                <div class="dish-card bg-white rounded-xl shadow-md overflow-hidden transition duration-300">
                    <div class="relative">
                        <img src="http://static.photos/food/640x360/3" alt="Sushi Combo" class="w-full h-48 object-cover">
                        <div class="absolute top-2 right-2 bg-primary text-white px-2 py-1 rounded-full text-xs font-bold">
                            $15.99
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">Sushi Combo</h3>
                        <p class="text-gray-600 text-sm mb-3">Assorted sushi rolls with wasabi and soy sauce</p>
                        <div class="flex justify-between items-center">
                            <div class="flex">
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <span class="text-sm ml-1">4.9 (150)</span>
                            </div>
                            <button class="text-primary hover:text-primary-dark text-sm font-medium">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Dish 4 -->
                <div class="dish-card bg-white rounded-xl shadow-md overflow-hidden transition duration-300">
                    <div class="relative">
                        <img src="http://static.photos/food/640x360/4" alt="Caesar Salad" class="w-full h-48 object-cover">
                        <div class="absolute top-2 right-2 bg-primary text-white px-2 py-1 rounded-full text-xs font-bold">
                            $8.99
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">Caesar Salad</h3>
                        <p class="text-gray-600 text-sm mb-3">Fresh romaine lettuce with croutons and dressing</p>
                        <div class="flex justify-between items-center">
                            <div class="flex">
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <span class="text-sm ml-1">4.5 (80)</span>
                            </div>
                            <button class="text-primary hover:text-primary-dark text-sm font-medium">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <button class="bg-primary text-white px-6 py-3 rounded-full font-bold hover:bg-opacity-90 transition">
                    View Full Menu
                </button>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Get your favorite food in just 3 simple steps</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="text-center p-6 bg-white rounded-xl shadow-md">
                    <div class="bg-primary bg-opacity-10 p-4 rounded-full inline-block mb-4">
                        <i data-feather="search" class="h-8 w-8 text-primary"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3">1. Choose Your Food</h3>
                    <p class="text-gray-600">Browse through our menu and select your favorite dishes</p>
                </div>
                
                <!-- Step 2 -->
                <div class="text-center p-6 bg-white rounded-xl shadow-md">
                    <div class="bg-primary bg-opacity-10 p-4 rounded-full inline-block mb-4">
                        <i data-feather="credit-card" class="h-8 w-8 text-primary"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3">2. Pay Online</h3>
                    <p class="text-gray-600">Secure payment with multiple options available</p>
                </div>
                
                <!-- Step 3 -->
                <div class="text-center p-6 bg-white rounded-xl shadow-md">
                    <div class="bg-primary bg-opacity-10 p-4 rounded-full inline-block mb-4">
                        <i data-feather="truck" class="h-8 w-8 text-primary"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-3">3. Fast Delivery</h3>
                    <p class="text-gray-600">Track your order in real-time until it arrives</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">What Our Customers Say</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Don't just take our word for it - hear from our happy customers</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <div class="flex mb-4">
                        <img src="http://static.photos/people/200x200/1" alt="Sarah J." class="w-12 h-12 rounded-full object-cover">
                        <div class="ml-4">
                            <h4 class="font-bold">Sarah J.</h4>
                            <div class="flex">
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">"The food arrived hot and fresh exactly when promised. The delivery person was very polite. Will definitely order again!"</p>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <div class="flex mb-4">
                        <img src="http://static.photos/people/200x200/2" alt="Michael T." class="w-12 h-12 rounded-full object-cover">
                        <div class="ml-4">
                            <h4 class="font-bold">Michael T.</h4>
                            <div class="flex">
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">"I'm impressed with how quickly my order was prepared and delivered. The app makes tracking the delivery so easy!"</p>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="bg-gray-50 p-6 rounded-xl">
                    <div class="flex mb-4">
                        <img src="http://static.photos/people/200x200/3" alt="Jessica L." class="w-12 h-12 rounded-full object-cover">
                        <div class="ml-4">
                            <h4 class="font-bold">Jessica L.</h4>
                            <div class="flex">
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                                <i data-feather="star" class="text-yellow-400 h-4 w-4"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">"The variety of restaurants available is amazing. I can order from my favorite places all in one app. Highly recommend!"</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-primary text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-6">Ready to satisfy your cravings?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Download our app now and get 20% off your first order!</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <button class="bg-white text-primary px-6 py-3 rounded-full font-bold hover:bg-opacity-90 transition">
                    <i data-feather="apple" class="inline mr-2"></i> App Store
                </button>
                <button class="bg-white text-primary px-6 py-3 rounded-full font-bold hover:bg-opacity-90 transition">
                    <i data-feather="play" class="inline mr-2"></i> Google Play
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i data-feather="truck" class="text-primary h-8 w-8"></i>
                        <span class="text-2xl font-bold">MunchMasters</span>
                    </div>
                    <p class="text-gray-400 mb-4">Delicious meals delivered fast to your doorstep</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i data-feather="facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i data-feather="twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i data-feather="instagram"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Careers</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Blog</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Press</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Contact Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">FAQs</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Delivery Areas</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Newsletter</h4>
                    <p class="text-gray-400 mb-4">Subscribe to get special offers and updates</p>
                    <div class="flex">
                        <input type="email" placeholder="Your email" class="bg-gray-800 text-white px-4 py-2 rounded-l focus:outline-none focus:ring-2 focus:ring-primary w-full">
                        <button class="bg-primary px-4 py-2 rounded-r hover:bg-opacity-90">
                            <i data-feather="send"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2023 MunchMasters Express. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Vanta.js background
        VANTA.NET({
            el: "body",
            mouseControls: true,
            touchControls: true,
            gyroControls: false,
            minHeight: 200.00,
            minWidth: 200.00,
            scale: 1.00,
            scaleMobile: 1.00,
            color: 0x4ECDC4,
            backgroundColor: 0xf8fafc,
            points: 10.00,
            maxDistance: 20.00,
            spacing: 15.00
        });

        // Initialize Feather Icons
        feather.replace();
        
        // Simple cart animation
        document.addEventListener('DOMContentLoaded', function() {
            const cartButtons = document.querySelectorAll('[data-feather="shopping-cart"]');
            cartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const cart = this.closest('button');
                    cart.classList.add('animate-ping');
                    setTimeout(() => {
                        cart.classList.remove('animate-ping');
                    }, 500);
                });
            });
        });
    </script>
</body>
</html>
