<?php
include 'db.php';

$id = $_GET['id'];
$query = "DELETE FROM users WHERE id = $id";

if (mysqli_query($conn, $query)) {
    header('Location: dashboard.php');
    exit;
} else {
    echo "Error deleting user: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
