<?php
session_start();
include 'db.php';

// 1. Force Login (Security)
// If they are not logged in, kick them to login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Redirect if Cart is Empty
if (empty($_SESSION['cart'])) {
    header("Location: Home.php");
    exit;
}

// Calculate Total Price
$total = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    $sql = "SELECT price FROM games WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $total += $row['price'] * $qty;
}

// 3. HANDLE ORDER SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $address = $conn->real_escape_string($_POST['address']);
    
    // A. Insert into ORDERS table
    $order_sql = "INSERT INTO orders (user_id, total_amount, address) VALUES ('$user_id', '$total', '$address')";
    
    if ($conn->query($order_sql) === TRUE) {
        $order_id = $conn->insert_id; 

        // B. Insert items into ORDER_ITEMS table
        foreach ($_SESSION['cart'] as $game_id => $quantity) {
            $price_sql = "SELECT price FROM games WHERE id = $game_id";
            $price_res = $conn->query($price_sql);
            $price_row = $price_res->fetch_assoc();
            $price = $price_row['price'];

            $item_sql = "INSERT INTO order_items (order_id, game_id, quantity, price) 
                         VALUES ('$order_id', '$game_id', '$quantity', '$price')";
            $conn->query($item_sql);
        }

        // C. Clear Cart and Redirect
        unset($_SESSION['cart']);
        echo "<script>alert('Order Placed Successfully! Thank you.'); window.location.href='Home.php';</script>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameStore - Checkout</title>
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
        <h1>Checkout</h1>
        <br>

        <div class="checkout-wrapper">
            
            <div class="checkout-form">
                <h2 style="margin-bottom: 20px;">Shipping Details</h2>
                <form action="checkout.php" method="POST">
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" required placeholder="John Doe">
                    </div>

                    <div class="form-group">
                        <label>Shipping Address</label>
                        <textarea name="address" required placeholder="123 Street Name, City" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 3px;" rows="3"></textarea>
                    </div>

                    <h2 style="margin: 20px 0;">Payment Details</h2>
                    <div class="form-group">
                        <label>Card Number (Fake)</label>
                        <input type="text" placeholder="1234 5678 9101 1121" required>
                    </div>

                    <div class="form-group" style="display: flex; gap: 10px;">
                        <div style="flex: 1;">
                            <label>Expiry</label>
                            <input type="text" placeholder="MM/YY" required>
                        </div>
                        <div style="flex: 1;">
                            <label>CVV</label>
                            <input type="text" placeholder="123" required>
                        </div>
                    </div>

                    <button type="submit" class="btn" style="width: 100%; margin-top: 20px; border: none; font-size: 1.2rem; cursor: pointer;">Place Order</button>
                </form>
            </div>

            <div class="checkout-summary">
                <h2 style="margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px;">Order Summary</h2>
                
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
                    <span style="color: #e50707;">$<?php echo $total; ?></span>
                </div>
            </div>

        </div>
    </main>

    <footer>
        <p>&copy; Games For You</p>
    </footer>

</body>
</html>