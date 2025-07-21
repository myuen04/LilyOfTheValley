<?php
// Start the session (if not already started)
session_start();
if (!isset($_SESSION['userId'])) {
    // Redirect to login if not logged in
    header("Location: index.php");
    exit();
}
$userId = $_SESSION['userId'];

// Include the database connection file
include('database.php');

// Check for connection errors
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Define a query to retrieve all columns from the Users table.
$sql = "SELECT UserID, UserName, Address, PhoneNumber, Email, Password, Role, FirstName, LastName FROM Users";
$result = $db->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List | Flower Shop Admin</title>
    <style>
        /* Global Styles */
        body {
            margin: 0;
            font-family: "Trebuchet MS", Helvetica, sans-serif;
            background-color: #fafafa;
            color: #333;
        }
        /* Navigation Bar */
        nav {
            background-color: #F54343;
            padding: 15px 20px;
            text-align: center;
        }
        nav a {
            color: #ffffff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 18px;
            transition: color 0.3s ease;
        }
        nav a:hover {
            color: #e0f2e9;
        }
        /* Logout Button Styling */
        .logout-container {
            text-align: right;
            padding: 10px 20px;
        }
        .logout-button {
            background-color: #f44336;
            border: none;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .logout-button:hover {
            background-color: #d32f2f;
        }
        /* Main Content Container */
        .container {
            padding: 20px;
        }
        h1 {
            font-family: 'Georgia', serif;
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        /* Table Styles */
        table {
            border-collapse: collapse;
            width: 100%;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px 10px;
            font-size: 16px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        /* Form (Delete Button) Styles */
        form {
            margin: 0;
        }
        input[type="submit"] {
            background-color: #f44336;
            color: #fff;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <nav>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <h1>User List</h1>
    
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['UserID']); ?></td>
                        <td><?php echo htmlspecialchars($row['UserName']); ?></td>
                        <td><?php echo htmlspecialchars($row['Address']); ?></td>
                        <td><?php echo htmlspecialchars($row['PhoneNumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['Email']); ?></td>
                        <!-- Display masked password -->
                        <td>*****</td>
                        <td><?php echo htmlspecialchars($row['Role']); ?></td>
                        <td><?php echo htmlspecialchars($row['FirstName']); ?></td>
                        <td><?php echo htmlspecialchars($row['LastName']); ?></td>
                        <!-- Delete Button -->
                        <td>
                            <form method="post" action="delete_user.php" onsubmit="return confirm('Are you sure you want to delete this entry?');">
                                <input type="hidden" name="UserID" value="<?php echo htmlspecialchars($row['UserID']); ?>">
                                <input type="submit" value="Delete">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p style="text-align: center;">No users found.</p>
        <?php endif; ?>
    </div>

    <?php
    // Free the result set and close the connection
    if ($result) {
        $result->free();
    }
    $db->close();
    ?>
</body>
</html>
