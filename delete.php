<?php
session_start();
include 'db.php';

// Ensure user is logged in and has permission to delete users
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // Delete user from the database
    $delete_query = "DELETE FROM users WHERE id = $delete_id";

    if (mysqli_query($conn, $delete_query)) {
        header("Location: dashboard.php?success=User deleted successfully");
        exit;
    } else {
        echo "Error deleting user: " . mysqli_error($conn);
    }
}
?>
