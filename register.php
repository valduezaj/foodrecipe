<?php session_start(); 

// If the session exists, the page can't be accessed
if (isset($_SESSION['status'])) {
    header('location: login.php'); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #121212; /* Dark background */
            color: #ffcc00; /* Light text color */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #1f1f1f;
            padding: 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            font-size: 2rem;
            color: #ffcc00; /* Accent red color */
            margin-bottom: 30px;
        }

        label {
            font-size: 1.1rem;
            color: #ddd;
            margin-bottom: 10px;
            display: block;
        }

        input[type="text"], input[type="password"], input[type="file"] {
            width: 100%;
            padding: 12px;
            background-color: #333;
            border: 1px solid #444;
            border-radius: 5px;
            color: #fff;
            font-size: 1rem;
            margin-bottom: 20px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="password"]:focus, input[type="file"]:focus {
            border-color: #ffcc00;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #ffcc00; /* Accent red */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #ffcc00;
        }

        p {
            text-align: center;
            color: #bbb;
        }

        p a {
            color: #ffcc00;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }

        .modal-header {
            font-size: 1.5em;
            font-weight: bold;
            color: #ffcc00;
        }

        .modal-body {
            margin-top: 10px;
            font-size: 1.1em;
        }

        .modal-footer {
            text-align: right;
            margin-top: 20px;
        }

        .error-message {
            background-color: #ffcc00;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-top: 15px;
        }

    </style>
</head>
<body>

<div class="container">
    <form action="register_handler.php" method="POST" enctype="multipart/form-data" id="registerForm">
        <h2>Create Your Account</h2>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="profile_image">Photo:</label>
        <input type="file" name="profile_image"><br><br>

        <button type="submit" name="submit">Register</button>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </form>
</div>

<!-- Modal -->
<?php if (isset($_SESSION['error_message'])): ?>
    <div id="errorModal" class="modal" style="display: block;">
        <div class="modal-content">
            <div class="modal-header">
                Error
            </div>
            <div class="modal-body">
                <?php echo $_SESSION['error_message']; ?>
            </div>
            <div class="modal-footer">
                <button id="closeModal" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>
    <?php
        // Clear the error message from session after displaying it
        unset($_SESSION['error_message']);
    ?>
<?php endif; ?>

<script>
    // Function to close the modal
    function closeModal() {
        document.getElementById("errorModal").style.display = "none";
    }
</script>

</body>
</html>
