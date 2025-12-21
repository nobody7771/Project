# checkout.php - Order Processing Page

## What This File Does

`checkout.php` handles the final step of the purchase process. It requires users to be logged in, displays a checkout form for shipping and payment details, and processes orders by saving them to the database. After successful order placement, it clears the cart and redirects to the home page.

## Role in the Project

- **Order Processing**: Creates order records in database
- **Security**: Requires user login (prevents anonymous orders)
- **Cart Validation**: Ensures cart is not empty before checkout
- **Order Storage**: Saves order to `orders` table and items to `order_items` table
- **Cart Clearing**: Removes items from cart after successful order
- **User Flow**: Final step after shopping cart (`cart.php` → `checkout.php` → `Home.php`)

## Code Breakdown

### 1. Session & Database Setup
```php
session_start();
include 'db.php';
```
- **`session_start()`**: Accesses user session (for login check and cart)
- **`include 'db.php'`**: Database connection for order storage

### 2. Security Check - Force Login
```php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
```
- **Purpose**: **Security feature** - only logged-in users can checkout
- **`$_SESSION['user_id']`**: Set during login (see `login.php`)
- **Redirect**: Sends unauthorized users to login page
- **Why Important**: Prevents anonymous orders, ensures order is linked to user account

### 3. Empty Cart Check
```php
if (empty($_SESSION['cart'])) {
    header("Location: Home.php");
    exit;
}
```
- **Purpose**: Prevents checkout with empty cart
- **Logic**: If cart is empty, redirect to home page
- **User Experience**: Prevents invalid checkout attempts

### 4. Calculate Total Price
```php
$total = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    $sql = "SELECT price FROM games WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $total += $row['price'] * $qty;
}
```
- **Purpose**: Calculates total order amount before displaying form
- **Loop**: Iterates through cart items
- **Calculation**: Sums (price × quantity) for all items
- **Used For**: Displaying order summary and storing in database

### 5. Order Submission Handler (POST Request)
```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $address = $conn->real_escape_string($_POST['address']);
```
- **Purpose**: Processes checkout form submission
- **`$user_id`**: Gets from session (logged-in user)
- **`real_escape_string()`**: **Security** - prevents SQL injection on address field

### 6. Create Order Record
```php
$order_sql = "INSERT INTO orders (user_id, total_amount, address) VALUES ('$user_id', '$total', '$address')";

if ($conn->query($order_sql) === TRUE) {
    $order_id = $conn->insert_id;
```
- **Purpose**: Creates main order record in `orders` table
- **INSERT Query**: Saves user_id, total amount, and shipping address
- **`$conn->insert_id`**: Gets the auto-generated order ID (needed for order items)
- **Success Check**: Proceeds only if order created successfully

### 7. Create Order Items Records
```php
foreach ($_SESSION['cart'] as $game_id => $quantity) {
    $price_sql = "SELECT price FROM games WHERE id = $game_id";
    $price_res = $conn->query($price_sql);
    $price_row = $price_res->fetch_assoc();
    $price = $price_row['price'];

    $item_sql = "INSERT INTO order_items (order_id, game_id, quantity, price) 
                 VALUES ('$order_id', '$game_id', '$quantity', '$price')";
    $conn->query($item_sql);
}
```
- **Purpose**: Saves each cart item as separate order item record
- **Why Separate Table**: Allows multiple items per order (normalized database design)
- **Loop**: Creates one `order_items` record for each cart item
- **Price Storage**: Stores price at time of purchase (prices might change later)
- **Links**: Each item linked to order via `order_id`

### 8. Clear Cart & Success Message
```php
unset($_SESSION['cart']);
echo "<script>alert('Order Placed Successfully! Thank you.'); window.location.href='Home.php';</script>";
exit;
```
- **`unset($_SESSION['cart'])`**: Clears shopping cart after successful order
- **JavaScript Alert**: Shows success message to user
- **Redirect**: Sends user back to home page
- **`exit`**: Stops script execution

