<?php
session_start(); 
include 'db.php'; 

// Fetch all games immediately
$result = $conn->query("SELECT * FROM games");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameStore - Home</title>
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
        <h1>Latest Games</h1>
        <br>
        
        <div class="search-container">
            <input type="text" id="search-input" class="search" placeholder="Search games..." onkeyup="liveSearch()">
        </div>

        <div class="game-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    
                    <article class="game-card">
                        <img src="<?= $row['image_path'] ?>" alt="<?= $row['title'] ?>">
                        <div class="game-info">
                            <h3><?= $row['title'] ?></h3>
                            <p><?= $row['genre'] ?></p>
                            <p><strong>$<?= $row['price'] ?></strong></p>
                            <a href="details.php?id=<?= $row['id'] ?>" class="btn">View Details</a>
                        </div>
                    </article>

                <?php endwhile; ?>
            <?php else: ?>
                <p>No games found!</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; Games For You</p>
    </footer>

    <script src="script.js"></script>

</body>
</html>
