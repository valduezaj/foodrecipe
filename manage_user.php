<?php
session_start();
require_once 'db.php'; // Include database connection

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['status']) || $_SESSION['role'] != 0) { // Role 0 is admin
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Handle deletion
if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    $deleteSql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $_SESSION['message'] = "User deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting user!";
        }
        $stmt->close();
    }
    header("Location: manage_user.php");
    exit();
}

// Fetch all users
$sql = "SELECT id, username, photo, role FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    
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
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }

        .action-buttons a {
            color: #f1c40f;
            margin-right: 10px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .action-buttons a:hover {
            color: #e74c3c;
        }

        .back-button {
            background-color: #333;
            color: #f1c40f;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            display: inline-block;
            font-size: 16px;
        }

        .back-button:hover {
            background-color: #444;
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


    <h1>Manage Users</h1>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="modal" id="messageModal" style="display: block;">
            <div class="modal-content">
                <div class="modal-header">
                    Success
                </div>
                <div class="modal-body">
                    <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
                </div>
                <div class="modal-footer">
                    <button id="closeModal" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="modal" id="errorModal" style="display: block;">
            <div class="modal-content">
                <div class="modal-header">
                    Error
                </div>
                <div class="modal-body">
                    <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                </div>
                <div class="modal-footer">
                    <button id="closeModal" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- User Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Photo</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td>
                            <?php if (!empty($row['photo']) && file_exists("uploads/" . $row['photo'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>" alt="User Photo">
                            <?php else: ?>
                                No Photo
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['role'] == 1 ? "User" : "Admin"; ?></td>
                        <td class="action-buttons">
                            <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <a href="manage_user.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            <a href="view_user.php?id=<?php echo $row['id']; ?>">View</a> <!-- Added View Action -->
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
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


    <!-- Back to Dashboard Button -->
    <a href="dashboard.php" class="back-button">Back to Dashboard</a>

    <script>
        // Function to close the modal
        function closeModal() {
            document.getElementById("messageModal").style.display = "none";
            document.getElementById("errorModal").style.display = "none";
        }
    </script>
</body>
</html>
