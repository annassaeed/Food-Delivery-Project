<?php
// Set response header to JSON first to prevent encoding issues if an early error occurs
header('Content-Type: application/json');

// --- AGGRESSIVE TRY-CATCH BLOCK FOR DEBUGGING ---
try {
    // WARNING: If db_connect.php fails here, this script will still crash 
    // unless the database error handler in db_connect.php returns JSON or exits cleanly.
    include 'db_connect.php'; 

    // Ensure user is authenticated (using session_start from db_connect.php)
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated. Please log in.');
    }

    // 1. Get user and posted JSON data
    $user_id = $_SESSION['user_id'];
    // Function get_user_data must be defined in db_connect.php
    $user = get_user_data($conn); 
    $data = json_decode(file_get_contents('php://input'), true);

    // Basic validation checks
    if (
        !isset($data['restaurant_id']) || 
        !isset($data['total_amount']) || 
        !is_numeric($data['total_amount']) || 
        empty($user['address'])
    ) {
        throw new Exception('Invalid or incomplete order data (missing address, restaurant ID, or total).');
    }

    $restaurant_id = (int)$data['restaurant_id'];
    $total_amount = (float)$data['total_amount'];
    $delivery_address = $user['address'];
    $status = 'Preparing';

    // Start a transaction for database integrity
    $conn->begin_transaction();

    try {
        // 2. Insert main order into 'orders' table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, restaurant_id, total_amount, delivery_address, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iids", $user_id, $restaurant_id, $total_amount, $delivery_address, $status);
        
        if (!$stmt->execute()) {
            throw new Exception("SQL Insertion Failed: " . $stmt->error);
        }
        
        $order_id = $conn->insert_id;
        $stmt->close();
        
        // Commit the transaction if everything succeeded
        $conn->commit();
        
        // Return success response
        echo json_encode(['status' => 'success', 'message' => 'Order placed successfully!', 'order_id' => $order_id]);

    } catch (Exception $e) {
        // SQL transaction failure
        $conn->rollback();
        throw new Exception("Transaction failed: " . $e->getMessage());
    }

} catch (Exception $e) {
    // Catch all errors (including failed authentication or initial data validation)
    
    // Log detailed error to the server log
    error_log("PROCESS ORDER FATAL ERROR: " . $e->getMessage());
    
    // Return the error message directly in the JSON response
    echo json_encode(['status' => 'error', 'message' => 'Critical Server Error: ' . $e->getMessage()]);
    
} finally {
    // Close connection if it was successfully opened
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>