<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // Ensure db connection

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Fetch user details (e.g., profile picture) from the database
$sql = "SELECT photo FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$profile_photo = !empty($user['photo']) ? "uploads/" . $user['photo'] : "uploads/default.png"; // Default image if no photo is set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #1d1f20; /* Dark background */
            color: white;
            margin: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
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

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            box-sizing: border-box;
            overflow-y: auto;
            text-align: center;
        }

        .header {
            background-color: #333;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
        }

        .profile-section {
            display: inline-block;
            text-align: center;
            background-color: #25282b;
            padding: 30px;
            border-radius: 12px;
            margin-top: 20px;
            width: 400px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
        }

        .profile-section img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 15px;
            border: 4px solid #ffcc00;
        }

        .profile-section h3 {
            margin: 15px 0;
            font-size: 28px;
            color: #ffcc00;
        }

        .profile-section p {
            font-size: 18px;
            color: #ccc;
        }

        .profile-section .btn-container {
            margin-top: 20px;
        }

        .profile-section button {
            background-color: #ffcc00;
            color: #1d1f20;
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .profile-section button:hover {
            background-color: #e6b800;
        }

        h1 {
            font-size: 36px;
            color: #ffcc00; /* Golden accent for the heading */
        }

        p {
            font-size: 18px;
            color: #ccc; /* Light grey for the user info */
        }

        /* Responsive Sidebar */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .sidebar a {
                text-align: center;
            }

            .profile-section {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><?php echo $role == 0 ? "Admin Dashboard" : "User Dashboard"; ?></h2>
    <a href="dashboard.php">Home</a>
    
    <?php if ($role == 1): ?>
        <a href="post_recipe.php">Post Recipe</a>
        <a href="view_your_post.php">View Your Posts</a>
    <?php endif; ?>
    <?php if ($role == 0): ?>
        <a href="post_recipes.php">Post Recipe</a>
        <a href="manage_user.php">Manage Users</a>
        <a href="view_recipe_post.php">View All Posts</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <!-- Dashboard Header -->
    <div class="header">
        <h1>Welcome to the Dashboard</h1>
        <p>Hello, <?php echo htmlspecialchars($username); ?> (<?php echo $role == 0 ? "Admin" : "User"; ?>)</p>
    </div>

    <!-- Profile Section -->
    <div class="profile-section">
    <?php
 // Assume $profile_photo is fetched from the database
 $profile_photo = $user['photo'] ?? ''; // Leave empty if no photo is available
 ?>
 <?php if (!empty($profile_photo)) : ?>
     <img src=" <?php echo htmlspecialchars($profile_photo); ?>" alt="Profile Picture">
 <?php endif; ?>
 
        <h3><?php echo htmlspecialchars($username); ?></h3>
        <p>Role: <?php echo $role == 0 ? "Admin" : "User"; ?></p>

        <!-- Buttons -->
        <div class="btn-container">
            <button onclick="window.location.href='view_profile.php'">View Profile</button>
            <button onclick="window.location.href='edit_profile.php'">Edit Profile</button>
        </div>
    </div>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>
