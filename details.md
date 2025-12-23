# details.php - Game Details Page

## Overview
This page displays detailed information about a specific game. Users can view game details and add it to their cart.

## Code Explanation

### Block 1: PHP Initialization
```php
session_start(); // ADDED THIS: Must be the very first line!
include 'db.php'; 

// 1. Get the ID from the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
```
**Explanation:**
- `session_start()`: Must be first line (before any HTML output)
- Starts session to track user login status
- `include 'db.php'`: Connects to database
- `$_GET['id']`: Gets game ID from URL parameter (`details.php?id=1`)
- Checks if ID exists in URL

### Block 2: Fetch Game Data
```php
    // 2. Fetch game info from Database
    $sql = "SELECT * FROM games WHERE id = $id";
    $result = $conn->query($sql);
    
    // Check if game actually exists
    if ($result->num_rows > 0) {
        $game = $result->fetch_assoc();
    } else {
        echo "Game not found!";
        exit;
    }
```
**Explanation:**
- `$sql = "SELECT * FROM games WHERE id = $id"`: 
  - **Breaking this down**:
    1. `SELECT *` → Get all columns from games table
    2. `FROM games` → From the games table
    3. `WHERE id = $id` → Only get row where id matches
    4. `$id` is inserted into SQL string
  - **Example**: If `$id = 1`, SQL becomes: `"SELECT * FROM games WHERE id = 1"`
  - **What `*` means**: Gets all columns (id, title, genre, price, description, image_path)
- `$result = $conn->query($sql)`: 
  - Executes the SQL query
  - `$result` stores the query result object
  - Contains the database row (if found) or empty (if not found)
- `if ($result->num_rows > 0)`: 
  - **Breaking this down**:
    1. `$result->num_rows` → Counts how many rows were returned
    2. `> 0` → Checks if count is greater than zero
    3. If > 0 → Game found in database
    4. If = 0 → Game not found (maybe deleted or wrong ID)
  - **Why check**: Prevents errors if game doesn't exist
- `$game = $result->fetch_assoc()`: 
  - **Breaking this down**:
    1. `fetch_assoc()` → Gets one row as associative array
    2. Associative means: keys are column names, not numbers
    3. `$game` now contains: 
       ```php
       [
           'id' => 1,
           'title' => 'Elden Ring',
           'genre' => 'Action RPG',
           'price' => 59.99,
           'description' => 'An epic adventure...',
           'image_path' => 'Elden.jpg'
       ]
       ```
  - **Why "assoc"**: 
    - Regular array: `$game[0]`, `$game[1]` (hard to remember what each is)
    - Associative: `$game['title']`, `$game['price']` (clear what each is)
- `else { echo "Game not found!"; exit; }`: 
  - If game not found → Shows error message
  - `exit` → Stops PHP execution
  - Prevents page from trying to display non-existent game

### Block 3: Error Handling
```php
} else {
    echo "No game selected!";
    exit;
}
```
**Explanation:**
- If no ID in URL → Shows error message
- `exit`: Stops PHP execution
- Prevents page from loading without game ID

### Block 4: Game Image Display
```html
<div class="details-image-col">
    <img src="<?php echo $game['image_path']; ?>" alt="<?php echo $game['title']; ?>">
</div>
```
**Explanation:**
- Displays game cover image
- `image_path`: File path stored in database
- `alt`: Text shown if image fails to load

### Block 5: Game Information Display
```html
<div class="details-info-col">
    <h1 class="game-title"><?php echo $game['title']; ?></h1>
    <p class="game-genre"><?php echo $game['genre']; ?></p>
    <h2 class="game-price">$<?php echo $game['price']; ?></h2>
    <p class="game-description">
        <?php echo $game['description']; ?>
    </p>
```
**Explanation:**
- Displays game title, genre, price, and description
- All data comes from `$game` array (from database)
- Styled with CSS classes for consistent appearance

### Block 6: Add to Cart Form
```html
<form action="cart.php" method="POST" class="cart-actions">
    <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
    <label class="qty-label">Quantity:</label>
    <input type="number" name="quantity" value="1" min="1" max="10" class="qty-input">
    <button type="submit" class="btn add-cart-btn">Add to Cart</button>
</form>
```
**Explanation:**
- Form submits to `cart.php`
- `method="POST"`: Sends data securely
- Hidden input: Passes game ID without showing it
- Quantity input: User can select 1-10 items
- `min="1" max="10"`: HTML5 validation limits
- Submit button: Adds item to cart

### Block 7: Back Navigation
```html
<div class="back-nav">
    <a href="Home.php" class="back-link">&larr; Back to Games</a>
</div>
```
**Explanation:**
- Link to return to home page
- Arrow symbol (`&larr;`) for visual indication
- Helps user navigate back to game list

## URL Structure
Page accessed via URL parameter:
- `details.php?id=1` → Shows game with ID 1
- `details.php?id=5` → Shows game with ID 5
- `details.php` → Shows error (no ID provided)

## Key Features
1. **Dynamic Content**: All data loaded from database
2. **Error Handling**: Checks for valid game ID
3. **Add to Cart**: Direct integration with cart system
4. **Quantity Selection**: Users can add multiple items
5. **Responsive Layout**: Two-column layout (image + info)

## User Flow
1. User clicks "View Details" on home page → URL includes game ID
2. Page loads → PHP fetches game data from database
3. Game details displayed → Image, title, price, description
4. User selects quantity → Clicks "Add to Cart"
5. Form submits → Item added to cart → User redirected to cart page

## Security Notes
- Game ID comes from URL (user-controlled)
- No SQL injection protection here (should use prepared statements)
- For learning purposes, assumes valid numeric IDs

