<?php
session_start();
require('database.php');

// Check if the user is logged in as a customer
if (!isset($_SESSION['CustomerID'])) {
    header("Location: user_view.php?section=shop&error=not_logged_in");
    exit();
}

$customerId = $_SESSION['CustomerID'];

// Only process POST requests with a ProductID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ProductID'])) {
    $productId = intval($_POST['ProductID']);

    // Step 1: Get product price
    $productResult = $db->query("SELECT Price FROM Product WHERE ProductID = $productId");
    if ($productResult->num_rows === 0) {
        die("Product not found.");
    }
    $unitPrice = $productResult->fetch_assoc()['Price'];

    // Step 2: Find or create cart
    $cartResult = $db->query("SELECT CartID FROM Cart WHERE CustomerID = $customerId");
    if ($cartResult->num_rows > 0) {
        $cartId = $cartResult->fetch_assoc()['CartID'];
    } else {
        // Create a new cart if one doesn't exist
        $db->query("INSERT INTO Cart (CustomerID, CurrentTotal) VALUES ($customerId, 0)");
        $cartId = $db->insert_id; // Auto-increment ID
    }

    // Step 3: Add or update CartItem
    $itemResult = $db->query("SELECT Quantity FROM CartItem WHERE CartID = $cartId AND ProductID = $productId");
    if ($itemResult->num_rows > 0) {
        $db->query("UPDATE CartItem SET Quantity = Quantity + 1 WHERE CartID = $cartId AND ProductID = $productId");
    } else {
        $stmt = $db->prepare("INSERT INTO CartItem (CartID, ProductID, Quantity, UnitPrice) VALUES (?, ?, 1, ?)");
        $stmt->bind_param("iid", $cartId, $productId, $unitPrice);
        $stmt->execute();
    }

    // Step 4: Update total in Cart
    $totalQuery = $db->query("SELECT SUM(UnitPrice * Quantity) AS total FROM CartItem WHERE CartID = $cartId");
    $newTotal = $totalQuery->fetch_assoc()['total'];
    $db->query("UPDATE Cart SET CurrentTotal = $newTotal WHERE CartID = $cartId");

    // Step 5: Redirect with confirmation
    header("Location: user_view.php?section=shop&success=added");
    exit();
}
?>
