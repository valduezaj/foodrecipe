<?php
session_start();

// Sample array of food recipes
$recipes = [
    [
        'name' => 'Spaghetti Carbonara',
        'image' => '1.jpg',
        'description' => 'A classic Italian pasta dish made with eggs, cheese, pancetta, and pepper.',
    ],
    [
        'name' => 'Chicken Curry',
        'image' => '2.jpg',
        'description' => 'A flavorful and spicy curry made with chicken, spices, and coconut milk.',
    ],
    [
        'name' => 'Chocolate Cake',
        'image' => '3.jpg',
        'description' => 'A rich and moist chocolate cake with creamy frosting.',
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Recipes</title>
    <style>
        /* Reset Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #121212; /* Dark background */
            color: #ffcc00; /* Light text */
            padding-top: 80px; /* Space for fixed header */
        }

        header {
            background-color: #333;
            color: #ffcc00; /* Accent color */
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        header h1 {
            font-size: 30px;
            margin-right: 20px;
        }

        .buttons {
            display: flex;
            gap: 20px;
        }

        .button {
            background-color: #333;
            color: #ffcc00;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #ffcc00;
            color: #fff;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 40px 0;
        }

        .recipe-card {
            display: flex;
            background-color: #1e1e1e;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .recipe-card:hover {
            transform: scale(1.05);
        }

        .recipe-card img {
            width: 250px;
            height: 150px;
            object-fit: cover;
        }

        .recipe-content {
            padding: 20px;
            flex: 1;
        }

        .recipe-content h2 {
            margin-bottom: 10px;
            color: #ffcc00;
            font-size: 24px;
        }

        .recipe-content p {
            margin-bottom: 20px;
            color: #ddd;
            font-size: 16px;
        }

        .recipe-content a {
            text-decoration: none;
            color: #ffcc00;
            font-weight: bold;
        }

        footer {
            background-color: #333;
            color: #ffcc00;
            text-align: center;
            padding: 10px;
            margin-top: 40px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .recipe-card {
                flex-direction: column;
                align-items: center;
            }

            .recipe-card img {
                width: 100%;
                height: auto;
            }

            .buttons {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            header h1 {
                font-size: 28px;
            }

            .recipe-content h2 {
                font-size: 20px;
            }

            .recipe-content p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome to Our Recipe Collection</h1>
    <div class="buttons">
        <?php if (isset($_SESSION['username'])): ?>
            <!-- Add user-specific links or actions if logged in -->
        <?php else: ?>
            <a href="login.php" class="button">Login</a>
            <a href="register.php" class="button">Register</a>
        <?php endif; ?>
    </div>
</header>

<div class="container">
    <h2>Featured Recipes</h2>

    <?php foreach ($recipes as $recipe): ?>
        <div class="recipe-card">
            <img src="<?php echo $recipe['image']; ?>" alt="<?php echo $recipe['name']; ?>">
            <div class="recipe-content">
                <h2><?php echo $recipe['name']; ?></h2>
                <p><?php echo $recipe['description']; ?></p>
                
            </div>
        </div>
    <?php endforeach; ?>
</div>

<footer>
    <p>&copy; 2025 Food Recipe Collection. All rights reserved.</p>
</footer>

</body>
</html>
