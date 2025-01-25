<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 1) { // Ensure the user is logged in and has the correct role
    header("Location: login.php");
    exit;
}

include 'db.php'; // Include the database connection

// Check if the ID is set in the URL and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: edit_your_posts.php"); // Redirect to posts page if ID is invalid
    exit;
}

$post_id = intval($_GET['id']);
$username = $_SESSION['username'];

// Fetch the post details
$stmt = $conn->prepare("SELECT title, image, description, ingredients FROM recipes WHERE id = ? AND username = ?");
if ($stmt) {
    $stmt->bind_param("is", $post_id, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: edit_your_posts.php"); // Redirect if no post is found
        exit;
    }

    $post = $result->fetch_assoc();
    $stmt->close();
} else {
    die("Error fetching post details.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $ingredients = htmlspecialchars(trim($_POST['ingredients']));
    $image = $_FILES['image'];

    $image_path = $post['image']; // Default to the existing image path

    // Handle image upload if a new image is provided
    if (!empty($image['name'])) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($image['name']);
        $image_file_type = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));

        // Validate image type and size
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($image_file_type, $allowed_types)) {
            $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } elseif ($image['size'] > 2 * 1024 * 1024) { // Limit file size to 2MB
            $error = "Image size should not exceed 2MB.";
        } else {
            // Move the uploaded file to the target directory
            if (!move_uploaded_file($image['tmp_name'], $image_path)) {
                $error = "Failed to upload the image.";
            }
        }
    }

    // If no errors, update the post in the database
    if (!isset($error)) {
        $stmt = $conn->prepare("UPDATE recipes SET title = ?, image = ?, description = ?, ingredients = ? WHERE id = ? AND username = ?");
        if ($stmt) {
            $stmt->bind_param("ssssis", $title, $image_path, $description, $ingredients, $post_id, $username);
            if ($stmt->execute()) {
                header("Location: edit_your_posts.php?success=Post updated successfully.");
                exit;
            } else {
                $error = "Failed to update the post.";
            }
            $stmt->close();
        } else {
            $error = "Database error.";
        }
    }
}
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
            background-color: #121212;
            color: #ffcc00;
            padding: 20px;
        }
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
            color: #ffcc00;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #1f1f1f;
            padding: 20px;
            border-radius: 8px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #222;
            color: #fff;
            border: 1px solid #444;
            border-radius: 5px;
        }

        button {
            background-color: #444;
            color: #ffcc00;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #555;
        }

        .error {
            color: red;
            text-align: center;
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

<?php if (isset($error)): ?>
    <p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>

    <label for="description">Description:</label>
    <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($post['description']); ?></textarea>

    <label for="ingredients">Ingredients:</label>
    <textarea id="ingredients" name="ingredients" rows="5" required><?php echo htmlspecialchars($post['ingredients']); ?></textarea>

    <label for="image">Image:</label>
    <?php if (!empty($post['image'])): ?>
        <p>Current Image:</p>
        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Current Image" width="150"><br><br>
    <?php endif; ?>
    <input type="file" id="image" name="image">

    <button type="submit">Update Post</button>
</form>

</body>
</html>