### 9. Checkout Form HTML
```html
<form action="checkout.php" method="POST">
    <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="fullname" required placeholder="John Doe">
    </div>

    <div class="form-group">
        <label>Shipping Address</label>
        <textarea name="address" required placeholder="123 Street Name, City" rows="3"></textarea>
    </div>

    <h2>Payment Details</h2>
    <div class="form-group">
        <label>Card Number (Fake)</label>
        <input type="text" placeholder="1234 5678 9101 1121" required>
    </div>
    <!-- Expiry, CVV fields -->
    
    <button type="submit" class="btn">Place Order</button>
</form>
```
- **Purpose**: Collects shipping and payment information
- **Note**: Payment fields are "fake" (not processed) - this is a demo project
- **Address Field**: Only field actually saved to database
- **Required Fields**: All fields marked `required` for HTML5 validation

### 10. Order Summary Display
```php
<div class="checkout-summary">
    <h2>Order Summary</h2>
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
        <span>$<?php echo $total; ?></span>
    </div>
</div>
```
- **Purpose**: Shows order items and total before submission
- **Display**: Lists each item with quantity and subtotal
- **Total**: Shows grand total at bottom

## Important Functions/Features

### Security Checks
- **Login Required**: `if (!isset($_SESSION['user_id']))` prevents anonymous checkout
- **Cart Validation**: `if (empty($_SESSION['cart']))` prevents empty orders
- **SQL Injection Prevention**: `real_escape_string()` on user input

### Order Creation Process
1. **Create Order**: Insert into `orders` table
2. **Get Order ID**: `$conn->insert_id` (auto-generated)
3. **Create Items**: Loop through cart, insert each into `order_items`
4. **Clear Cart**: Remove items from session

### Database Structure
- **`orders` table**: 
  - `id` (auto-increment primary key)
  - `user_id` (foreign key to users)
  - `total_amount` (order total)
  - `address` (shipping address)
- **`order_items` table**:
  - `id` (auto-increment primary key)
  - `order_id` (foreign key to orders)
  - `game_id` (foreign key to games)
  - `quantity` (number purchased)
  - `price` (price at time of purchase)

### Transaction Logic
- **Note**: This code doesn't use database transactions
- **Risk**: If order_items insert fails, order exists but items missing
- **Improvement**: Could wrap in transaction for atomicity

## Connections to Other Files

- **Includes**: `db.php` (database connection)
- **Requires**: `login.php` (user must be logged in)
- **Receives from**: `cart.php` (user clicks "Proceed to Checkout")
- **Redirects to**: 
  - `login.php` (if not logged in)
  - `Home.php` (after successful order)
- **Uses**: `stylesheet.css` (checkout form styling)
- **Related**: `cart.php` (cart contents displayed here)

## Dependencies

- **Database Tables**: 
  - `orders` (id, user_id, total_amount, address)
  - `order_items` (id, order_id, game_id, quantity, price)
  - `games` (to fetch prices)
- **Session**: Must have `$_SESSION['user_id']` and `$_SESSION['cart']`
- **CSS**: Requires `stylesheet.css` for checkout styling

## Security Features

1. **Authentication Required**: Only logged-in users can checkout
2. **Input Sanitization**: Address field escaped to prevent SQL injection
3. **Cart Validation**: Empty cart check prevents invalid orders
4. **Price Storage**: Stores price at purchase time (prevents price change issues)

## Common Issues

1. **Redirected to login**:
   - User not logged in
   - Session expired
   - Need to login first

2. **Redirected to home**:
   - Cart is empty
   - Need to add items to cart first

3. **Order created but items missing**:
   - `order_items` insert failed
   - Database error (check `$conn->error`)
   - Missing `order_items` table

4. **"Headers already sent" error**:
   - HTML output before `header()` call
   - Whitespace before `<?php` tag

5. **Cart not cleared**:
   - `unset($_SESSION['cart'])` not executed
   - Order creation failed before cart clear
   - Session issue

6. **Total amount incorrect**:
   - Price changed in database after adding to cart
   - Calculation error in loop
   - Cart contains invalid game IDs

## Data Flow

1. User clicks "Proceed to Checkout" from `cart.php`
2. `checkout.php` checks if user is logged in
3. If logged in, displays checkout form with order summary
4. User fills shipping address and payment details
5. Form submits POST request
6. PHP creates order in `orders` table
7. PHP creates order items in `order_items` table
8. Cart is cleared
9. Success message shown
10. User redirected to home page

