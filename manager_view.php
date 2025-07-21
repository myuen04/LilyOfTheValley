<?php
session_start();
require('database.php');

if (!isset($_SESSION['userId'])) {
    // Redirect to login if not logged in
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['userId'];

$section = isset($_GET['section']) ? $_GET['section'] : 'home';

$searchterm = isset($_GET['searchterm']) ? trim($_GET['searchterm']) : '';


if ($section === 'employees') {
    if ($searchterm) {
        $searchterm_esc = $db->real_escape_string($searchterm);
        $sql = "SELECT * FROM Users WHERE (FirstName LIKE '%$searchterm_esc%' OR LastName LIKE '%$searchterm_esc%') AND Role = 'Employee'";
    } else {
        $sql = "SELECT * FROM Users WHERE Role = 'Employee'";
    }
    $result = $db->query($sql);
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
      <a href="manager_view.php?section=home" class="nav-button <?= (!isset($_GET['section']) || $_GET['section'] === 'home') ? 'active' : '' ?>">Home</a>
      <a href="manager_view.php?section=employees" class="nav-button <?= ($_GET['section'] ?? '') === 'employees' ? 'active' : '' ?>">Manage Employees</a>
      <a href="logout.php" class="nav-button">Logout</a>
    </div>
  </div>
</nav>

<div id="app">

  <?php if ($section === 'home'): ?>
    <section id="education">
      <h2>About the Flower</h2>
      <div id="flower-info">
        <p>The Lily of the Valley is a fragrant flower known for purity and joy. Our curated collection features skincare, decor, and lifestyle accessories, each delicately themed with this iconic botanical. Explore our offerings for a touch of elegance in your everyday routine.</p>
      </div>
    </section>

  <?php elseif ($section === 'employees'): ?>
    <section id="manageEmployees">
      <h2>Manage Employees</h2>

      <?php if (isset($_GET['error']) && $_GET['error'] === 'not_logged_in'): ?>
        <div class="popup-overlay">
          <div class="popup-box">
            <p>You must be logged in to access this page.</p>
            <a href="index.php" class="popup-close">OK</a>
          </div>
        </div>
      <?php endif; ?>

      <div class="search-container">
        <form method="get" action="manager_view.php">
          <input type="hidden" name="section" value="employees" />
          
          <input type="text" name="searchterm" placeholder="Search..." value="<?= htmlspecialchars($searchterm) ?>">
          <button type="submit">Search</button>
        </form>
      </div>

      <table id="employeeResults">
        <?php if ($result && $result->num_rows > 0): ?>
        <tr>
            <th>User ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>User Name</th>
            <th>Address</th>
            <th>Phone Number</th>
            <th>Email</th>
            <th>Password</th>
            <th>Role</th>
            
        </tr>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="employee-result">
              
              <td><?php echo htmlspecialchars($row['UserID']); ?></td>
                    <td><?php echo htmlspecialchars($row['FirstName']); ?></td>
                    <td><?php echo htmlspecialchars($row['LastName']); ?></td>
                    <td><?php echo htmlspecialchars($row['UserName']); ?></td>
                    <td><?php echo htmlspecialchars($row['Address']); ?></td>
                    <td><?php echo htmlspecialchars($row['PhoneNumber']); ?></td>
                    <td><?php echo htmlspecialchars($row['Email']); ?></td>
                    <!-- Display masked password -->
                    <td>*****</td>
                    <td><?php echo htmlspecialchars($row['Role']); ?></td>
                    
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No products found.</p>
        <?php endif; ?>
      </table>
    </section>

  <?php elseif ($section === 'cart'): ?>
    <section id="cart">
      <h2>Your Cart</h2>
      <p>Your cart is empty (placeholder).</p>
    </section>

  <?php endif; ?>
</div>

</body>
</html>
