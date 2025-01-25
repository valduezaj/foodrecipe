<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #121212; /* Dark background */
            color:  #ffcc00; /* Light text color */
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
            color:  #ffcc00; /* Accent red color */
            margin-bottom: 30px;
        }

        label {
            font-size: 1.1rem;
            color: #ddd;
            margin-bottom: 10px;
            display: block;
        }

        input[type="text"], input[type="password"] {
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

        input[type="text"]:focus, input[type="password"]:focus {
            border-color:  #ffcc00;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color:  #ffcc00; /* Accent red */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color:  #ffcc00;
        }

        p {
            text-align: center;
            color: #bbb;
        }

        p a {
            color:  #ffcc00;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        /* Modal for error messages */
        .error-message {
            background-color:  #ffcc00;
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
        <form action="login_process.php" method="POST">
            <h2>Login</h2>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit" name="submit">Login</button>

            <p>Don't have an account? <a href="register.php">Register</a></p>
        </form>

        <!-- Modal for error messages -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <p><?php echo $_SESSION['error_message']; ?></p>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
    </div>

</body>
</html>
