<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // Ensure db connection

// Check if 'id' is set in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_all_posts.php");
    exit;
}

$post_id = intval($_GET['id']); // Sanitize the ID
$message = "";

// Fetch the post data
$query = "SELECT * FROM recipes WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If no post is found, redirect
    header("Location: view_all_posts.php");
    exit;
}

$post = $result->fetch_assoc();

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $ingredients = htmlspecialchars($_POST['ingredients']);
    $image = $post['image']; // Keep the existing image by default

    // Check if a new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate the image
        if (getimagesize($_FILES['image']['tmp_name'])) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $target_file; // Update the image path
            } else {
                $message = "Failed to upload the image.";
            }
        } else {
            $message = "Invalid image file.";
        }
    }

    // Update the post in the database
    $update_query = "UPDATE recipes SET title = ?, description = ?, ingredients = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssi", $title, $description, $ingredients, $image, $post_id);

    if ($stmt->execute()) {
        $message = "Post updated successfully!";
        // Refresh post data
        $post['title'] = $title;
        $post['description'] = $description;
        $post['ingredients'] = $ingredients;
        $post['image'] = $image;
    } else {
        $message = "Failed to update the post.";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1d1f20;
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



        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #25282b;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
        }

        button {
            background-color: #ffcc00;
            color: #1d1f20;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #e6b800;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
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

<h1>Edit Post</h1>

<div class="message"><?php echo $message; ?></div>

<form method="POST" enctype="multipart/form-data">
    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($post['description']); ?></textarea>

    <label for="ingredients">Ingredients</label>
    <textarea id="ingredients" name="ingredients" rows="5" required><?php echo htmlspecialchars($post['ingredients']); ?></textarea>

    <label for="image">Image</label>
    <?php if ($post['image']): ?>
        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Recipe Image" style="max-width: 100%; margin-bottom: 10px;">
    <?php endif; ?>
    <input type="file" id="image" name="image">

    <button type="submit">Update Post</button>
</form>

<a href="view_recipe_post.php" style="display: block; text-align: center; margin-top: 20px; color: #ffcc00; text-decoration: none;">Back to All Posts</a>

</body>
</html>
