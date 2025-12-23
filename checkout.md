# checkout.php - Order Processing Page

## Overview
This page handles the final order submission. Users enter shipping details and payment info, then order is saved to database.

## Code Explanation

### Block 1: Security Check
```php
session_start();
include 'db.php';

// 1. Force Login (Security)
// If they are not logged in, kick them to login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
```
**Explanation:**
- `session_start()`: Starts session to check login status
- Checks if user is logged in (`$_SESSION['user_id']`)
- If not logged in → Redirects to login page
- This prevents unauthorized checkout

### Block 2: Empty Cart Check
```php
// 2. Redirect if Cart is Empty
if (empty($_SESSION['cart'])) {
    header("Location: Home.php");
    exit;
}
```
**Explanation:**
- Checks if cart has items
- If cart is empty → Redirects to home page
- Prevents checkout with no items

### Block 3: Calculate Total
```php
// Calculate Total Price
$total = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    $sql = "SELECT price FROM games WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $total += $row['price'] * $qty;
}
```
**Explanation:**
- `$total = 0`: 
  - Initializes total to zero
  - Will accumulate sum of all items
- `foreach ($_SESSION['cart'] as $id => $qty)`: 
  - **Breaking this down**:
    1. `foreach` → Loop through each item in cart array
    2. `$_SESSION['cart']` → The cart array (e.g., `[1 => 2, 3 => 1]`)
    3. `as $id => $qty` → For each item:
       - `$id` → Game ID (the key, e.g., 1, 3)
       - `$qty` → Quantity (the value, e.g., 2, 1)
    4. `=>` separates key from value in array
  - **Example**: If cart is `[1 => 2, 3 => 1]`:
    - First loop: `$id = 1`, `$qty = 2`
    - Second loop: `$id = 3`, `$qty = 1`
- `$sql = "SELECT price FROM games WHERE id = $id"`: 
  - **Breaking this down**:
    1. `SELECT price` → Only get price column (not all columns)
    2. `FROM games` → From games table
    3. `WHERE id = $id` → For this specific game
    4. `$id` is inserted into SQL
  - **Why get price again**: 
    - Prices might have changed since user added to cart
    - We want current price, not old price
    - Ensures user pays current price
- `$result = $conn->query($sql)`: 
  - Executes the query
  - Gets price from database
- `$row = $result->fetch_assoc()`: 
  - Gets the result row as array
  - Contains: `['price' => 59.99]`
- `$total += $row['price'] * $qty`: 
  - **Breaking this down**:
    1. `$row['price']` → Gets price (e.g., 59.99)
    2. `* $qty` → Multiplies by quantity (e.g., 2)
    3. Result: 59.99 × 2 = 119.98
    4. `+=` → Adds to total (same as `$total = $total + 119.98`)
  - **Example calculation**:
    - Item 1: Price $59.99 × Qty 2 = $119.98 → Total = $119.98
    - Item 2: Price $29.99 × Qty 1 = $29.99 → Total = $149.97
- **Why use database prices**: 
  - Cart only stores game IDs and quantities
  - Prices fetched fresh from database each time
  - If admin changes price, checkout uses new price
  - Fair and accurate pricing

### Block 4: Order Submission Handler
```php
// 3. HANDLE ORDER SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $address = $conn->real_escape_string($_POST['address']);
```
**Explanation:**
- Checks if checkout form was submitted
- Gets user ID from session
- Gets shipping address from form
- `real_escape_string()`: Prevents SQL injection

### Block 5: Create Order Record
```php
    // A. Insert into ORDERS table
    $order_sql = "INSERT INTO orders (user_id, total_amount, address) VALUES ('$user_id', '$total', '$address')";
    
    if ($conn->query($order_sql) === TRUE) {
        $order_id = $conn->insert_id;
```
**Explanation:**
- `$order_sql = "INSERT INTO orders (user_id, total_amount, address) VALUES ('$user_id', '$total', '$address')"`: 
  - **Breaking this SQL INSERT query down**:
    1. `INSERT INTO orders` → Add new row to orders table
    2. `(user_id, total_amount, address)` → Column names we're inserting into
    3. `VALUES (...)` → Values to insert
    4. `'$user_id'` → User ID from session (e.g., 5)
    5. `'$total'` → Total price calculated earlier (e.g., 149.97)
    6. `'$address'` → Shipping address from form
  - **Example query**: 
    ```sql
    INSERT INTO orders (user_id, total_amount, address) 
    VALUES ('5', '149.97', '123 Main St, City')
    ```
- `if ($conn->query($order_sql) === TRUE)`: 
  - **Breaking this down**:
    1. `$conn->query($order_sql)` → Executes the INSERT query
    2. Returns `true` if successful, `false` if failed
    3. `=== TRUE` → Strict comparison (checks type too)
    4. If true → Order was created successfully
- `$order_id = $conn->insert_id`: 
  - **Breaking this down**:
    1. `insert_id` → Property that contains ID of last inserted row
    2. When we INSERT a row, database auto-generates an ID
    3. `insert_id` gets that auto-generated ID
    4. `$order_id` stores it (e.g., 10, 11, 12)
  - **Why needed**: 
    - We need this ID to link order_items to this order
    - Each order_item must know which order it belongs to
  - **Example**: 
    - Order created with ID 10
    - Order items will have `order_id = 10`
    - Links them together

### Block 6: Create Order Items
```php
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
```
**Explanation:**
- `foreach ($_SESSION['cart'] as $game_id => $quantity)`: 
  - **Breaking this down**:
    1. Loops through each item in cart
    2. `$game_id` → Game ID (e.g., 1, 3, 5)
    3. `$quantity` → Quantity user ordered (e.g., 2, 1, 3)
  - **Why loop**: Cart can have multiple games, need to save each one
