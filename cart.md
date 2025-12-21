# cart.php - Shopping Cart Management Page

## What This File Does

`cart.php` manages the user's shopping cart. It displays all items added to the cart, allows users to update quantities, remove items, and proceed to checkout. The cart is stored in PHP sessions, so it persists across page visits (but is cleared when user logs out).

## Role in the Project

- **Cart Storage**: Uses `$_SESSION['cart']` array to store cart items
- **Add to Cart**: Receives POST requests from `details.php` to add games
- **Cart Display**: Shows cart contents in a table format with images, prices, quantities
- **Quantity Updates**: Allows real-time quantity changes with instant total recalculation (JavaScript)
- **Item Removal**: Provides remove functionality for individual items
- **Checkout Gateway**: Links to `checkout.php` for order completion

## Code Breakdown

### 1. Session & Database Setup
```php
session_start(); 
include 'db.php';
```
- **`session_start()`**: Starts/resumes session to access cart data
- **`include 'db.php'`**: Loads database connection to fetch game details

### 2. Initialize Cart Array
```php
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
```
- **Purpose**: Creates empty cart array if it doesn't exist
- **Cart Structure**: `$_SESSION['cart'][game_id] = quantity`
- **Example**: `$_SESSION['cart'][1] = 2` means game ID 1 with quantity 2

### 3. Handle Add to Cart (POST Request)
```php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['game_id'])) {
    $game_id = $_POST['game_id'];
    $quantity = $_POST['quantity'];

    // Add to session array
    if (isset($_SESSION['cart'][$game_id])) {
        $_SESSION['cart'][$game_id] += $quantity;
    } else {
        $_SESSION['cart'][$game_id] = $quantity;
    }
    
    // Redirect to self to clear POST data
    header("Location: cart.php");
    exit;
}
```
- **Purpose**: Processes "Add to Cart" requests from `details.php`
- **`$_POST['game_id']`**: Game ID being added
- **`$_POST['quantity']`**: Quantity to add
- **Logic**: 
  - If game already in cart: adds to existing quantity
  - If new game: sets initial quantity
- **Redirect**: Redirects to self to prevent duplicate additions on page refresh (POST-Redirect-GET pattern)

### 4. Handle Remove Item (GET Request)
```php
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    unset($_SESSION['cart'][$remove_id]); 
    header("Location: cart.php"); 
    exit;
}
```
- **Purpose**: Removes item from cart when user clicks "Remove"
- **`$_GET['remove']`**: Game ID to remove (from URL parameter)
- **`unset()`**: Deletes array element
- **Redirect**: Refreshes page to show updated cart

### 5. Empty Cart Check
```php
<?php if (empty($_SESSION['cart'])): ?>
    <div>
        <p>Your cart is empty.</p>
        <a href="Home.php" class="btn">Go Shopping</a>
    </div>
<?php else: ?>
```
- **Purpose**: Shows message if cart is empty
- **`empty()`**: Checks if cart array has no items
- **User Experience**: Provides link back to shopping

### 6. Cart Table Display Loop
```php
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
```
- **Purpose**: Loops through cart items and displays them
- **`foreach ($_SESSION['cart'] as $id => $qty)`**: Iterates cart array (id = game_id, qty = quantity)
- **Database Query**: Fetches game details for each cart item
- **Calculations**:
  - `$subtotal`: Price × Quantity for one item
  - `$grand_total`: Sum of all subtotals

### 7. Quantity Input with JavaScript Integration
```html
<input type="number" 
       value="<?php echo $qty; ?>" 
       min="1" 
       class="cart-qty-input" 
       data-price="<?php echo $row['price']; ?>"
       onchange="updateCart()"
       onkeyup="updateCart()">
```
- **Purpose**: Allows user to change quantity
- **`data-price`**: HTML5 data attribute stores price (used by JavaScript)
- **`onchange="updateCart()"`**: Calls JavaScript function when value changes
- **`onkeyup="updateCart()"`**: Also updates while typing
- **Note**: Changes are visual only - doesn't update session until checkout

