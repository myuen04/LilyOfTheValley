<?php
session_start();
require('database.php');

if (!isset($_SESSION['userId'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['userId'];
$customerId = $_SESSION['CustomerID'] ?? null;
$section = $_GET['section'] ?? 'home';

$searchtype = $_GET['searchtype'] ?? '';
$searchterm = trim($_GET['searchterm'] ?? '');
$validColumns = ['ProductName', 'Category'];

if (!in_array($searchtype, $validColumns)) {
    $searchtype = '';
    $searchterm = '';
}

if ($section === 'shop') {
    if ($searchtype && $searchterm) {
        $searchtype_esc = $db->real_escape_string($searchtype);
        $searchterm_esc = $db->real_escape_string($searchterm);
        $sql = "SELECT * FROM Product WHERE $searchtype_esc LIKE '%$searchterm_esc%'";
    } else {
        $sql = "SELECT * FROM Product";
    }
    $result = $db->query($sql);
}

if ($section === 'cart' && $customerId) {
    $cartItemsQuery = "
        SELECT ci.CartItemID, p.ProductName, p.Category, ci.Quantity, ci.UnitPrice, (ci.Quantity * ci.UnitPrice) AS Total
        FROM CartItem ci
        JOIN Product p ON ci.ProductID = p.ProductID
        JOIN Cart c ON ci.CartID = c.CartID
        WHERE c.CustomerID = $customerId
    ";
    $cartItems = $db->query($cartItemsQuery);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Flower Shop</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<nav class="navbar">
  <div class="nav-container">
    <div class="nav-links">
      <a href="user_view.php?section=home" class="nav-button <?= $section === 'home' ? 'active' : '' ?>">Home</a>
      <a href="user_view.php?section=shop" class="nav-button <?= $section === 'shop' ? 'active' : '' ?>">Shop</a>
      <a href="user_view.php?section=cart" class="nav-button <?= $section === 'cart' ? 'active' : '' ?>">Cart</a>
    </div>
    <div class="nav-links">
      <a href="logout.php" class="nav-button logout-button">Logout</a>
    </div>
  </div>
</nav>

<div id="app">
<?php if ($section === 'home'): ?>

  <section id="home-hero" style="background: linear-gradient(to right, #f9f9f9, #f0f0f0); padding: 60px 30px; text-align: center; border-radius: 16px;">
      
       <img src="./img/logo.png" alt="Lily of the Valley Logo" style="width: 200px; height: auto; margin-bottom: 20px;">
    <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--primary-color); margin-bottom: 20px;">
      Welcome to the Lily of the Valley
    </h1>
    <div class="flower-info">
    <h2>Flower Information</h2>
        <?php require('flowerInfo.php');
            if ($flowerInfo && $flowerInfo->num_rows > 0) {
        $row = $flowerInfo->fetch_assoc();
        echo "<h3>" . htmlspecialchars($row["Name"]) ."<span style='font-weight:normal;'>(".htmlspecialchars($row["ScientificName"]).")</span>"."</h3>";
        echo "<p style='font-size: 1.1rem; max-width: 700px; margin: 0 auto 30px auto; color: var(--dark-color); line-height: 1.6;'>" . htmlspecialchars($row["Description"]) . "</p>";
        echo "<p><strong> Regions:</strong> ".htmlspecialchars($row["Region1"]).", ".htmlspecialchars($row["Region2"]).", and ".htmlspecialchars($row["Region2"]);
        echo "<h2>Facts about Lily of the valley's</h2>";
        echo "<ul><li>".htmlspecialchars($row["Fact1"])."</li><li>".htmlspecialchars($row["Fact2"]). "</li></ul>";
        } else {
            echo "<p>No flower information available.</p>";
        }?>
    </div>
    <a href="user_view.php?section=shop" class="submit-order-button" style="text-decoration: none;">Shop Now</a>
    <div style="margin-top: 40px;">

    </div>
  </section>


  <?php elseif ($section === 'shop'): ?>
    <section id="shop">
      <h2>Shop Products</h2>

      <?php if (isset($_GET['error']) && $_GET['error'] === 'not_logged_in'): ?>
        <div class="popup-overlay"><div class="popup-box">
          <p>You must be logged in to add items to your cart.</p>
          <a href="user_view.php?section=shop" class="popup-close">OK</a>
        </div></div>
      <?php elseif (isset($_GET['success']) && $_GET['success'] === 'added'): ?>
        <div class="popup-overlay success"><div class="popup-box">
          <p>Product added to cart successfully!</p>
          <a href="user_view.php?section=shop" class="popup-close">OK</a>
        </div></div>
      <?php endif; ?>

      <div class="search-container">
        <form method="get" action="user_view.php">
          <input type="hidden" name="section" value="shop" />
          <select name="searchtype">
            <option value="ProductName" <?= $searchtype === 'ProductName' ? 'selected' : '' ?>>Product Name</option>
            <option value="Category" <?= $searchtype === 'Category' ? 'selected' : '' ?>>Category</option>
          </select>
          <input type="text" name="searchterm" placeholder="Search..." value="<?= htmlspecialchars($searchterm) ?>">
          <button type="submit">Search</button>
        </form>
      </div>

      <div id="product-grid">
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product-card">
              <?php if (!empty($row['Picture'])): ?>
                <img src="<?= htmlspecialchars($row['Picture']) ?>" alt="<?= htmlspecialchars($row["ProductName"]) ?>" class="product-img">
              <?php endif; ?>
              <h3><?= htmlspecialchars($row["ProductName"]) ?></h3>
              <span><?= htmlspecialchars($row["Category"]) ?></span>
              <p>$<?= htmlspecialchars($row["Price"]) ?></p>
              <form method="post" action="addToCart.php">
                <input type="hidden" name="ProductID" value="<?= htmlspecialchars($row['ProductID']) ?>">
                <button class="addToCart" type="submit">Add to Cart</button>
              </form>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No products found.</p>
        <?php endif; ?>
      </div>
    </section>

  <?php elseif ($section === 'cart'): ?>
    <section id="cart">
      <h2>Your Cart</h2>
      <?php if (isset($cartItems) && $cartItems->num_rows > 0): ?>
        <form method="post" action="updateCart.php">
        <table>
          <thead>
            <tr>
              <th>Product</th>
              <th>Category</th>
              <th>Quantity</th>
              <th>Unit Price</th>
              <th>Total</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $grandTotal = 0; ?>
            <?php while ($item = $cartItems->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($item['ProductName']) ?></td>
                <td><?= htmlspecialchars($item['Category']) ?></td>
                <td>
                  <input type="number" name="quantities[<?= $item['CartItemID'] ?>]" value="<?= $item['Quantity'] ?>" min="1" />
                </td>
                <td>$<?= number_format($item['UnitPrice'], 2) ?></td>
                <td>$<?= number_format($item['Total'], 2) ?></td>
                <td>
                  <button type="submit" name="delete" value="<?= $item['CartItemID'] ?>" onclick="return confirm('Remove this item?')">Delete</button>
                </td>
              </tr>
              <?php $grandTotal += $item['Total']; ?>
            <?php endwhile; ?>
            <?php
              $salesTax = $grandTotal * 0.06625;
              $totalWithTax = $grandTotal + $salesTax;
            ?>
            <tr>
              <td colspan="4" style="text-align: right;"><strong>Sales Tax (6.625%):</strong></td>
              <td><strong>$<?= number_format($salesTax, 2) ?></strong></td>
              <td></td>
            </tr>
            <tr>
              <td colspan="4" style="text-align: right;"><strong>Total with Tax:</strong></td>
              <td><strong>$<?= number_format($totalWithTax, 2) ?></strong></td>
              <td></td>
            </tr>
          </tbody>
        </table>
        <br>
        <button type="submit" name="update" class="cart-button">Update Cart</button>
        <button type="submit" formaction="Order.php" class="submit-order-button">Submit Order</button>
        </form>
      <?php else: ?>
        <p>Your cart is empty.</p>
      <?php endif; ?>
    </section>
  <?php endif; ?>
</div>
</body>
</html>
