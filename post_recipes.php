<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // Include your database connection

$username = $_SESSION['username'];

// Initialize variables for form input values
$title = $ingredients = $instructions = $image = '';
$titleError = $ingredientsError = $instructionsError = $imageError = '';

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $title = htmlspecialchars($_POST['title']);
    $ingredients = htmlspecialchars($_POST['ingredients']);
    $instructions = htmlspecialchars($_POST['instructions']);

    if (empty($title)) {
        $titleError = 'Recipe title is required.';
    }

    if (empty($ingredients)) {
        $ingredientsError = 'Ingredients are required.';
    }

    if (empty($instructions)) {
        $instructionsError = 'Instructions are required.';
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imagePath = 'uploads/' . $imageName;

        // Move uploaded image to 'uploads' folder
        if (!move_uploaded_file($imageTmpName, $imagePath)) {
            $imageError = 'Failed to upload image.';
        } else {
            $image = $imagePath; // Store image path for database insertion
        }
    }

    // If no errors, insert the recipe into the database
    if (empty($titleError) && empty($ingredientsError) && empty($instructionsError) && empty($imageError)) {
        $sql = "INSERT INTO recipes (title, ingredients, instructions, image, posted_by) VALUES ('$title', '$ingredients', '$instructions', '$image', '$username')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Recipe posted successfully!'); window.location.href = 'view_recipe_post.php';</script>";
        } else {
            echo "<script>alert('Error posting recipe. Please try again.');</script>";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Recipe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1d1f20;
            color: white;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
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

        .form-container {
            background-color: #333;
            padding: 20px;
            width: 400px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .form-container h2 {
            color: #ffcc00;
            text-align: center;
        }

        .form-container label {
            font-size: 18px;
            color: #ccc;
        }

        .form-container input, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            background-color: #25282b;
            color: #fff;
            border: 1px solid #444;
            border-radius: 4px;
        }

        .form-container button {
            background-color: #ffcc00;
            color: #333;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            width: 100%;
            font-size: 18px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #e7b800;
        }

        .error {
            color: red;
            font-size: 14px;
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

<div class="form-container">
    <h2>Post a New Recipe</h2>

    <form action="post_recipe.php" method="POST" enctype="multipart/form-data">
        <!-- Recipe Title -->
        <label for="title">Recipe Title:</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>">
        <span class="error"><?php echo $titleError; ?></span>

        <!-- Ingredients -->
        <label for="ingredients">Ingredients:</label>
        <textarea name="ingredients" id="ingredients" rows="4"><?php echo htmlspecialchars($ingredients); ?></textarea>
        <span class="error"><?php echo $ingredientsError; ?></span>

        <!-- Instructions -->
        <label for="instructions">Instructions:</label>
        <textarea name="instructions" id="instructions" rows="6"><?php echo htmlspecialchars($instructions); ?></textarea>
        <span class="error"><?php echo $instructionsError; ?></span>

        <!-- Recipe Image (Optional) -->
        <label for="image">Recipe Image (Optional):</label>
        <input type="file" name="image" id="image">
        <span class="error"><?php echo $imageError; ?></span>

        <!-- Submit Button -->
        <button type="submit">Post Recipe</button>
    </form>
</div>
</body>
</html>
