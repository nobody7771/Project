# edit_game.php - Edit Game (Admin Only)

## Overview
This page allows admins to edit existing games. Pre-fills form with current game data and allows updating all fields including optional image replacement.

## Code Explanation

### Block 1: Security Check
```php
session_start();
include 'db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit;
}
```
**Explanation:**
- Same security check as add_game.php
- Only admin can access this page
- Prevents unauthorized editing

### Block 2: Get Game ID
```php
$id = $_GET['id'];
$error = "";
$success = "";
```
**Explanation:**
- Gets game ID from URL (`edit_game.php?id=1`)
- Initializes message variables
- ID used to identify which game to edit

### Block 3: Fetch Current Game Data
```php
// 1. Fetch current game data
$sql = "SELECT * FROM games WHERE id = $id";
$result = $conn->query($sql);
$game = $result->fetch_assoc();
```
**Explanation:**
- Queries database for game with matching ID
- `fetch_assoc()`: Gets game data as array
- Stores in `$game` variable
- Used to pre-fill form fields

### Block 4: Form Submission Handler
```php
// 2. Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $genre = $conn->real_escape_string($_POST['genre']);
    $price = $_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);
```
**Explanation:**
- Checks if form was submitted
- Gets updated form data
- Sanitizes input to prevent SQL injection

### Block 5: Image Upload Check
```php
    // Check if new image was uploaded
    if (!empty($_FILES['image']['name'])) {
        $image_name = $_FILES['image']['name'];
        $target_dir = "images/";
        $target_file = $target_dir . basename($image_name);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        
        // Update query WITH image
        $update_sql = "UPDATE games SET title='$title', genre='$genre', price='$price', description='$description', image_path='$target_file' WHERE id=$id";
    } else {
        // Update query WITHOUT image (keep old one)
        $update_sql = "UPDATE games SET title='$title', genre='$genre', price='$price', description='$description' WHERE id=$id";
    }
```
**Explanation:**
- `!empty($_FILES['image']['name'])`: 
  - **Breaking this down**:
    1. `$_FILES['image']` → Gets uploaded file array
    2. `['name']` → Gets filename (empty if no file uploaded)
    3. `empty()` → Checks if value is empty/null/0/false
    4. `!empty()` → Checks if value is NOT empty
  - **What it checks**: 
    - If user uploaded file → `['name']` has value → `!empty()` = true
    - If user didn't upload → `['name']` is empty → `!empty()` = false
  - **Why this check**: Image upload is optional in edit form
- **If image uploaded** (inside `if` block):
  - Processes file upload (same as add_game.php)
  - Creates UPDATE query that includes `image_path='$target_file'`
  - **Example query**: 
    ```sql
    UPDATE games SET title='New Title', genre='Action', price='59.99', 
    description='New desc', image_path='images/new-image.jpg' WHERE id=1
    ```
- **If no image** (inside `else` block):
  - Skips file upload process
  - Creates UPDATE query WITHOUT `image_path`
  - Database keeps old image_path value
  - **Example query**: 
    ```sql
    UPDATE games SET title='New Title', genre='Action', price='59.99', 
    description='New desc' WHERE id=1
    ```
- **Why two queries**: 
  - If image uploaded → Must update image_path
  - If no image → Don't change image_path (keep old one)
  - SQL doesn't allow "update only if exists" easily, so we use two queries

### Block 6: Execute Update
```php
    if ($conn->query($update_sql) === TRUE) {
        $success = "Game updated! Redirecting...";
        header("refresh:1;url=admin.php");
    } else {
        $error = "Error: " . $conn->error;
    }
}
```
**Explanation:**
- Executes UPDATE query
- If successful → Shows success message → Redirects to admin page after 1 second
- If failed → Shows error message
- `refresh:1`: Waits 1 second before redirect

### Block 7: Pre-filled Form Fields
```html
<input type="text" name="title" value="<?php echo $game['title']; ?>" required>
<input type="text" name="genre" value="<?php echo $game['genre']; ?>" required>
<input type="number" step="0.01" name="price" value="<?php echo $game['price']; ?>" required>
<textarea name="description" ...><?php echo $game['description']; ?></textarea>
```
**Explanation:**
- `value="<?php echo $game['title']; ?>"`: Pre-fills field with current value
- User can see current data and modify it
- Makes editing easier - don't have to retype everything

### Block 8: Current Image Display
```html
<div class="form-group">
    <label>New Image (Optional)</label>
    <input type="file" name="image">
    <p>Current: <img src="<?php echo $game['image_path']; ?>" style="height: 50px; vertical-align: middle;"></p>
</div>
```
**Explanation:**
- Shows current image as thumbnail
- Image upload is optional (no `required` attribute)
- Admin can see current image before deciding to replace it

## Key Differences from Add Game
1. **Pre-filled Form**: Shows current game data
2. **Optional Image**: Image upload not required
3. **UPDATE Query**: Uses UPDATE instead of INSERT
4. **WHERE Clause**: Updates specific game by ID
5. **Image Handling**: Keeps old image if no new one uploaded

## UPDATE Query Structure
```sql
UPDATE games 
SET title='...', genre='...', price='...', description='...', image_path='...' 
WHERE id=$id
```
- `UPDATE`: Modifies existing record
- `SET`: Specifies which fields to update
- `WHERE`: Identifies which record to update (by ID)

## User Flow
1. Admin clicks "Edit" on game → Goes to edit_game.php?id=X
2. Form pre-filled → Shows current game data
3. Admin modifies fields → Changes title, price, etc.
4. Admin optionally uploads new image → Or keeps old one
5. Admin clicks "Update Game" → Form submits
6. Database updated → Success message → Redirects to admin page

## Important Notes
- Image upload is optional (unlike add_game.php)
- Old image is kept if no new image uploaded
- Form action includes game ID: `edit_game.php?id=<?php echo $id; ?>`
- Uses UPDATE SQL statement instead of INSERT

