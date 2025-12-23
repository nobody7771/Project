# Home.php - Main Landing Page

## Overview
This is the homepage that displays all available games in a grid layout. Users can browse games and search for them.

## Code Explanation

### Block 1: PHP Initialization
```php
session_start(); 
include 'db.php'; 

// Fetch all games immediately
$result = $conn->query("SELECT * FROM games");
```
**Explanation:**
- `session_start()`: 
  - Starts PHP session to track user login status
  - Must be called before any HTML output
  - Creates/accesses session file on server
- `include 'db.php'`: 
  - **Breaking this down**:
    1. `include` → Loads and executes code from db.php file
    2. This gives us access to `$conn` (database connection)
    3. Like copying all code from db.php into this file
  - **Why include**: 
    - Don't repeat database connection code
    - Change connection in one place (db.php) affects all files
- `$result = $conn->query("SELECT * FROM games")`: 
  - **Breaking this down**:
    1. `$conn` → Database connection object (from db.php)
    2. `->query()` → Method that executes SQL query
    3. `"SELECT * FROM games"` → SQL query string
       - `SELECT *` → Get all columns
       - `FROM games` → From games table
    4. `$result` → Stores query result object
  - **What $result contains**: 
    - Not the actual data yet
    - An object that can fetch rows
    - Use `fetch_assoc()` to get actual data

### Block 2: HTML Head Section
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameStore - Home</title>
    <link rel="stylesheet" href="stylesheet.css?v=5">
</head>
```
**Explanation:**
- Standard HTML5 document structure
- `viewport` meta tag makes page responsive on mobile devices
- Links to CSS stylesheet (v=5 prevents browser caching old CSS)

### Block 3: Navigation Bar
```php
<?php if (isset($_SESSION['user_id'])): ?>
    <li><a href="logout.php">Logout</a></li>
<?php else: ?>
    <li><a href="login.php">Login</a></li>
    <li><a href="register.php">Register</a></li>
<?php endif; ?>
```
**Explanation:**
- `if (isset($_SESSION['user_id']))`: 
  - **Breaking this down**:
    1. `isset()` → Checks if variable exists and is not null
    2. `$_SESSION['user_id']` → Gets user ID from session
    3. If user logged in → `$_SESSION['user_id']` exists → `isset()` = true
    4. If user not logged in → `$_SESSION['user_id']` doesn't exist → `isset()` = false
  - **Why check user_id**: 
    - When user logs in, `$_SESSION['user_id']` is set
    - When user logs out, it's removed
    - This tells us if user is logged in
- `:` → Alternative syntax for `if` statement
  - Instead of `{ }`, uses `:` and `endif`
  - Same functionality, different style
- **If logged in** (inside first block):
  - Shows "Logout" link
  - User can log out
- **If not logged in** (inside `else` block):
  - Shows "Login" and "Register" links
  - User can create account or log in
- `endif`: 
  - Closes the `if` statement (alternative syntax)
  - Same as closing `}` in regular syntax

### Block 4: Search Bar
```html
<div class="search-container">
    <input type="text" id="search-input" class="search" placeholder="Search games..." onkeyup="liveSearch()">
</div>
```
**Explanation:**
- Creates a search input field
- `onkeyup="liveSearch()"`: Calls JavaScript function every time user types
- This enables live/filtering search without page reload

### Block 5: Game Grid Display
```php
<?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <article class="game-card">
            <img src="<?= $row['image_path'] ?>" alt="<?= $row['title'] ?>">
            <div class="game-info">
                <h3><?= $row['title'] ?></h3>
                <p><?= $row['genre'] ?></p>
                <p><strong>$<?= $row['price'] ?></strong></p>
                <a href="details.php?id=<?= $row['id'] ?>" class="btn">View Details</a>
            </div>
        </article>
    <?php endwhile; ?>
<?php else: ?>
    <p>No games found!</p>
<?php endif; ?>
```
**Explanation:**
- `if ($result->num_rows > 0)`: 
  - **Breaking this down**:
    1. `$result->num_rows` → Counts how many rows query returned
    2. `> 0` → Checks if count is greater than zero
    3. If > 0 → Games exist → Show games
    4. If = 0 → No games → Show "No games found" message
  - **Why check**: Prevents errors if database is empty
- `while($row = $result->fetch_assoc())`: 
  - **Breaking this complex line down**:
    1. `while` → Loop that repeats while condition is true
    2. `$row = $result->fetch_assoc()` → 
       - `fetch_assoc()` → Gets one row as associative array
       - `= $row` → Stores it in variable
       - Returns the row (or false if no more rows)
    3. Loop continues while `fetch_assoc()` returns a row
    4. When no more rows → Returns false → Loop stops
  - **How it works**:
    - First loop: Gets first game → `$row = ['id' => 1, 'title' => 'Elden Ring', ...]`
    - Second loop: Gets second game → `$row = ['id' => 2, 'title' => 'Dispatch', ...]`
    - Continues until no more games
- `<?= $row['image_path'] ?>`: 
  - **Breaking this down**:
    1. `<?= ?>` → Short PHP echo syntax
    2. Same as: `<?php echo $row['image_path']; ?>`
    3. `$row['image_path']` → Gets image path from array
    4. Outputs: "Elden.jpg" (or whatever path is)
  - **Why `<?= ?>`**: Shorter syntax for simple output
- `<img src="<?= $row['image_path'] ?>" alt="<?= $row['title'] ?>">`: 
  - **Breaking this down**:
    1. `src="<?= $row['image_path'] ?>"` → Image source (file path)
    2. `alt="<?= $row['title'] ?>"` → Alternative text (shown if image fails)
    3. Both values come from database
  - **Example HTML output**: 
    ```html
    <img src="Elden.jpg" alt="Elden Ring">
    ```
- `<a href="details.php?id=<?= $row['id'] ?>">`: 
  - **Breaking this down**:
    1. Creates link to details.php
    2. `?id=` → URL parameter (query string)
    3. `<?= $row['id'] ?>` → Game ID inserted into URL
    4. Result: `details.php?id=1` (or whatever the ID is)
  - **Why URL parameter**: 
    - Passes game ID to details.php
    - details.php uses `$_GET['id']` to get it
    - Allows dynamic page content

### Block 6: JavaScript Include
```html
<script src="script.js"></script>
```
**Explanation:**
- Loads JavaScript file for search functionality
- Must be at bottom of page so HTML loads first

## User Flow
1. User visits page → PHP fetches all games from database
2. Games displayed in grid → User can browse
3. User types in search → JavaScript filters games instantly
4. User clicks "View Details" → Goes to details.php with game ID

## Key Features
- **Dynamic Content**: Games loaded from database, not hardcoded
- **Live Search**: Filters games as you type (JavaScript)
- **Responsive Design**: CSS Grid adapts to screen size
- **Session Aware**: Navigation changes based on login status

