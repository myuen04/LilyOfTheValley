<?php
session_start();
require('database.php');

if (!isset($_SESSION['CustomerID'])) {
    header("Location: index.php");
    exit();
}

$customerId = $_SESSION['CustomerID'];

// Get cart info
$cartQuery = $db->query("SELECT * FROM Cart WHERE CustomerID = $customerId");
$cart = $cartQuery->fetch_assoc();
$cartId = $cart['CartID'];
$subtotal = $cart['CurrentTotal'];
$orderDate = date('Y-m-d');
$status = 'processed';

// Calculate tax and total
$salesTax = $subtotal * 0.06625;
$totalWithTax = $subtotal + $salesTax;

// Insert order with taxed total
$db->query("INSERT INTO Orders (OrderDate, CustomerID, CartID, TotalAmount, Status)
            VALUES ('$orderDate', $customerId, $cartId, $totalWithTax, '$status')");

$orderId = $db->insert_id;

// Get ordered items (capture before deletion)
$itemsQuery = $db->query("
    SELECT p.ProductName, ci.Quantity, ci.UnitPrice, (ci.Quantity * ci.UnitPrice) AS Total
    FROM CartItem ci
    JOIN Product p ON ci.ProductID = p.ProductID
    WHERE ci.CartID = $cartId
");

// Clear cart
$db->query("DELETE FROM CartItem WHERE CartID = $cartId");
$db->query("DELETE FROM Cart WHERE CartID = $cartId");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Confirmation</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .receipt {
      background: white;
      padding: 40px;
      max-width: 600px;
      margin: 40px auto;
      border-radius: 12px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }

    .receipt h2 {
      text-align: center;
      color: var(--primary-color);
      margin-bottom: 20px;
    }

    .receipt-details {
      margin-bottom: 20px;
      font-size: 0.95rem;
      color: var(--dark-color);
    }

    .receipt table {
      width: 100%;
      border-collapse: collapse;
    }

    .receipt th, .receipt td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
    }

    .receipt th {
      background-color: var(--light-color);
      color: var(--dark-color);
    }

    .receipt .total {
      font-weight: bold;
      text-align: right;
    }

    .btn-home {
      display: block;
      text-align: center;
      margin-top: 30px;
    }

    .btn-home a {
      background-color: var(--primary-color);
      color: white;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
    }

    .btn-home a:hover {
      background-color: #556052;
    }
  </style>
</head>
<body>

<div class="receipt">
  <h2>Order Successful!</h2>

  <div class="receipt-details">
    <p><strong>Order ID:</strong> <?= $orderId ?></p>
    <p><strong>Order Date:</strong> <?= $orderDate ?></p>
    <p><strong>Status:</strong> <?= ucfirst($status) ?></p>
    <p><strong>Customer ID:</strong> <?= $customerId ?></p>
    <p><strong>Cart ID:</strong> <?= $cartId ?></p>
  </div>

  <table>
    <thead>
      <tr>
        <th>Product</th>
        <th>Qty</th>
        <th>Unit Price</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php $grandTotal = 0; ?>
      <?php while ($item = $itemsQuery->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($item['ProductName']) ?></td>
          <td><?= $item['Quantity'] ?></td>
          <td>$<?= number_format($item['UnitPrice'], 2) ?></td>
          <td>$<?= number_format($item['Total'], 2) ?></td>
        </tr>
        <?php $grandTotal += $item['Total']; ?>
      <?php endwhile; ?>
      <tr>
        <td colspan="3" class="total">Sales Tax (6.625%):</td>
        <td><strong>$<?= number_format($salesTax, 2) ?></strong></td>
      </tr>
      <tr>
        <td colspan="3" class="total">Total with Tax:</td>
        <td><strong>$<?= number_format($totalWithTax, 2) ?></strong></td>
      </tr>
    </tbody>
  </table>

  <div class="btn-home">
    <a href="user_view.php?section=home">Return to Home</a>
  </div>
</div>

</body>
</html>
