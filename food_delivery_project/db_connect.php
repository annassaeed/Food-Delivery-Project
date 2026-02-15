<?php
// Database connection settings
// CRITICAL FIX: The MySQL server is running on port 3307 (as per your my.cnf).
$servername = "localhost";
$username = "root";   
// The error confirms MySQL is being accessed with NO password.
$password = "";       
$database = "food_delivery_db"; 
$port = 3307; // <--- ADDED: Use the port defined in your my.cnf

// Create connection, explicitly passing the port number
$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    // If connection fails, show a clear, instructional message
    die("<h1>Database Connection Error</h1>
         <p><strong>Status:</strong> Could not connect to the database: " . $conn->connect_error . "</p>
         <p><strong>Action Required:</strong> Please ensure your MySQL service is running and that the 
         <code>\$password</code> variable in <code>db_connect.php</code> is correct.</p>");
}

// Start session here for convenience in all included files
session_start();

// Redirect unauthenticated users if trying to access protected pages
function check_auth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Function to fetch user data (used by dashboard, order, restaurant pages)
function get_user_data($conn) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT id, name, email, phone, address FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
    }
    return null;
}
?>