<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // Ensure db connection

// Check if 'id' is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: view_recipe_post.php"); // Redirect to the view all posts page if no 'id' is set
    exit;
}

$id = $_GET['id'];

// Query to fetch the details of the post
$result = mysqli_query($conn, "SELECT * FROM recipes WHERE id = '$id'");
$post = mysqli_fetch_assoc($result);

// If the post is not found, redirect back to the view all posts page
if (!$post) {
    header("Location: view_recipe_post.php");
    exit;
}

mysqli_close($conn); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post</title>
    
    <style>
        /* General page styling */
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
            margin-bottom: 20px;
        }

        /* Post Details Styling */
        .post-details {
            width: 80%;
            margin-left: 270px;
            background-color: #222;
            padding: 20px;
            border-radius: 8px;
            color: #ecf0f1;
        }

        .post-details h2 {
            font-size: 28px;
            color: #f1c40f;
        }

        .post-details img {
            width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin: 20px 0;
        }

        .post-details p {
            font-size: 18px;
            line-height: 1.6;
        }

        .back-link {
            color: #3498db;
            text-decoration: none;
            font-size: 18px;
            margin-top: 20px;
            display: inline-block;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #2980b9;
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

    <div class="header-container">
        <h1>View Post</h1>
    </div>

    <div class="post-details">
        <h2><?php echo htmlspecialchars($post['title']); ?></h2>
        
        <div class="image-container">
            <?php if ($post['image']): ?>
                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Recipe Image">
            <?php else: ?>
                <p class="no-image">No image uploaded</p>
            <?php endif; ?>
        </div>
        
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($post['description'])); ?></p>
        <p><strong>Ingredients:</strong> <?php echo nl2br(htmlspecialchars($post['ingredients'])); ?></p>

        <a href="view_recipe_post.php" class="back-link">Back to All Posts</a>
    </div>

</body>
</html>
