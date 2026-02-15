<?php
include 'db_connect.php'; // Includes the database connection and session_start()

$error = "";
$success = "";

// When form submits
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        // 1. Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "This email is already registered. Please log in.";
        } else {
            // 2. *** REVERTED TO SECURE HASHING ***
            // Passwords are now stored securely using irreversible bcrypt hashing.
            // This is mandatory for security, but requires login.php to use password_verify().
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); 

            // 3. Insert new user into database
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, address, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $address, $hashed_password);
            
            if ($stmt->execute()) {
                
                // --- MODIFICATION START ---
                // Redirect user to login.php with a success parameter instead of auto-logging in.
                header("Location: login.php?registration=success");
                exit();
                // --- MODIFICATION END ---
                
            } else {
                $error = "Registration failed. Please try again.";
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
    <title>Register | MunchMasters Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .register-bg {
            background: linear-gradient(135deg, rgba(255,107,107,0.1) 0%, rgba(78,205,196,0.1) 100%);
        }
        .bg-primary { background-color: #FF6B6B; }
        .text-primary { color: #FF6B6B; }
        .focus\:ring-primary:focus { --tw-ring-color: #FF6B6B; }
    </style>
</head>
<body class="font-sans register-bg min-h-screen flex items-center">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-primary text-white p-6 text-center">
                <i data-feather="user-plus" class="h-10 w-10 mx-auto mb-4"></i>
                <h1 class="text-2xl font-bold">Create Account</h1>
                <p class="text-white opacity-80">Join MunchMasters today</p>
                <div class="mt-4 flex justify-center space-x-4">
                    <a href="index.html" class="text-white underline hover:opacity-75">Home</a>
                    <a href="login.php" class="text-white underline hover:opacity-75">Login</a>
                </div>
            </div>
            <div class="p-6">
                <?php if (!empty($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="register.php">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 mb-2">Full Name</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="mb-4">
                        <label for="phone" class="block text-gray-700 mb-2">Phone Number (Optional)</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="mb-4">
                        <label for="address" class="block text-gray-700 mb-2">Delivery Address (Optional)</label>
                        <textarea id="address" name="address" rows="3"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" required 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="mb-6">
                        <label for="confirm_password" class="block text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-bold hover:bg-opacity-90 transition mb-4">
                        Register
                    </button>
                </form>
            </div>
            <div class="bg-gray-50 p-4 text-center">
                <p class="text-gray-600">Already have an account? <a href="login.php" class="text-primary font-medium hover:underline">Login here</a></p>
                <p class="text-xs text-gray-400 mt-2">
                    <a href="forgot_password.php" class="hover:underline">Forgot Password?</a>
                </p>
            </div>
        </div>
    </div>
    <script>
        feather.replace();
    </script>
</body>
</html>