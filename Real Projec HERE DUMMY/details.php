<?php
session_start();
include 'db.php'; 

// Check if ID exists in URL and fetch game
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM games WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $game = $result->fetch_assoc();
    } else {
        exit("Game not found!");
    }
} else {
    exit("No game selected!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameStore - <?= $game['title'] ?></title>
    <link rel="stylesheet" href="stylesheet.css?v=5">
</head>
<body>

    <header>
        <div class="navbar">
            <div class="logo">GameStore</div>
            <ul class="nav-links">
                <li><a href="Home.php">Home</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>

                <li><a href="cart.php">Cart</a></li>
            </ul>
        </div>
    </header>

    <main class="container">
        
        <div class="back-nav">
            <a href="Home.php" class="back-link">&larr; Back to Games</a>
        </div>

        <div class="details-wrapper">
            <div class="details-image-col">
                <img src="<?= $game['image_path'] ?>" alt="<?= $game['title'] ?>">
            </div>

            <div class="details-info-col">
                <h1 class="game-title"><?= $game['title'] ?></h1>
                <p class="game-genre"><?= $game['genre'] ?></p>
                <h2 class="game-price">$<?= $game['price'] ?></h2>

                <p class="game-description">
                    <?= $game['description'] ?>
                </p>

                <form action="cart.php" method="POST" class="cart-actions">
                    <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
                    <label class="qty-label">Quantity:</label>
                    <input type="number" name="quantity" value="1" min="1" max="10" class="qty-input">
                    <button type="submit" class="btn add-cart-btn">Add to Cart</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; Games For You</p>
    </footer>

</body>
</html>