### 8. Grand Total Display
```php
<div class="total-section">
    <p>Grand Total: <span id="grand-total-text" class="grand-total-price">$<?php echo $grand_total; ?></span></p>
    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
</div>
```
- **Purpose**: Shows total price and checkout button
- **`id="grand-total-text"`**: JavaScript updates this element
- **Checkout Link**: Only shown if cart has items

## Important Functions/Features

### Session Cart Array Structure
- **Format**: `$_SESSION['cart'][game_id] = quantity`
- **Example**: 
  ```php
  $_SESSION['cart'] = [
      1 => 2,  // Game ID 1, quantity 2
      3 => 1   // Game ID 3, quantity 1
  ];
  ```
- **Storage**: Persists across page visits (until logout or browser closes)

### Add to Cart Logic
- **If item exists**: Adds to existing quantity (`+=`)
- **If new item**: Sets initial quantity (`=`)
- **Prevents Duplicates**: Same game ID increases quantity, doesn't create duplicate entry

### Remove Item Logic
- **`unset($_SESSION['cart'][$id])`**: Removes specific game from cart
- **URL Parameter**: Uses `?remove=game_id` in URL

### Cart Calculations
- **Subtotal**: `price × quantity` for each item
- **Grand Total**: Sum of all subtotals
- **Real-time Updates**: JavaScript recalculates when quantity changes (see `script.js`)

### POST-Redirect-GET Pattern
- **Problem**: Refreshing page after POST resubmits form (adds item again)
- **Solution**: Redirect to GET after POST (`header("Location: cart.php")`)
- **Result**: Refresh only reloads GET request, doesn't resubmit POST

## Connections to Other Files

- **Includes**: `db.php` (database connection)
- **Receives POST from**: `details.php` (add to cart form)
- **Links to**: 
  - `checkout.php` (proceed to checkout)
  - `Home.php` (continue shopping)
- **Uses**: `script.js` (`updateCart()` function for real-time totals)
- **Uses**: `stylesheet.css` (cart table styling)
- **Related**: `logout.php` (clears cart when user logs out)

## Dependencies

- **Database Table**: `games` table (to fetch game details for cart items)
- **Session Storage**: PHP sessions must be enabled
- **JavaScript**: Requires `script.js` for quantity update functionality
- **CSS**: Requires `stylesheet.css` for cart styling

## Data Flow

1. **Adding Item**:
   - User clicks "Add to Cart" on `details.php`
   - Form submits POST to `cart.php` with `game_id` and `quantity`
   - PHP adds/updates item in `$_SESSION['cart']`
   - Redirects to `cart.php` (GET request)

2. **Displaying Cart**:
   - PHP loops through `$_SESSION['cart']`
   - For each item, queries database for game details
   - Displays in HTML table
   - Calculates totals

3. **Updating Quantity**:
   - User changes quantity input
   - JavaScript `updateCart()` recalculates totals instantly
   - Note: Session not updated until checkout (or could add update button)

4. **Removing Item**:
   - User clicks "Remove" link (`?remove=game_id`)
   - PHP removes item from `$_SESSION['cart']`
   - Redirects to refresh page

## Common Issues

1. **Cart empty after refresh**:
   - Session not started (`session_start()` missing)
   - Session expired (browser closed, timeout)
   - Session destroyed (logout)

2. **Item added multiple times on refresh**:
   - POST-Redirect-GET pattern not implemented
   - Need `header("Location: cart.php")` after POST

3. **Quantity changes don't persist**:
   - JavaScript updates display only
   - Need to add form submission to update session

4. **Cart cleared on logout**:
   - Expected behavior (cart stored in session)
   - Could store in database if persistence needed

5. **Grand total not updating**:
   - `script.js` not loaded
   - JavaScript errors in browser console
   - Missing `id="grand-total-text"` element

