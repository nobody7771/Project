# details.php - Game Details Page

## What This File Does

`details.php` displays detailed information about a specific game. It shows the game's image, title, genre, price, description, and provides an "Add to Cart" form. Users navigate here from the home page to see more information before purchasing.

## Role in the Project

- **Product Details**: Shows comprehensive game information
- **Add to Cart Entry Point**: Provides form to add games to shopping cart
- **Navigation**: Links back to home page and to cart
- **User Experience**: Allows users to view full details before purchasing

## Code Breakdown

### 1. Session & Database Setup
```php
session_start();
include 'db.php';
```
- **`session_start()`**: Starts session (needed for navigation to show login/logout status)
- **`include 'db.php'`**: Database connection to fetch game details

### 2. Get Game ID from URL
```php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
```
- **Purpose**: Retrieves game ID from URL parameter
- **`$_GET['id']`**: Gets ID from URL like `details.php?id=1`
- **URL Format**: `details.php?id=GAME_ID`

### 3. Fetch Game from Database
```php
$sql = "SELECT * FROM games WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $game = $result->fetch_assoc();
} else {
    echo "Game not found!";
    exit;
}
```
- **Purpose**: Queries database for game with matching ID
- **`SELECT *`**: Gets all columns (id, title, genre, price, description, image_path)
- **`fetch_assoc()`**: Gets game data as associative array
- **Error Handling**: Shows "Game not found" if ID doesn't exist
- **`exit`**: Stops script if game not found

### 4. Error Handling - No ID Provided
```php
} else {
    echo "No game selected!";
    exit;
}
```
- **Purpose**: Handles case where URL doesn't have `?id=` parameter
- **User Experience**: Prevents blank page if someone visits `details.php` directly

### 5. Page Title Dynamic
```php
<title>GameStore - <?php echo $game['title']; ?></title>
```
- **Purpose**: Sets browser tab title to game name
- **Dynamic Content**: Uses game title from database

### 6. Navigation Header
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
- **Purpose**: Consistent navigation across all pages
- **Dynamic Links**: Shows login/logout based on session status

### 7. Back Navigation Link
```php
<div class="back-nav">
    <a href="Home.php" class="back-link">&larr; Back to Games</a>
</div>
```
- **Purpose**: Easy navigation back to game list
- **User Experience**: Clear way to return to browsing

### 8. Game Details Display Layout
```php
<div class="details-wrapper">
    <div class="details-image-col">
        <img src="<?php echo $game['image_path']; ?>" alt="<?php echo $game['title']; ?>">
    </div>

    <div class="details-info-col">
        <h1 class="game-title"><?php echo $game['title']; ?></h1>
        <p class="game-genre"><?php echo $game['genre']; ?></p>
        <h2 class="game-price">$<?php echo $game['price']; ?></h2>
        <p class="game-description"><?php echo $game['description']; ?></p>
```
- **Layout**: Two-column flexbox layout (image left, info right)
- **Image**: Large game cover image
- **Information Displayed**:
  - Title (h1 heading)
  - Genre (paragraph)
  - Price (h2 heading, styled in red)
  - Description (full text paragraph)

### 9. Add to Cart Form
```php
<form action="cart.php" method="POST" class="cart-actions">
    <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
    <label class="qty-label">Quantity:</label>
    <input type="number" name="quantity" value="1" min="1" max="10" class="qty-input">
    <button type="submit" class="btn add-cart-btn">Add to Cart</button>
</form>
```
- **Purpose**: Form to add game to shopping cart
- **`action="cart.php"`**: Submits to cart page (see `cart.php` for processing)
- **Hidden Input**: `game_id` - passes game ID without user seeing it
- **Quantity Input**: 
  - `type="number"`: Numeric input with up/down arrows
  - `min="1"`: Minimum quantity is 1
  - `max="10"`: Maximum quantity is 10
  - `value="1"`: Default quantity
- **Submit Button**: "Add to Cart" button

## Important Functions/Features

