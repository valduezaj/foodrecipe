<?php
session_start();
require_once 'db.php'; // Include database connection

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 0) { // Role 0 is admin
    header("Location: login.php");
    exit();
}

// Get the user ID from the URL
if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);

    // Fetch the user's current details from the database
    $sql = "SELECT id, username, photo, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $_SESSION['error_message'] = "User not found!";
        header("Location: manage_user.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "Invalid request!";
    header("Location: manage_user.php");
    exit();
}

// Handle form submission for editing the user
if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = isset($_POST['role']) ? intval($_POST['role']) : 1; // Default to User role if not set
    $profile_image = $user['photo']; // Keep existing image if not uploading a new one

    if (empty($username)) {
        $_SESSION['error_message'] = "Username is required!";
        header("Location: edit.php?id=" . $userId);
        exit();
    }

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $password_update = ", password = ?";
    } else {
        $password_update = "";
    }

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['profile_image']['name']);
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = basename($_FILES['profile_image']['name']);
            } else {
                $_SESSION['error_message'] = "Error uploading the image!";
                header("Location: edit.php?id=" . $userId);
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Invalid image file type!";
            header("Location: edit.php?id=" . $userId);
            exit();
        }
    }

    $updateSql = "UPDATE users SET username = ?, role = ?, photo = ? $password_update WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    if ($stmt) {
        if (!empty($password)) {
            $stmt->bind_param("sisi", $username, $role, $profile_image, $hashed_password, $userId);
        } else {
            $stmt->bind_param("sisi", $username, $role, $profile_image, $userId);
        }
        if ($stmt->execute()) {
            $_SESSION['message'] = "User updated successfully!";
            header("Location: manage_user.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error updating user: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Error preparing SQL statement!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1d1f20;
            color: #fff;
            margin: 0;
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

        input[type="text"], input[type="password"], select, input[type="file"] {
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

        img {
            max-width: 80px;
            max-height: 80px;
            border-radius: 8px;
            margin-bottom: 10px;
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


<h1>Edit User</h1>

<?php if (isset($_SESSION['message'])): ?>
    <div class="message" style="color: #28a745;"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="message" style="color: #dc3545;"><?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

<form action="edit.php?id=<?php echo $userId; ?>" method="POST" enctype="multipart/form-data">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

    <label for="password">Password (leave empty to keep current)</label>
    <input type="password" id="password" name="password">

    <label for="role">Role</label>
    <select name="role" id="role">
        <option value="1" <?php echo ($user['role'] == 1) ? "selected" : ""; ?>>User</option>
        <option value="0" <?php echo ($user['role'] == 0) ? "selected" : ""; ?>>Admin</option>
    </select>

    <label for="profile_image">Profile Photo</label>
    <input type="file" id="profile_image" name="profile_image">
    <?php if (!empty($user['photo']) && file_exists("uploads/" . $user['photo'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($user['photo']); ?>" alt="User Photo">
    <?php else: ?>
        <span>No Photo</span>
    <?php endif; ?>

    <button type="submit" name="submit">Update User</button>
</form>

<a href="manage_user.php" style="display: block; text-align: center; margin-top: 20px; color: #ffcc00; text-decoration: none;">Back to Manage Users</a>

</body>
</html>
