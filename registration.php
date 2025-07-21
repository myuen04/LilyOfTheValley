<?php
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phoneNum = $_POST['phoneNum'];
    $role = 'Customer';

    require('database.php');
    
    
        $sql = "INSERT INTO Users (FirstName, LastName, UserName, Password, Email, Role, Address, PhoneNumber)
            VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssssssss", $firstName, $lastName, $username, $password, $email, $role, $address, $phoneNum);
        if($stmt->execute()){
            $userId = $stmt->insert_id;
            
            $customerSql = "INSERT INTO Customer (UserID, ShippingAddress) VALUES (?,?)";
            $customerStmt = $db->prepare($customerSql);
            $customerStmt->bind_param("is", $userId, $address);
            $customerStmt->execute();
            
            header("Location: index.php");
            exit();

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
    
    form{
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
    #app {
      padding: 40px;
      max-width: 1000px;
      margin: auto;
    }
    section {
      margin-bottom: 40px;
      background: white;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }
    ul { list-style: none; padding: 0; }
    li { padding: 10px 0; border-bottom: 1px solid #ddd; }
    li:last-child { border-bottom: none; }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }
    #flower-info {
      font-family: 'Playfair Display', cursive;
      font-size: 18px;
      line-height: 1.6;
    }
  </style>
</head>
    <body>
      <div class="login-container">
        
        

        <div class="login-box">
            <?php if ($error): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
          <h2>Registration</h2>
          <form method="POST" action="registration.php">
             <input type="text" name="firstName" placeholder="First Name" required>
             <input type="text" name="lastName" placeholder="Last Name" required>
             <input type="email" name="email" placeholder="Email" required>
             <input type="text" name="address" placeholder="Address" required>
             <input type="text" name="phoneNum" placeholder="Phone Number" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
            <p>Already have an account register? <a href="index.php">login</a></p>
          </form>
        </div>
      </div>
    </body>
</html>
