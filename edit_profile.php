<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'db.php'; // Ensure database connection

$username = $_SESSION['username'];

// Fetch current user details
$sql = "SELECT username, photo FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'];

    // Handle profile photo upload
    $photo = $user['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $photo = $target_dir . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }

    // Update user details
    $sql = "UPDATE users SET username = ?, photo = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $new_username, $photo, $username);
    if ($stmt->execute()) {
        $_SESSION['username'] = $new_username; // Update session username
        header("Location: view_profile.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
            background-color: #25282b;
            padding: 30px;
            border-radius: 12px;
            width: 400px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
        }

        .form-container h2 {
            color: #ffcc00;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
        }

        .form-container label {
            margin: 10px 0 5px;
            font-size: 16px;
        }

        .form-container input {
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .form-container button {
            background-color: #ffcc00;
            color: #1d1f20;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #e6b800;
        }

        a {
            color: #ffcc00;
            text-align: center;
            display: block;
            margin-top: 15px;
            text-decoration: none;
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
    <h2>Edit Profile</h2>
    <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        
        
        
        
        <label for="photo"> Photo</label>
        <input type="file" id="photo" name="photo">

        <button type="submit">Save Changes</button>
    </form>
    
</div>

</body>
</html>

<?php mysqli_close($conn); ?>
