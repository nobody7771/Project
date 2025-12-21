<?php
session_start(); 
include 'db.php'; 

// CHANGED: We removed the PHP Search logic. 
// Now we simply select ALL games, and JS filters them visually.
$sql = "SELECT * FROM games";
$result = $conn->query($sql);
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
            <input type="text" 
                   id="search-input" 
                   class="search"
                   placeholder="Search games..." 
                   onkeyup="liveSearch()">
        </div>

        <div class="game-grid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <article class="game-card">
                        <img src="<?php echo $row['image_path']; ?>" alt="<?php echo $row['title']; ?>">
                        <div class="game-info">
                            <h3><?php echo $row['title']; ?></h3>
                            <p><?php echo $row['genre']; ?></p>
                            <p><strong>$<?php echo $row['price']; ?></strong></p>
                            <a href="details.php?id=<?php echo $row['id']; ?>" class="btn">View Details</a>
                        </div>
                    </article>
                    <?php
                }
            } else {
                echo "<p>No games found!</p>";
            }
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; Games For You</p>
    </footer>

    <script src="script.js"></script>

</body>
</html>