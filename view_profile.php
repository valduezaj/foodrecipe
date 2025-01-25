<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // Ensure database connection

$username = $_SESSION['username'];

// Fetch user details from the database
$sql = "SELECT username, role, photo FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Default profile photo
$profile_photo = !empty($user['photo']) ? "uploads/" . $user['photo'] : "uploads/default.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
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

        .profile-container {
            text-align: center;
            background-color: #25282b;
            padding: 30px;
            border-radius: 12px;
            width: 400px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
        }

        .profile-container img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 15px;
            border: 4px solid #ffcc00;
        }

        h2 {
            color: #ffcc00;
        }

        p {
            font-size: 18px;
            color: #ccc;
        }

        a {
            color: #ffcc00;
            text-decoration: none;
            font-size: 16px;
        }

        a:hover {
            text-decoration: underline;
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

<div class="profile-container">
        <?php
        // Assume $profile_photo is fetched from the database
        $profile_photo = $user['photo'] ?? ''; // Leave empty if no photo is available
        ?>
        <?php if (!empty($profile_photo)) : ?>
            <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Profile Picture">
        <?php endif; ?>
        
    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
    <p>Role: <?php echo $user['role'] == 0 ? "Admin" : "User"; ?></p>
    
</div>

</body>
</html>

<?php mysqli_close($conn); ?>
