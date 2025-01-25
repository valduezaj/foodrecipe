<?php
session_start();
include 'db.php'; // Include database connection

if (isset($_POST['submit'])) {
    // Get user input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if username and password are provided
    if (empty($username) || empty($password)) {
        $_SESSION['error_message'] = "Username and password are required!";
        header("Location: login.php");
        exit();
    }

    // Check user credentials in the database
    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the hashed password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['status'] = true;

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $_SESSION['error_message'] = "Invalid username or password!";
            }
        } else {
            $_SESSION['error_message'] = "Invalid username or password!";
        }
    } else {
        $_SESSION['error_message'] = "Error preparing the SQL statement!";
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['error_message'] = "Invalid request!";
}

header("Location: login.php");
exit();
?>
