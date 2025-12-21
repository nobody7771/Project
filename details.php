<?php
session_start(); // ADDED THIS: Must be the very first line!
include 'db.php'; 

// 1. Get the ID from the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // 2. Fetch game info from Database
    $sql = "SELECT * FROM games WHERE id = $id";
    $result = $conn->query($sql);
    
    // Check if game actually exists
    if ($result->num_rows > 0) {
        $game = $result->fetch_assoc();
    } else {
        echo "Game not found!";
        exit;
    }
} else {
    echo "No game selected!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameStore - <?php echo $game['title']; ?></title>
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
                <img src="<?php echo $game['image_path']; ?>" alt="<?php echo $game['title']; ?>">
            </div>

            <div class="details-info-col">
                <h1 class="game-title"><?php echo $game['title']; ?></h1>
                <p class="game-genre"><?php echo $game['genre']; ?></p>
                <h2 class="game-price">$<?php echo $game['price']; ?></h2>

                <p class="game-description">
                    <?php echo $game['description']; ?>
                </p>

                <form action="cart.php" method="POST" class="cart-actions">
                    <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
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