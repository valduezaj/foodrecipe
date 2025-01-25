<?php
session_start();
require_once 'db.php'; // Include the database connection

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Retrieve the form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $profile_image = null;

    // Check if username and password are empty
    if (empty($username) || empty($password)) {
        $_SESSION['error_message'] = "Username and password are required!";
        header("Location: register.php");
        exit();
    }

    // Check if the username already exists in the database
    $checkUserSql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($checkUserSql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['error_message'] = "Username is already taken. Please choose another one.";
        header("Location: register.php");
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['profile_image']['name']);
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate the image file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($image_file_type, $allowed_types)) {
            // Move the uploaded file to the server
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = basename($_FILES['profile_image']['name']);
            } else {
                $_SESSION['error_message'] = "Error uploading the image!";
                header("Location: register.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Invalid image file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            header("Location: register.php");
            exit();
        }
    }

    // Default role is User (1)
    $role = 1;

    // Insert the user data into the database
    $insertSql = "INSERT INTO users (username, password, photo, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    if ($stmt) {
        $stmt->bind_param("sssi", $username, $hashed_password, $profile_image, $role);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Registration successful! You can now log in.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Error preparing the SQL statement!";
    }

    $conn->close();
} else {
    $_SESSION['error_message'] = "Invalid request!";
    header("Location: register.php");
    exit();
}
?>
