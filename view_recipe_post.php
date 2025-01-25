<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // Ensure db connection

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Posts</title>
    
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
        }

        /* Table styling */
        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-left: 270px;
            width: calc(100% - 270px);
        }

        th, td {
            padding: 15px;
            text-align: right;
            border-bottom: 1px solid #444;
        }

        th {
            background-color: #333;
            color: #f1c40f;
            font-size: 18px;
        }

        td {
            background-color: #222;
        }

        img {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .action-link, .view-link {
            color: #3498db;
            text-decoration: none;
            
            margin-right: 15px;
            border-radius: 5px;
            padding: 6px 12px;
            transition: color 0.3s ease, background-color 0.3s ease;
        }

        .action-link:hover, .view-link:hover {
            color: #fff;
            background-color: #2980b9;
        }

        .delete {
            color: #e74c3c;
            text-decoration: none;
           
            padding: 6px 12px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .delete:hover {
            color: #fff;
            background-color: #c0392b; /* Darker red on hover */
        }

        /* Styling for no image */
        .no-image {
            color: #777;
            font-style: italic;
        }

        /* Alternating row colors */
        tr:nth-child(even) {
            background-color: #2a2d31;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #2c3e50;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
        }

        .modal-header {
            font-size: 1.5em;
            font-weight: bold;
            color: #f1c40f;
        }

        .modal-body {
            margin-top: 10px;
            color: #ecf0f1;
            font-size: 1.1em;
        }

        .modal-footer {
            text-align: right;
            margin-top: 20px;
        }

        #closeModal {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }

        #closeModal:hover {
            background-color: #c0392b;
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
        <h1>All User Posts</h1>
    </div>

    <table>
        <tr>
            <th>Title</th>
            <th>Image</th>
            <th>Description</th>
            <th>Ingredients</th>
            <th>Action</th>
        </tr>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM recipes");
        while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td>
                    <?php if ($row['image']): ?>
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Recipe Image">
                    <?php else: ?>
                        <span class="no-image">No image uploaded</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['description']); ?></td> <!-- Display description -->
                <td><?php echo htmlspecialchars($row['ingredients']); ?></td> <!-- Display ingredients -->
                <td>
                    <a class="action-link" href="edit_all_post.php?id=<?php echo $row['id']; ?>">Edit</a>
                    <a class="delete" href="delete_all_posts.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                    <a class="view-link" href="view_post.php?id=<?php echo $row['id']; ?>">View</a> <!-- Added View Action -->
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <?php mysqli_close($conn); ?>
</body>
</html>
