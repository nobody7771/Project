# Home.php - Main Landing Page

## What This File Does

`Home.php` is the **main page** of the GameStore website. It displays all available games in a grid layout and provides a search functionality. Users can browse games, see prices, genres, and navigate to individual game detail pages.

## Role in the Project

- **Entry Point**: This is typically the first page users see when visiting the website
- **Game Catalog**: Fetches and displays all games from the database
- **Navigation Hub**: Contains links to login, register, cart, and logout pages
- **Search Interface**: Provides a search bar that filters games in real-time (using JavaScript)
- **Session Management**: Checks if user is logged in to show appropriate navigation links

## Code Breakdown

### 1. PHP Session & Database Setup
```php
session_start(); 
include 'db.php';
```
- **`session_start()`**: Starts/resumes a PHP session to track logged-in users
- **`include 'db.php'`**: Loads the database connection (`$conn` becomes available)

### 2. Database Query - Fetch All Games
```php
$sql = "SELECT * FROM games";
$result = $conn->query($sql);
```
- **Purpose**: Retrieves all games from the `games` table
- **`$sql`**: SQL query string
- **`$result`**: MySQLi result object containing all game records
- **Note**: The comment mentions search logic was removed - now JavaScript handles filtering

### 3. HTML Header with Navigation
```php
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
```
- **Dynamic Navigation**: Shows "Logout" if user is logged in, otherwise shows "Login" and "Register"
- **`$_SESSION['user_id']`**: Session variable set during login (see `login.php`)

### 4. Search Input Field
```html
<input type="text" 
       id="search-input" 
       class="search"
       placeholder="Search games..." 
       onkeyup="liveSearch()">
```
- **Purpose**: Search bar for filtering games
- **`onkeyup="liveSearch()"`**: Calls JavaScript function `liveSearch()` from `script.js` on every keystroke
- **Client-Side Filtering**: Filters games without reloading the page

### 5. Game Grid Display Loop
```php
<div class="game-grid">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            ?>
            <article class="game-card">
                <img src="<?php echo $row['image_path']; ?>" alt="<?php echo $row['title']; ?>">
                <div class="game-info">
                    <h3><?php echo $row['title']; ?></h3>
                    <p><?php echo $row['genre']; ?></p>
                    <p><strong>$<?php echo $row['price']; ?></strong></p>
                    <a href="details.php?id=<?php echo $row['id']; ?>" class="btn">View Details</a>
                </div>
            </article>
            <?php
        }
    } else {
        echo "<p>No games found!</p>";
    }
    ?>
</div>
```
- **`$result->num_rows`**: Checks if any games were found
- **`while($row = $result->fetch_assoc())`**: Loops through each game record
- **`$row`**: Associative array containing game data (id, title, genre, price, image_path, description)
- **Game Card**: Each game is displayed as a card with image, title, genre, price, and a "View Details" link
- **Link to Details**: `details.php?id=<?php echo $row['id']; ?>` passes the game ID via URL parameter

### 6. JavaScript Inclusion
```html
<script src="script.js"></script>
```
- **Purpose**: Loads `script.js` which contains the `liveSearch()` function

## Important Functions/Features

### Database Query Execution
- **Function**: `$conn->query($sql)`
- **Input**: SQL query string
- **Output**: MySQLi result object
- **Used for**: Fetching all games from database

### Session Variable Check
- **Variable**: `$_SESSION['user_id']`
- **Purpose**: Determines if user is logged in
- **Set by**: `login.php` after successful authentication
- **Used for**: Showing/hiding login/logout links

### Game Data Display
- **Loop**: `while($row = $result->fetch_assoc())`
- **Purpose**: Iterates through each game record
- **Data Accessed**: `$row['id']`, `$row['title']`, `$row['genre']`, `$row['price']`, `$row['image_path']`

## Connections to Other Files

- **Includes**: `db.php` (database connection)
- **Links to**: 
  - `login.php` (user login)
  - `register.php` (user registration)
  - `logout.php` (end session)
  - `cart.php` (shopping cart)
  - `details.php` (individual game page - via `?id=` parameter)
- **Uses**: `script.js` (search functionality)
- **Uses**: `stylesheet.css` (styling)

## Dependencies

- **PHP Session**: Must be enabled in PHP configuration
- **Database**: Requires `games` table with columns: id, title, genre, price, description, image_path
- **JavaScript**: Requires `script.js` for search functionality
- **CSS**: Requires `stylesheet.css` for styling

## Common Issues

1. **No games displayed**: 
   - Database `games` table is empty
   - Database connection failed (check `db.php`)

2. **Search not working**:
   - `script.js` file not found or not loaded
   - JavaScript errors in browser console

3. **Navigation links broken**:
   - Files (login.php, register.php, etc.) don't exist in same directory
   - Wrong file paths

4. **Images not showing**:
   - `image_path` in database points to wrong location
   - Image files don't exist at specified paths

