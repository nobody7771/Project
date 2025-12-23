# admin.php - Admin Dashboard

## Overview
This page is the admin control panel. Only users with username "admin" can access it. Shows all games and orders for management.

## Code Explanation

### Block 1: Security Check
```php
session_start();
include 'db.php';

// 1. SECURITY CHECK: Only 'admin' can see this page
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit;
}
```
**Explanation:**
- `session_start()`: Starts session to check login status
- **Breaking down the security check**:
  - `!isset($_SESSION['user_id'])` → Checks if user is NOT logged in
    - `isset()` returns true if variable exists
    - `!` means "NOT" (negation)
    - So `!isset()` means "does NOT exist"
  - `||` → Logical OR operator (means "OR")
  - `$_SESSION['username'] !== 'admin'` → Checks if username is NOT "admin"
    - `!==` → Strict "not equal" comparison
    - Checks both value AND type
  - **Combined meaning**: If user NOT logged in OR username NOT "admin" → Redirect
- `header("Location: login.php")`: 
  - Sends HTTP header to redirect browser
  - Browser automatically goes to login.php
- `exit`: 
  - Stops PHP execution immediately
  - Prevents any code below from running
- **Why both checks**: 
  - First check: Prevents non-logged-in users
  - Second check: Prevents logged-in non-admin users
  - Both must pass for access

### Block 2: Fetch Games
```php
// Fetch All Games
$games_sql = "SELECT * FROM games";
$games_result = $conn->query($games_sql);
```
**Explanation:**
- SQL query gets all games from database
- `$games_result`: Stores query result
- Used to display games table

### Block 3: Fetch Orders
```php
// Fetch All Orders
$orders_sql = "SELECT orders.id, users.username, orders.total_amount, orders.order_date 
               FROM orders 
               JOIN users ON orders.user_id = users.id 
               ORDER BY order_date DESC";
$orders_result = $conn->query($orders_sql);
```
**Explanation:**
- **Breaking down this complex SQL query step by step**:
  1. `SELECT orders.id, users.username, orders.total_amount, orders.order_date`
     - `SELECT` → Choose which columns to get
     - `orders.id` → Gets ID from orders table
     - `users.username` → Gets username from users table
     - `orders.total_amount` → Gets total from orders table
     - `orders.order_date` → Gets date from orders table
     - **Why prefix with table name**: When joining tables, columns might have same names, so we specify which table
  2. `FROM orders` → Start with orders table
  3. `JOIN users` → Also include users table
  4. `ON orders.user_id = users.id` → **This is the connection**:
     - `orders.user_id` → Foreign key in orders table
     - `=` → Matches
     - `users.id` → Primary key in users table
     - **Meaning**: Link orders to users where order's user_id matches user's id
  5. `ORDER BY order_date DESC` → Sort results:
     - `ORDER BY` → Sort the results
     - `order_date` → Sort by this column
     - `DESC` → Descending order (newest first)
     - `ASC` would be oldest first
- **What JOIN does**:
  - Orders table has: `id, user_id, total_amount, order_date`
  - Users table has: `id, username, email, password`
  - JOIN combines them: `order_id, username, total, date`
  - **Example result**:
    ```
    Order ID: 1, Username: john, Total: $59.99, Date: 2024-01-15
    Order ID: 2, Username: jane, Total: $29.99, Date: 2024-01-14
    ```
- `$orders_result`: Stores the combined query result

### Block 4: Games Management Header
```html
<div class="admin-header">
    <h2>Manage Games</h2>
    <a href="add_game.php" class="add-btn">+ Add New Game</a>
</div>
```
**Explanation:**
- Header section with title and "Add Game" button
- Button links to `add_game.php` for adding new games

### Block 5: Games Table
```php
<?php while($game = $games_result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $game['id']; ?></td>
        <td><img src="<?php echo $game['image_path']; ?>" style="width: 50px;"></td>
        <td><?php echo $game['title']; ?></td>
        <td>$<?php echo $game['price']; ?></td>
        <td>
            <a href="edit_game.php?id=<?php echo $game['id']; ?>">Edit</a>
            <a href="delete_game.php?id=<?php echo $game['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
        </td>
    </tr>
<?php endwhile; ?>
```
**Explanation:**
- Loops through each game in database
- Displays: ID, thumbnail image, title, price
- Edit link: Goes to `edit_game.php` with game ID
- Delete link: Goes to `delete_game.php` with game ID
- `confirm()`: JavaScript confirmation dialog before delete

### Block 6: Orders Table
```php
<?php while($order = $orders_result->fetch_assoc()): ?>
    <tr>
        <td>#<?php echo $order['id']; ?></td>
        <td><?php echo $order['username']; ?></td>
        <td>$<?php echo $order['total_amount']; ?></td>
        <td><?php echo $order['order_date']; ?></td>
    </tr>
<?php endwhile; ?>
```
**Explanation:**
- Loops through each order
- Displays: Order ID, customer username, total amount, order date
- Shows order history for admin review

## Admin Features
1. **View All Games**: See complete game catalog
2. **Edit Games**: Modify game details
3. **Delete Games**: Remove games from catalog
4. **View Orders**: See all customer orders
5. **Order History**: Track sales and customers

## Security
- **Access Control**: Only "admin" username can access
- **Session Check**: Must be logged in
- **Double Verification**: Checks both login status AND username

## Database Queries
1. **Games Query**: Simple SELECT all games
2. **Orders Query**: JOIN query to combine orders with user data
   - Links `orders.user_id` to `users.id`
   - Gets username from users table

## User Flow
1. Admin logs in → Username "admin" → Redirected to admin dashboard
2. Admin sees games table → Can edit or delete games
3. Admin sees orders table → Can view order history
4. Admin clicks "Add New Game" → Goes to add_game.php

## Important Notes
- Admin username is hardcoded as "admin"
- No separate admin table - uses username check
- Delete has confirmation dialog to prevent accidents
- Orders are read-only (no edit/delete functionality)

