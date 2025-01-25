<?php
session_start();

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // Ensure database connection

// Check if 'id' is set in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_recipe_post.php");
    exit;
}

$post_id = intval($_GET['id']); // Sanitize the ID

// Fetch the post to check if it exists and possibly delete its image file
$query = "SELECT image FROM recipes WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If the post does not exist, redirect to the view page
    header("Location: view_recipe_post.php");
    exit;
}

$post = $result->fetch_assoc();

// Delete the image file if it exists
if (!empty($post['image']) && file_exists($post['image'])) {
    unlink($post['image']);
}

// Delete the post from the database
$delete_query = "DELETE FROM recipes WHERE id = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("i", $post_id);

if ($stmt->execute()) {
    // Redirect to the view page after successful deletion
    header("Location: view_recipe_post.php?message=Post deleted successfully");
} else {
    // If deletion fails, redirect with an error message
    header("Location: view_recipe_post.php?error=Failed to delete the post");
}

$stmt->close();
$conn->close();
?>