- `$price_sql = "SELECT price FROM games WHERE id = $game_id"`: 
  - **Breaking this down**:
    1. Gets current price from database
    2. `SELECT price` → Only gets price column (not all columns)
    3. `WHERE id = $game_id` → For this specific game
  - **Why get price again**: Prices might have changed since user added to cart
  - **Example**: If `$game_id = 1`, SQL: `"SELECT price FROM games WHERE id = 1"`
- `$price_res = $conn->query($price_sql)`: 
  - Executes the price query
  - `$price_res` stores result
- `$price_row = $price_res->fetch_assoc()`: 
  - Gets the price row as array
  - Contains: `['price' => 59.99]`
- `$price = $price_row['price']`: 
  - Extracts just the price value
  - `$price` now = 59.99 (or whatever current price is)
- `$item_sql = "INSERT INTO order_items (order_id, game_id, quantity, price) VALUES ('$order_id', '$game_id', '$quantity', '$price')"`: 
  - **Breaking this down**:
    1. `INSERT INTO order_items` → Add new row to order_items table
    2. `(order_id, game_id, quantity, price)` → Column names
    3. `VALUES (...)` → Values to insert
    4. `'$order_id'` → Links this item to the order we just created
    5. `'$game_id'` → Which game this is
    6. `'$quantity'` → How many of this game
    7. `'$price'` → Price at time of purchase (snapshot)
  - **Example**: `"INSERT INTO order_items (order_id, game_id, quantity, price) VALUES ('5', '1', '2', '59.99')"`
- `$conn->query($item_sql)`: 
  - Executes the INSERT query
  - Saves this item to database
- **Why price snapshot is important**:
  - If game price changes from $59.99 to $49.99 later
  - Order still shows $59.99 (what user paid)
  - Fair for both customer and business

### Block 7: Clear Cart and Success
```php
        // C. Clear Cart and Redirect
        unset($_SESSION['cart']);
        echo "<script>alert('Order Placed Successfully! Thank you.'); window.location.href='Home.php';</script>";
        exit;
```
**Explanation:**
- `unset($_SESSION['cart'])`: Clears cart after successful order
- Shows success alert message
- Redirects to home page
- `exit`: Stops PHP execution

### Block 8: Checkout Form
```html
<form action="checkout.php" method="POST">
    <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="fullname" required placeholder="John Doe">
    </div>
    <div class="form-group">
        <label>Shipping Address</label>
        <textarea name="address" required placeholder="123 Street Name, City" ...></textarea>
    </div>
    <h2>Payment Details</h2>
    <div class="form-group">
        <label>Card Number (Fake)</label>
        <input type="text" placeholder="1234 5678 9101 1121" required>
    </div>
```
**Explanation:**
- Form collects shipping and payment information
- **Note**: Payment fields are fake/demo - no real payment processing
- Only `address` field is saved to database
- Other fields are for UI demonstration only

### Block 9: Order Summary
```php
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
```
**Explanation:**
- `foreach ($_SESSION['cart'] as $id => $qty)`: 
  - Loops through each item in cart
  - Gets game ID and quantity
- `$sql = "SELECT title, price FROM games WHERE id = $id"`: 
  - **Breaking this down**:
    1. `SELECT title, price` → Only get these two columns (not all columns)
    2. `FROM games` → From games table
    3. `WHERE id = $id` → For this specific game
  - **Why only title and price**: 
    - We only need these for summary display
    - More efficient than getting all columns
- `$r = $conn->query($sql)`: 
  - Executes query (short variable name `$r` for result)
- `$row = $r->fetch_assoc()`: 
  - Gets row as array: `['title' => 'Elden Ring', 'price' => 59.99]`
- `$sub = $row['price'] * $qty`: 
  - Calculates subtotal: price × quantity
  - Example: 59.99 × 2 = 119.98
- `echo "<div class='summary-row'>..."`: 
  - **Breaking this down**:
    1. `echo` → Outputs HTML to page
    2. `{$row['title']}` → PHP variable inside string (curly braces needed)
    3. `(x$qty)` → Shows quantity (e.g., "x2")
    4. `$$sub` → Shows subtotal (e.g., "$119.98")
    5. **Why `$$sub`**: First `$` is literal dollar sign, second `$sub` is variable
  - **Example output**: 
    ```html
    <div class='summary-row'>
        <span>Elden Ring (x2)</span>
        <span>$119.98</span>
    </div>
    ```
- **Why echo instead of separate PHP/HTML**: 
  - This is inside PHP block, so we use echo
  - Alternative would be closing PHP and using regular HTML

## Database Structure
Order is split into two tables:
1. **orders**: Main order record (user, total, address, date)
2. **order_items**: Individual items in order (game, quantity, price snapshot)

## Key Features
1. **Security**: Requires login before checkout
2. **Price Snapshot**: Stores prices at purchase time
3. **Cart Clearing**: Automatically clears cart after order
4. **Order History**: Orders saved permanently in database
5. **Two-Step Process**: Order record + Order items

## User Flow
1. User clicks "Checkout" → Redirected if not logged in
2. User fills shipping form → Enters address
3. User reviews order summary → Sees items and total
4. User submits → Order saved to database
5. Cart cleared → Success message → Redirected to home

## Important Notes
- Payment processing is fake/demo only
- Only shipping address is saved
- Order prices are snapshots (won't change if game price changes)
- Cart is cleared after successful order

