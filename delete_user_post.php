<?php
session_start();

// Ensure the user is logged in and is the owner of the post
if (!isset($_SESSION['username']) || $_SESSION['role'] != 1) { // Ensure the user is logged in with role "User"
    header("Location: login.php");
    exit();
}

include 'db.php'; // Include the database connection

// Get the post ID from the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $postId = intval($_GET['id']);
    $username = $_SESSION['username'];

    // Prepare and execute the query to delete the post
    $stmt = $conn->prepare("DELETE FROM recipes WHERE id = ? AND username = ?");
    if ($stmt) {
        $stmt->bind_param("is", $postId, $username);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Post deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error: Could not delete post. It may not exist or you do not have permission to delete it.";
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Error preparing SQL statement!";
    }
} else {
    $_SESSION['error_message'] = "Invalid post ID!";
}

$conn->close();

// Redirect the user back to their posts page
header("Location: view_your_post.php");
exit();
?>
