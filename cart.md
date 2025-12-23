# cart.php - Shopping Cart Page

## Overview
This page manages the shopping cart. Users can view items, update quantities, and remove items. Cart is stored in PHP session.

## Code Explanation

### Block 1: Cart Initialization
```php
session_start(); 
include 'db.php';

// 1. Initialize Cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
```
**Explanation:**
- `session_start()`: Starts session to access cart data
- `include 'db.php'`: Connects to database
- Checks if cart exists in session
- If not exists → Creates empty array
- Cart structure: `$_SESSION['cart'][game_id] = quantity`

### Block 2: Add to Cart Handler
```php
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
```
**Explanation:**
- `$_SERVER["REQUEST_METHOD"] == "POST"`: 
  - Checks if form was submitted (not just page loaded)
- `&& isset($_POST['game_id'])`: 
  - `&&` means "AND" (both conditions must be true)
  - `isset()` checks if variable exists and is not null
  - `$_POST['game_id']` checks if form field "game_id" was sent
  - **Both must be true**: Form submitted AND game_id exists
- `$game_id = $_POST['game_id']`: 
  - Gets the game ID from form (e.g., 1, 2, 3)
- `$quantity = $_POST['quantity']`: 
  - Gets quantity user selected (e.g., 1, 2, 3)
- `if (isset($_SESSION['cart'][$game_id]))`: 
  - **Breaking this down**:
    1. `$_SESSION['cart']` → Gets cart array from session
    2. `[$game_id]` → Checks specific game ID in array
    3. `isset()` → Checks if that game already exists in cart
  - **Example**: If `$game_id = 1`, checks if `$_SESSION['cart'][1]` exists
- `$_SESSION['cart'][$game_id] += $quantity`: 
  - **Breaking this down**:
    1. `+=` is addition assignment operator
    2. Same as: `$_SESSION['cart'][$game_id] = $_SESSION['cart'][$game_id] + $quantity`
    3. Adds new quantity to existing quantity
  - **Example**: If cart has 2, and user adds 1 more → becomes 3
- `$_SESSION['cart'][$game_id] = $quantity`: 
  - If game NOT in cart → Sets quantity directly
  - **Example**: First time adding game → sets to 1 (or whatever user selected)
- `header("Location: cart.php")`: 
  - **What it does**: Tells browser to go to cart.php page
  - **Why needed**: After adding item, redirects to cart page
  - **Important**: Must be called before any HTML output
- `exit`: 
  - Stops PHP execution immediately
  - Prevents any code after from running
  - **Why needed**: After redirect, we don't want to continue processing
- **Why redirect is important**:
  - Without redirect: User refreshes page → Form resubmits → Item added again
  - With redirect: User refreshes → Just reloads cart page → No duplicate addition

### Block 3: Remove from Cart Handler
```php
// 3. Handle Remove
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    unset($_SESSION['cart'][$remove_id]); 
    header("Location: cart.php"); 
    exit;
}
```
**Explanation:**
- Checks if remove parameter exists in URL (`?remove=1`)
- Gets game ID to remove
- `unset()`: Removes item from cart array
- Redirects to refresh page and show updated cart

### Block 4: Empty Cart Display
```php
<?php if (empty($_SESSION['cart'])): ?>
    <div style="background: white; padding: 20px; border-radius: 5px; color: #333;">
        <p>Your cart is empty.</p>
        <a href="Home.php" class="btn" style="margin-top: 10px;">Go Shopping</a>
    </div>
```
**Explanation:**
- Checks if cart is empty
- Shows friendly message and link to home page
- Only displays when cart has no items

