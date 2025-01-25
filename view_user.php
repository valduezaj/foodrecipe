<?php
session_start();
require_once 'db.php'; // Include database connection

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 0) { // Role 0 is admin
    header("Location: login.php");
    exit();
}

// Get user ID from the URL
if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);

    // Fetch user data from the database
    $sql = "SELECT id, username, photo, role, created_at FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $_SESSION['error_message'] = "User not found!";
        header("Location: manage_user.php");
        exit();
    }

    $stmt->close();
} else {
    $_SESSION['error_message'] = "Invalid user ID!";
    header("Location: manage_user.php");
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #1f1f1f;
            color: #fff;
            margin: 0;
            padding: 20px;
        }
           /* Sidebar Styles */
 .sidebar {
     width: 250px;
     height: 100%;
     background-color: #333; /* Dark Sidebar */
     color: white;
     position: fixed;
     top: 0;
     left: 0;
     padding: 20px;
     box-sizing: border-box;
     overflow-y: auto;
 }
 .sidebar h2 {
     font-size: 24px;
     margin-bottom: 20px;
     color: #ffcc00; /* Golden accent for the title */
 }
 .sidebar a {
     color: #ffcc00;
     text-decoration: none;
     display: block;
     padding: 12px;
     font-size: 18px;
     transition: all 0.3s ease;
 }
 .sidebar a:hover {
     background-color: #444;
     padding-left: 25px;
 }

        h1 {
            text-align: center;
            color: #f1c40f;
            font-size: 36px;
        }

        .user-details {
            background-color: #333;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            margin: 20px auto;
        }

        .user-details h2 {
            color: #f1c40f;
            text-align: center;
        }

        .user-details p {
            font-size: 18px;
            color: #ecf0f1;
        }

        .user-details img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin: 10px auto;
            display: block;
        }

        .back-button {
            background-color: #333;
            color: #f1c40f;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            display: inline-block;
            font-size: 16px;
            text-align: center;
        }

        .back-button:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h2><?php echo $_SESSION['role'] == 0 ? "Admin Dashboard" : "User Dashboard"; ?></h2>
    <a href="dashboard.php">Home</a>
    
    <?php if ($_SESSION['role'] == 1): ?>
        <a href="post_recipe.php">Post Recipe</a>
        <a href="view_your_post.php">View Your Posts</a>
    <?php endif; ?>
    <?php if ($_SESSION['role'] == 0): ?>
        <a href="post_recipes.php">Post Recipe</a>
        <a href="manage_user.php">Manage Users</a>
        <a href="view_recipe_post.php">View All Posts</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</div>


<h1>View User Details</h1>

<div class="user-details">
    <h2><?php echo htmlspecialchars($user['username']); ?>'s Details</h2>
    
    <!-- Display User Photo -->
    <?php if (!empty($user['photo']) && file_exists("uploads/" . $user['photo'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($user['photo']); ?>" alt="User Photo">
    <?php else: ?>
        <p>No Photo Available</p>
    <?php endif; ?>

    <p><strong>ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>

    <p><strong>Role:</strong> <?php echo $user['role'] == 1 ? "User" : "Admin"; ?></p>
    <p><strong>Account Created:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
</div>

<!-- Back Button -->
<a href="manage_user.php" class="back-button">Back to Manage Users</a>

</body>
</html>
