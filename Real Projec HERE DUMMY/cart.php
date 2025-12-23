<?php
session_start(); 
include 'db.php';

// STEP 1: CREATE CART
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// STEP 2: UPDATE CART
if (isset($_POST['update_btn'])) {
    foreach ($_POST['quantities'] as $id => $qty) {
        $qty = (int)$qty; 
        if ($qty < 1) $qty = 1;
        $_SESSION['cart'][$id] = $qty;
    }
    header("Location: cart.php");
    exit;
}

// STEP 3: ADD ITEM
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['game_id'])) {
    $game_id = $_POST['game_id'];
    $quantity = $_POST['quantity'];

    if (isset($_SESSION['cart'][$game_id])) {
        $_SESSION['cart'][$game_id] += $quantity;
    } else {
        $_SESSION['cart'][$game_id] = $quantity;
    }
    
    header("Location: cart.php");
    exit;
}

// STEP 4: REMOVE ITEM
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]); 
    header("Location: cart.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GameStore - Shopping Cart</title>
    <link rel="stylesheet" href="stylesheet.css">
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
            
            <div class="empty-cart-msg">
                <p>Your cart is empty.</p>
                <a href="Home.php" class="btn">Go Shopping</a>
            </div>

        <?php else: ?>

            <form action="cart.php" method="POST">
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
                                               name="quantities[<?php echo $id; ?>]" 
                                               value="<?php echo $qty; ?>" 
                                               min="1" 
                                               class="cart-qty-input" 
                                               data-price="<?php echo $row['price']; ?>"
                                               onchange="updateCart()" 
                                               onkeyup="updateCart()">
                                    </td>
                                    <td><span class="row-total">$<?php echo $subtotal; ?></span></td>
                                    <td><a href="cart.php?remove=<?php echo $id; ?>" class="remove-btn">Remove</a></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <div class="cart-actions-row">
                    <button type="submit" name="update_btn" class="btn update-btn">Update Cart</button>
                    
                    <div class="cart-total-box">
                        <p>Grand Total: <span id="grand-total-text">$<?php echo $grand_total; ?></span></p>
                        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                    </div>
                </div>

            </form>

        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; Games For You</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>