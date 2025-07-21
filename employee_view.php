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

$sql = "SELECT * FROM Users WHERE UserID = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
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
    <!-- Logo on the left -->
    <div class="nav-logo">
      <a href="employee_view.php?section=home">
        <img src="./img/logo.png" alt="Logo" class="navbar-logo">
      </a>
    </div>

    <!-- Navigation links -->
    <div class="nav-links">
      <a href="employee_view.php?section=home" class="nav-button <?= $section === 'home' ? 'active' : '' ?>">Home</a>
      <a href="employee_view.php?section=employees" class="nav-button <?= $section === 'employees' ? 'active' : '' ?>">View Your Information</a>
      <a href="logout.php" class="nav-button">Logout</a>
    </div>
  </div>
</nav>

<div id="app">

<?php if ($section === 'home'): ?>
  <section id="home-hero" style="background: linear-gradient(to right, #f9f9f9, #f0f0f0); padding: 60px 30px; text-align: center; border-radius: 16px;">
    
    <!-- Logo image -->
    <img src="./img/logo.png" alt="Main Flower" class="main-flower-image">


    <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--primary-color); margin-bottom: 20px;">
      Welcome to the Lily of the Valley
    </h1>

    <?php 
      require('flowerInfo.php');
      if ($flowerInfo && $flowerInfo->num_rows > 0) {
        $row = $flowerInfo->fetch_assoc();
        echo "<h2>" . htmlspecialchars($row["Name"]) . "</h2>";
        echo "<p style='font-size: 1.1rem; max-width: 700px; margin: 0 auto 30px auto; color: var(--dark-color); line-height: 1.6;'>" . htmlspecialchars($row["Description"]) . "</p>";
      } else {
        echo "<p>No flower information available.</p>";
      }
    ?>
  </section>

<?php elseif ($section === 'employees'): ?>
  <section id="manageEmployees">
    <h2>View Your Information</h2>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'not_logged_in'): ?>
      <div class="popup-overlay">
        <div class="popup-box">
          <p>You must be logged in to access this page.</p>
          <a href="index.php" class="popup-close">OK</a>
        </div>
      </div>
    <?php endif; ?>

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
          <td><?= htmlspecialchars($row['UserID']) ?></td>
          <td><?= htmlspecialchars($row['FirstName']) ?></td>
          <td><?= htmlspecialchars($row['LastName']) ?></td>
          <td><?= htmlspecialchars($row['UserName']) ?></td>
          <td><?= htmlspecialchars($row['Address']) ?></td>
          <td><?= htmlspecialchars($row['PhoneNumber']) ?></td>
          <td><?= htmlspecialchars($row['Email']) ?></td>
          <td>*****</td>
          <td><?= htmlspecialchars($row['Role']) ?></td>
        </tr>
        <?php endwhile; ?>
      <?php endif; ?>
    </table>
  </section>

<?php elseif ($section === 'cart'): ?>
  <section id="cart">
    <h2>Your Cart</h2>
    <p>Your cart is empty.</p>
  </section>
<?php endif; ?>

</div>
</body>
</html>
