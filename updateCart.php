<?php
session_start();
require('database.php');

if (!isset($_SESSION['CustomerID'])) {
    header("Location: user_view.php?section=cart&error=not_logged_in");
    exit();
}

$customerId = $_SESSION['CustomerID'];

// Get the user's cart ID
$cartIdResult = $db->query("SELECT CartID FROM Cart WHERE CustomerID = $customerId");
$cartId = null;

if ($cartIdResult && $cartIdResult->num_rows > 0) {
    $cartId = intval($cartIdResult->fetch_assoc()['CartID']);
}

// Delete item if requested
if (isset($_POST['delete'])) {
    $cartItemId = intval($_POST['delete']);
    $db->query("DELETE FROM CartItem WHERE CartItemID = $cartItemId");
}

// Update quantities if requested
if (isset($_POST['update']) && isset($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $cartItemId => $quantity) {
        $cartItemId = intval($cartItemId);
        $quantity = max(1, intval($quantity));
        $db->query("UPDATE CartItem SET Quantity = $quantity WHERE CartItemID = $cartItemId");
    }
}

// Recalculate and update cart total
if ($cartId !== null) {
    $totalQuery = $db->query("SELECT SUM(UnitPrice * Quantity) AS total FROM CartItem WHERE CartID = $cartId");
    $newTotal = $totalQuery->fetch_assoc()['total'] ?? 0;
    $db->query("UPDATE Cart SET CurrentTotal = $newTotal WHERE CartID = $cartId");
}

// Redirect back to cart with success message
header("Location: user_view.php?section=cart&success=updated");
exit();
?>
