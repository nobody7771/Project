<?php
session_start();
include 'db.php';

// STEP 1: SECURITY CHECKS
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if (empty($_SESSION['cart'])) {
    header("Location: Home.php");
    exit;
}

// STEP 2: CALCULATE TOTAL
$total_price = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    $sql = "SELECT price FROM games WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $total_price += ($row['price'] * $qty);
}

// STEP 3: PROCESS ORDER
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $address = $conn->real_escape_string($_POST['address']);
    
    // Insert Order
    $order_sql = "INSERT INTO orders (user_id, total_amount, address) VALUES ('$user_id', '$total_price', '$address')";
    
    if ($conn->query($order_sql) === TRUE) {
        $new_order_id = $conn->insert_id; 

        // Insert Items
        foreach ($_SESSION['cart'] as $game_id => $quantity) {
            $price_sql = "SELECT price FROM games WHERE id = $game_id";
            $res = $conn->query($price_sql);
            $row = $res->fetch_assoc();
            $current_price = $row['price'];

            $item_sql = "INSERT INTO order_items (order_id, game_id, quantity, price) 
                         VALUES ('$new_order_id', '$game_id', '$quantity', '$current_price')";
            $conn->query($item_sql);
        }

        unset($_SESSION['cart']); 
        echo "<script>alert('Order Placed Successfully!'); window.location.href='Home.php';</script>";
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GameStore - Checkout</title>
    <link rel="stylesheet" href="stylesheet.css?v=10">
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
        <h1>Checkout</h1>
        <br>

        <div class="checkout-wrapper">
            
            <div class="checkout-form">
                <h2>Shipping Details</h2>
                <form action="checkout.php" method="POST">
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" required placeholder="John Doe">
                    </div>

                    <div class="form-group">
                        <label>Shipping Address</label>
                        <textarea name="address" required placeholder="123 Street Name, City" rows="3" class="address-box"></textarea>
                    </div>

                    <h2 class="payment-title">Payment Details</h2>
                    
                    <div class="form-group">
                        <label>Card Number (Fake)</label>
                        <input type="text" placeholder="1234 5678 9101 1121" required>
                    </div>

                    <div class="form-row">
                        <div class="form-half">
                            <label>Expiry</label>
                            <input type="text" placeholder="MM/YY" required>
                        </div>
                        <div class="form-half">
                            <label>CVV</label>
                            <input type="text" placeholder="123" required>
                        </div>
                    </div>

                    <button type="submit" class="btn place-order-btn">Place Order</button>
                </form>
            </div>

            <div class="checkout-summary">
                <h2 class="summary-title">Order Summary</h2>
                
                <?php 
                foreach ($_SESSION['cart'] as $id => $qty) {
                    $sql = "SELECT title, price FROM games WHERE id = $id";
                    $r = $conn->query($sql);
                    $row = $r->fetch_assoc();
                    $sub = $row['price'] * $qty;
                    
                    echo "<div class='summary-row'>
                            <span>{$row['title']} (x$qty)</span>
                            <span>$$sub</span>
                          </div>";
                }
                ?>
                
                <div class="summary-row total-row">
                    <span>Total:</span>
                    <span class="final-price">$<?php echo $total_price; ?></span>
                </div>
            </div>

        </div>
    </main>

    <footer>
        <p>&copy; Games For You</p>
    </footer>
</body>
</html>