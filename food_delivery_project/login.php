<?php
include 'db_connect.php'; 

$error = "";
$success = "";

// Check for successful registration redirect
if (isset($_GET['registration']) && $_GET['registration'] === 'success') {
    $success = "Registration successful! Please log in with your new account.";
}

// When form submits
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        // Query user: Select ID and HASHED password
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];

            // *** SECURE PASSWORD CHECK (using password_verify) ***
            if (password_verify($password, $hashed_password)) {
                // Success! Set session and redirect
                session_start();
                $_SESSION['user_id'] = $row['id'];
                header("Location: dashboard.php");
                exit();
            } else {
                // Generic error message for security
                $error = "Incorrect email or password.";
            }
        } else {
            // Email not found (use generic error message for security)
            $error = "Incorrect email or password.";
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
    <title>Login | MunchMasters Express</title>
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
                <i data-feather="log-in" class="h-10 w-10 mx-auto mb-4"></i>
                <h1 class="text-2xl font-bold">Welcome Back</h1>
                <p class="text-white opacity-80">Sign in to order your favorite meals</p>
                <div class="mt-4 flex justify-center space-x-4">
                    <a href="index.html" class="text-white underline hover:opacity-75">Home</a>
                    <a href="register.php" class="text-white underline hover:opacity-75">Register</a>
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
                
                <form method="POST" action="login.php">
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" required 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-bold hover:bg-opacity-90 transition mb-4">
                        Login
                    </button>
                </form>
            </div>
            <div class="bg-gray-50 p-4 text-center">
                <p class="text-gray-600">Don't have an account? <a href="register.php" class="text-primary font-medium hover:underline">Register here</a></p>
                <!-- Retained Password Reset link structure for user convenience -->
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