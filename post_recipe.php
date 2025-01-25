<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // Ensure db connection

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients']; // Get the ingredients
    
    // Handle file upload
    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];
        
        // Get file details
        $fileName = $_FILES['image']['name'];
        $fileTmpName = $_FILES['image']['tmp_name'];
        $fileSize = $_FILES['image']['size'];
        $fileError = $_FILES['image']['error'];
        $fileType = $_FILES['image']['type'];

        // Allow only specific image file types
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        
        if (in_array($fileType, $allowedTypes)) {
            if ($fileError === 0) {
                if ($fileSize < 5000000) { // Max size 5MB
                    $fileDestination = 'uploads/' . uniqid('', true) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                        // Save the post data along with the image path
                        $sql = "INSERT INTO recipes (username, title, description, image, ingredients) VALUES ('$username', '$title', '$description', '$fileDestination', '$ingredients')";
                        if (mysqli_query($conn, $sql)) {
                            $success = "Recipe posted successfully!";
                        } else {
                            $success = "Error posting recipe: " . mysqli_error($conn);
                        }
                    } else {
                        $success = "Error uploading the image.";
                    }
                } else {
                    $success = "File size is too large. Maximum size is 5MB.";
                }
            } else {
                $success = "Error uploading the file.";
            }
        } else {
            $success = "Invalid file type. Only JPEG, PNG, and JPG files are allowed.";
        }
    } else {
        $success = "No image uploaded.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Recipe</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #121212;
            color: #ffcc00;
            margin: 0;
            padding: 40px;
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
            color: #ffcc00;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        form {
            background-color: #1f1f1f;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
            max-width: 600px;
            margin: 0 auto;
        }

        label {
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffcc00;
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #444;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 1rem;
            color: #ffcc00;
            background-color: #2b2b2b;
        }

        textarea {
            resize: vertical;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #ffcc00;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #ffcc00;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            padding: 10px 15px;
            background-color: #333;
            color: white;
            border-radius: 5px;
            text-align: center;
        }

        .back-button:hover {
            background-color: #444;
        }

        .message {
            text-align: center;
            font-size: 1.2rem;
            margin-top: 20px;
            color: #27ae60; /* Green success message */
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

<h1>Post Recipe</h1>

<form action="post_recipe.php" method="POST" enctype="multipart/form-data">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required>

    <label for="description">Description:</label>
    <textarea id="description" name="description" rows="4" required></textarea>

    <label for="ingredients">Ingredients (comma-separated):</label>
    <textarea id="ingredients" name="ingredients" rows="4" required></textarea>

    <label for="image">Upload an Image:</label>
    <input type="file" name="image" id="image" required>

    <input type="submit" value="Post Recipe">
</form>

<!-- Back to Dashboard Button -->
<a href="dashboard.php" class="back-button">Back to Dashboard</a>

<?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($success)) { ?>
    <div class="message"><?php echo $success; ?></div>
<?php } ?>

</body>
</html>

<?php mysqli_close($conn); ?>
