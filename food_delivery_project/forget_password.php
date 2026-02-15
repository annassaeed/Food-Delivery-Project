<?php
include 'db_connect.php'; 

// Stage 1: Collect Email (default)
// Stage 2: Reset Form (after successful email submission)
$stage = 'email'; 
$message = "";

// Handle Email Submission (Stage 1)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email_submit'])) {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $message = "Please enter your email address.";
    } else {
        // 1. Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];
            
            // 2. Simulate Token Generation (Token is a random, temporary hash)
            // In a real application, this token would be stored in a separate table 
            // with an expiry time, and sent via email.
            $reset_token = bin2hex(random_bytes(16));
            
            // 3. For Canvas simulation, we skip the email and move straight to the reset stage
            // We pass the token via a hidden field (in a real app, this would be a URL parameter)
            $stage = 'reset';
            $message = "Email confirmed. Please enter your new password below.";
        } else {
            // Use a vague message for security (don't reveal if email exists)
            $message = "If the email is valid, a reset link would be sent.";
        }
        $stmt->close();
    }
}

// Handle Password Reset Submission (Stage 2)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_submit'])) {
    $reset_token = $_POST['reset_token'] ?? '';
    $email = $_POST['email'] ?? '';
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // In a real app, you would validate the token against the database here.
    // For this simulation, we check passwords and update based on the submitted email.

    if ($new_password !== $confirm_password) {
        $message = "Passwords do not match!";
        $stage = 'reset'; // Stay on reset form
    } elseif (strlen($new_password) < 8) {
        $message = "New password must be at least 8 characters long.";
        $stage = 'reset';
    } else {
        // Find user by email (as token validation is skipped)
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // 3. Update the user's password securely
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);
            
            if ($stmt->execute()) {
                // Success! Redirect to login
                header("Location: login.php?reset_success=1");
                exit();
            } else {
                $message = "Password update failed. Please try again.";
                $stage = 'reset';
            }
        } else {
             $message = "Invalid reset request.";
        }
        $stmt->close();
    }
}

// Get success message from redirect
if (isset($_GET['reset_success'])) {
    $message = "Your password has been successfully reset. Please log in.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | MunchMasters Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .page-bg {
            background: linear-gradient(135deg, rgba(255,107,107,0.1) 0%, rgba(78,205,196,0.1) 100%);
        }
        .bg-primary { background-color: #FF6B6B; }
        .text-primary { color: #FF6B6B; }
        .focus\:ring-primary:focus { --tw-ring-color: #FF6B6B; }
    </style>
</head>
<body class="font-sans page-bg min-h-screen flex items-center">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden">
            
            <div class="bg-primary text-white p-6 text-center">
                <i data-feather="lock" class="h-10 w-10 mx-auto mb-4"></i>
                <h1 class="text-2xl font-bold">
                    <?php echo $stage === 'email' ? 'Forgot Password' : 'Reset Password'; ?>
                </h1>
                <p class="text-white opacity-80">
                    <?php echo $stage === 'email' ? 'Enter your email to receive a reset link.' : 'Set your new secure password.'; ?>
                </p>
            </div>
            
            <div class="p-6">
                <?php if (!empty($message)): ?>
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($stage === 'email'): ?>
                    <!-- Stage 1: Email Input Form -->
                    <form method="POST" action="forgot_password.php">
                        <div class="mb-6">
                            <label for="email" class="block text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <button type="submit" name="email_submit" class="w-full bg-primary text-white py-3 rounded-lg font-bold hover:bg-opacity-90 transition mb-4">
                            Submit Email
                        </button>
                    </form>

                <?php elseif ($stage === 'reset'): ?>
                    <!-- Stage 2: New Password Form -->
                    <form method="POST" action="forgot_password.php">
                        <!-- Hidden fields to retain context -->
                        <input type="hidden" name="reset_token" value="<?php echo htmlspecialchars($reset_token ?? ''); ?>">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">

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
                        
                        <button type="submit" name="reset_submit" class="w-full bg-primary text-white py-3 rounded-lg font-bold hover:bg-opacity-90 transition mb-4">
                            Reset Password
                        </button>
                    </form>

                <?php endif; ?>
            </div>

            <div class="bg-gray-50 p-4 text-center">
                <p class="text-gray-600">Remembered your password? <a href="login.php" class="text-primary font-medium hover:underline">Log in here</a></p>
            </div>
        </div>
    </div>
    <script>
        feather.replace();
    </script>
</body>
</html>