### URL Parameter Handling
- **Format**: `details.php?id=GAME_ID`
- **Example**: `details.php?id=3` shows game with ID 3
- **`$_GET['id']`**: Retrieves ID from URL
- **Security Note**: ID used directly in SQL (could be vulnerable to SQL injection - should use prepared statements)

### Database Query
- **Query**: `SELECT * FROM games WHERE id = $id`
- **Returns**: Single game record with all columns
- **Error Handling**: Checks if game exists before displaying

### Add to Cart Form Submission
- **Method**: POST (data sent in request body, not URL)
- **Data Sent**:
  - `game_id`: Game ID (hidden field)
  - `quantity`: Number of items (user input)
- **Destination**: `cart.php` processes the form (see `cart.php` documentation)

### Dynamic Content Display
- **All game data**: Title, genre, price, description, image
- **Echo Statements**: `<?php echo $game['column_name']; ?>` displays database values
- **HTML Structure**: Semantic HTML with proper headings and paragraphs

## Connections to Other Files

- **Includes**: `db.php` (database connection)
- **Receives from**: `Home.php` (via link `details.php?id=GAME_ID`)
- **Submits to**: `cart.php` (add to cart form)
- **Links to**: 
  - `Home.php` (back navigation)
  - `cart.php` (view cart)
  - `login.php` / `logout.php` (navigation)
- **Uses**: `stylesheet.css` (details page styling)
- **Related**: `cart.php` (receives POST data from this page)

## Dependencies

- **Database Table**: `games` table with columns: id, title, genre, price, description, image_path
- **URL Parameter**: Requires `?id=` parameter in URL
- **CSS**: Requires `stylesheet.css` for details page layout
- **Session**: Uses session for navigation (optional, but recommended)

## Security Considerations

### SQL Injection Risk
- **Current Code**: `$sql = "SELECT * FROM games WHERE id = $id";`
- **Risk**: If `$id` contains malicious SQL, it could execute
- **Mitigation**: 
  - Currently relies on `$_GET['id']` being numeric
  - Should use prepared statements: `$stmt = $conn->prepare("SELECT * FROM games WHERE id = ?");`
  - Or validate: `$id = (int)$_GET['id'];` (casts to integer)

### Input Validation
- **Quantity**: HTML5 `min` and `max` attributes provide basic validation
- **Game ID**: Should validate that ID exists and is numeric

## Common Issues

1. **"No game selected!" error**:
   - URL missing `?id=` parameter
   - User visited `details.php` directly without ID
   - Link from home page broken

2. **"Game not found!" error**:
   - Game ID doesn't exist in database
   - Invalid ID in URL
   - Database connection issue

3. **Image not showing**:
   - `image_path` in database points to wrong location
   - Image file doesn't exist
   - Wrong file path format

4. **Add to Cart not working**:
   - Form not submitting (check browser console)
   - `cart.php` not receiving POST data
   - Session not started in `cart.php`

5. **Page layout broken**:
   - CSS file not loaded (`stylesheet.css`)
   - Missing CSS classes
   - Browser compatibility issue

6. **SQL Injection vulnerability**:
   - Current code uses direct string interpolation
   - Should use prepared statements for production

## User Flow

1. User browses games on `Home.php`
2. User clicks "View Details" on a game card
3. Browser navigates to `details.php?id=GAME_ID`
4. Page displays game details
5. User selects quantity and clicks "Add to Cart"
6. Form submits POST to `cart.php`
7. User redirected to cart page

## Data Flow

1. **URL Request**: `details.php?id=1`
2. **PHP Gets ID**: `$id = $_GET['id']`
3. **Database Query**: `SELECT * FROM games WHERE id = 1`
4. **Fetch Result**: `$game = $result->fetch_assoc()`
5. **Display**: HTML outputs `$game['title']`, `$game['price']`, etc.
6. **Form Submission**: User submits with `game_id` and `quantity`
7. **POST to Cart**: `cart.php` receives and processes

