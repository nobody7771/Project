<?php
session_start(); 
include 'db.php';

// 1. Initialize Cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 2. HANDLE ADD TO CART
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['game_id'])) {
    $game_id = $_POST['game_id'];
    $quantity = $_POST['quantity'];

    // Add to session array
    if (isset($_SESSION['cart'][$game_id])) {
        $_SESSION['cart'][$game_id] += $quantity;
    } else {
        $_SESSION['cart'][$game_id] = $quantity;
    }
    
    // --- THE FIX IS HERE ---
    // Redirect to self to clear the POST data so refresh doesn't add it again
    header("Location: cart.php");
    exit;
}

// 3. Handle Remove
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    unset($_SESSION['cart'][$remove_id]); 
    header("Location: cart.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameStore - Shopping Cart</title>
    <link rel="stylesheet" href="stylesheet.css?v=6">
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
        <h1>Your Shopping Cart</h1>
        <br>

        <?php if (empty($_SESSION['cart'])): ?>
            <div style="background: white; padding: 20px; border-radius: 5px; color: #333;">
                <p>Your cart is empty.</p>
                <a href="Home.php" class="btn" style="margin-top: 10px;">Go Shopping</a>
            </div>
        <?php else: ?>

            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Game</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grand_total = 0;
                    foreach ($_SESSION['cart'] as $id => $qty) {
                        $sql = "SELECT * FROM games WHERE id = $id";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $subtotal = $row['price'] * $qty;
                            $grand_total += $subtotal;
                            ?>
                            <tr>
                                <td><img src="<?php echo $row['image_path']; ?>" class="cart-img"></td>
                                <td><?php echo $row['title']; ?></td>
                                <td>$<?php echo $row['price']; ?></td>
                                
                                <td>
                                    <input type="number" 
                                           value="<?php echo $qty; ?>" 
                                           min="1" 
                                           class="cart-qty-input" 
                                           data-price="<?php echo $row['price']; ?>"
                                           onchange="updateCart()"
                                           onkeyup="updateCart()">
                                </td>

                                <td><span class="row-total">$<?php echo $subtotal; ?></span></td>
                                
                                <td>
                                    <a href="cart.php?remove=<?php echo $id; ?>" class="remove-btn">Remove</a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>

            <div class="total-section">
                <p>Grand Total: <span id="grand-total-text" class="grand-total-price">$<?php echo $grand_total; ?></span></p>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </div>

        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; Games For You</p>
    </footer>

    <script src="script.js"></script>

</body>
</html>