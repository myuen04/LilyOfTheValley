<?php
session_start(); // Start session at the top

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    require('database.php');

    $sql = 'SELECT * FROM Users WHERE UserName=?';
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['Password'])) {
            $role = $user['Role'];
            $userId = $user['UserID'];

            $_SESSION['userId'] = $userId;
            $_SESSION['role'] = $role;

            if ($role === 'Manager') {
                session_start();
                $_SESSION['userId'] = $userId;
                header("Location: manager_view.php");
                exit();

            } elseif ($role === 'Customer') {
                session_start();
                $_SESSION['userId'] = $userId;
                $_SESSION['CustomerID'] = $userId;
                header("Location: user_view.php");
                exit();
            }elseif ($role === 'Admin') {
                session_start();
                $_SESSION['userId'] = $userId;
                header("Location: admin_view.php");
                exit();
            }elseif ($role === 'Employee') {
                session_start();
                $_SESSION['userId'] = $userId;
                header("Location: employee_view.php");
                exit();
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flower Shop Login</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&display=swap');
    :root {
      --primary-color: #6b705c;
      --accent-color: #a5a58d;
      --light-color: #e6e6fa;
      --dark-color: #2f3e46;
    }
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--light-color);
      color: var(--dark-color);
    }
    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-box {
      background: white;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
      width: 340px;
    }
    form {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    input, button {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    button {
      background-color: var(--primary-color);
      color: white;
      border: none;
      cursor: pointer;
    }
    button:hover { background-color: #556052; }
    h2 {
      text-align: center;
      color: var(--primary-color);
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>
      <h2>Login</h2>
      <form method="POST" action="index.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <p>Don't have an account? Register <a href="registration.php">here</a>.</p>
      </form>
    </div>
  </div>
</body>
</html>
