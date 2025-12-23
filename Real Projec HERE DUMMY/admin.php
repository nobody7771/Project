<?php
session_start();
include 'db.php';

// 1. SECURITY CHECK: Only 'admin' can see this page
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch All Games
$games_result = $conn->query("SELECT * FROM games");

// Fetch All Orders (Joined with Users to see names)
$orders_sql = "SELECT orders.id, users.username, orders.total_amount, orders.order_date 
               FROM orders 
               JOIN users ON orders.user_id = users.id 
               ORDER BY order_date DESC";
$orders_result = $conn->query($orders_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>

    <header>
        <div class="navbar">
            <div class="logo">Admin Panel</div>
            <ul class="nav-links">
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </header>

    <main class="container">
        <h1>Admin Dashboard</h1>
        <br>

        <div class="admin-header">
            <h2>Manage Games</h2>
            <a href="add_game.php" class="add-btn">+ Add New Game</a>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($game = $games_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $game['id'] ?></td>
                    <td><img src="<?= $game['image_path'] ?>" class="admin-thumb"></td>
                    <td><?= $game['title'] ?></td>
                    <td>$<?= $game['price'] ?></td>
                    <td>
                        <a href="edit_game.php?id=<?= $game['id'] ?>" class="edit-btn">Edit</a>
                        
                        <a href="delete_game.php?id=<?= $game['id'] ?>" 
                           class="delete-btn"
                           onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Recent Orders</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = $orders_result->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $order['id'] ?></td>
                    <td><?= $order['username'] ?></td>
                    <td>$<?= $order['total_amount'] ?></td>
                    <td><?= $order['order_date'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </main>

    <footer>
        <p>&copy; Games For You</p>
    </footer>

</body>
</html>