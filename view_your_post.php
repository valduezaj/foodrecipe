<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 1) { // Ensure the user is logged in and has a role of "User"
    header("Location: login.php");
    exit;
}

include 'db.php'; // Include the database connection

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Posts</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #121212; /* Dark background */
            color: #ffcc00; /* Light text color */
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
            color: #ffcc00;
            text-align: center;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        table {
    width: 80%;
    border-collapse: collapse;
    margin-top: 20px;
    margin-left: 270px;
    width: calc(100% - 270px);
}

        table, th, td {
            border: 1px solid #444; /* Dark border color */
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #222; /* Dark header */
            color: #ffcc00; /* Accent yellow color */
        }

        td {
            background-color: #1f1f1f; /* Dark row background */
        }

        a.delete, a.edit {
            color: #ffcc00;
            text-decoration: none;
            font-weight: bold;
            margin: 0 5px;
        }

        a.delete:hover, a.edit:hover {
            color: #fff;
        }

        /* Back Button Style */
        .back-button {
            background-color: #444;
            color: #ffcc00;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 30px;
            display: inline-block;
            text-align: center;
            font-size: 1.2rem;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #555;
        }

        img {
            width: 120px;
            height: auto;
            border-radius: 5px;
            border: 2px solid #444;
        }

        /* Responsive styling */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            table, th, td {
                padding: 8px;
            }

            img {
                width: 80px;
            }
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

<h1>Your Posts</h1>

<!-- Button to go back to Dashboard -->
<a href="dashboard.php" class="back-button">Back to Dashboard</a>

<table>
    <tr>
        <th>Title</th>
        <th>Image</th>
        <th>Description</th>
        <th>Ingredients</th>
        <th>Action</th>
    </tr>
    <?php
    // Prepare and execute query to fetch the user's posts securely
    $stmt = $conn->prepare("SELECT id, title, image, description, ingredients FROM recipes WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if posts exist and display them
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td>
                        <?php if (!empty($row['image'])): ?>
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Recipe Image">
                        <?php else: ?>
                            <img src="default_image.png" alt="No image available"> <!-- Default image -->
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td> <!-- Display description -->
                    <td><?php echo htmlspecialchars($row['ingredients']); ?></td> <!-- Display ingredients -->
                    <td>
                        <a class="edit" href="edit_your_posts.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a class="delete" href="delete_user_post.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile;
        } else {
            echo '<tr><td colspan="5">No posts found.</td></tr>';
        }
        $stmt->close();
    } else {
        echo '<tr><td colspan="5">Error fetching posts.</td></tr>';
    }

    $conn->close();
    ?>
</table>

</body>
</html>