### Block 5: Cart Table Loop
```php
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
**Explanation:**
- `$grand_total = 0`: 
  - Initializes total to zero
  - Will accumulate total of all items
- `foreach ($_SESSION['cart'] as $id => $qty)`: 
  - **Breaking this down**:
    1. `foreach` → Loop that goes through each item in array
    2. `$_SESSION['cart']` → The cart array (e.g., `[1 => 2, 3 => 1]`)
    3. `as $id => $qty` → For each item, get:
       - `$id` → The key (game ID, e.g., 1, 3)
       - `$qty` → The value (quantity, e.g., 2, 1)
    4. `=>` is array key-value separator
  - **Example**: If cart is `[1 => 2, 3 => 1]`:
    - First loop: `$id = 1`, `$qty = 2`
    - Second loop: `$id = 3`, `$qty = 1`
- `$sql = "SELECT * FROM games WHERE id = $id"`: 
  - **Breaking this down**:
    1. `SELECT *` → Get all columns from games table
    2. `FROM games` → From the games table
    3. `WHERE id = $id` → Only get row where id matches
    4. `$id` is inserted into SQL string
  - **Example**: If `$id = 1`, SQL becomes: `"SELECT * FROM games WHERE id = 1"`
- `$result = $conn->query($sql)`: 
  - Executes the SQL query
  - `$result` stores the query result object
- `if ($result->num_rows > 0)`: 
  - Checks if game was found in database
  - `num_rows` counts how many rows returned
  - If > 0 → Game exists → Continue
  - If = 0 → Game deleted from database → Skip this item
- `$row = $result->fetch_assoc()`: 
  - Gets game data as associative array
  - Contains: `['id' => 1, 'title' => 'Elden Ring', 'price' => 59.99, ...]`
- `$subtotal = $row['price'] * $qty`: 
  - **Breaking this down**:
    1. `$row['price']` → Gets price from database (e.g., 59.99)
    2. `* $qty` → Multiplies by quantity (e.g., 2)
    3. Result: 59.99 × 2 = 119.98
  - **Why calculate**: Shows price for this quantity of this item
- `$grand_total += $subtotal`: 
  - **Breaking this down**:
    1. `+=` is addition assignment
    2. Same as: `$grand_total = $grand_total + $subtotal`
    3. Adds this item's total to overall total
  - **Example**: 
    - Start: `$grand_total = 0`
    - Item 1: `$grand_total = 0 + 119.98 = 119.98`
    - Item 2: `$grand_total = 119.98 + 49.99 = 169.97`

### Block 6: Cart Item Display
```html
<td>
    <input type="number" 
           value="<?php echo $qty; ?>" 
           min="1" 
           class="cart-qty-input" 
           data-price="<?php echo $row['price']; ?>"
           onchange="updateCart()"
           onkeyup="updateCart()">
</td>
```
**Explanation:**
- Quantity input field
- `value`: Shows current quantity
- `min="1"`: Prevents zero or negative quantities
- `data-price`: Stores price for JavaScript calculations
- `onchange` and `onkeyup`: Calls JavaScript function when quantity changes
- JavaScript updates totals instantly without page reload

### Block 7: Remove Link
```html
<td>
    <a href="cart.php?remove=<?php echo $id; ?>" class="remove-btn">Remove</a>
</td>
```
**Explanation:**
- Link with remove parameter in URL
- Clicking removes item from cart
- Uses GET method (visible in URL)

### Block 8: Grand Total Display
```html
<div class="total-section">
    <p>Grand Total: <span id="grand-total-text" class="grand-total-price">$<?php echo $grand_total; ?></span></p>
    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
</div>
```
**Explanation:**
- Shows total price of all items
- `id="grand-total-text"`: JavaScript updates this dynamically
- Checkout button links to checkout page

## Key Features
1. **Session-Based Storage**: Cart persists across page visits
2. **Real-Time Updates**: JavaScript updates totals as quantities change
3. **Database Integration**: Fetches current prices from database
4. **Quantity Management**: Users can update quantities
5. **Item Removal**: One-click removal from cart

## Cart Structure
Cart is stored as associative array:
```php
$_SESSION['cart'] = [
    1 => 2,  // Game ID 1, Quantity 2
    3 => 1,  // Game ID 3, Quantity 1
];
```

## User Flow
1. User adds item → Stored in session
2. User views cart → Items loaded from session and database
3. User changes quantity → JavaScript updates totals instantly
4. User removes item → Deleted from session
5. User clicks checkout → Goes to checkout page

