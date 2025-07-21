<?php
// Include the database connection file
include('database.php');

// Check for a valid database connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Verify the UserID is sent via POST
if (isset($_POST['UserID'])) {
    $userID = intval($_POST['UserID']);
    
    // Prepare a statement to safely delete the user
    $stmt = $db->prepare("DELETE FROM Users WHERE UserID = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $db->error);
    }
    $stmt->bind_param("i", $userID);
    
    if ($stmt->execute()) {
        // Successful deletion
        $stmt->close();
        $db->close();
        // Redirect back to the users list
        header("Location: admin_view.php");
        exit;
    } else {
        // Handle the execution error
        echo "Error deleting record: " . $stmt->error;
        $stmt->close();
        $db->close();
    }
} else {
    echo "No user ID was provided.";
    $db->close();
}
?>
