# add_game.php - Add New Game (Admin Only)

## Overview
This page allows admins to add new games to the store. Includes form for game details and image upload functionality.

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
- Checks if user is logged in AND is admin
- If not admin → Redirects to login page
- Prevents unauthorized users from adding games

### Block 2: Message Variables
```php
$error = "";
$success = "";
```
**Explanation:**
- Variables to store feedback messages
- `$error`: For error messages (upload failed, database error)
- `$success`: For success messages (game added successfully)

### Block 3: Form Data Collection
```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $genre = $conn->real_escape_string($_POST['genre']);
    $price = $_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);
```
**Explanation:**
- Checks if form was submitted
- Gets form data: title, genre, price, description
- `real_escape_string()`: Prevents SQL injection
- Price not escaped (will be validated as number)

### Block 4: Image Upload Setup
```php
    // IMAGE UPLOAD LOGIC
    // 1. Get the file info
    $image_name = $_FILES['image']['name'];
    $target_dir = "images/"; // Folder where we save images
    $target_file = $target_dir . basename($image_name);
```
**Explanation:**
- `$_FILES['image']`: PHP superglobal for uploaded files
- `['name']`: Gets original filename
- `$target_dir`: Directory where images are saved
- `basename()`: Gets just filename (removes path)
- `$target_file`: Full path where image will be saved

### Block 5: Move Uploaded File
```php
    // 2. Move file to folder
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
```
**Explanation:**
- `move_uploaded_file()`: Moves file from temporary location to permanent location
- `$_FILES['image']['tmp_name']`: Temporary file location (PHP creates this)
- `$target_file`: Destination path
- Returns true if successful, false if failed

### Block 6: Save to Database
```php
        // 3. Save info to Database
        $sql = "INSERT INTO games (title, genre, price, description, image_path) 
                VALUES ('$title', '$genre', '$price', '$description', '$target_file')";

        if ($conn->query($sql) === TRUE) {
            $success = "Game added successfully!";
        } else {
            $error = "Database Error: " . $conn->error;
        }
```
**Explanation:**
- SQL INSERT query adds new game to database
- Stores: title, genre, price, description, image_path
- `image_path`: Stores file path (e.g., "images/game.jpg")
- If successful → Sets success message
- If failed → Sets error message with database error

### Block 7: Upload Error Handling
```php
    } else {
        $error = "Failed to upload image.";
    }
}
```
**Explanation:**
- If image upload fails → Sets error message
- Form won't submit if image upload fails

### Block 8: HTML Form with File Upload
```html
<form action="add_game.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label>Game Title</label>
        <input type="text" name="title" required>
    </div>
    <div class="form-group">
        <label>Genre</label>
        <input type="text" name="genre" required>
    </div>
    <div class="form-group">
        <label>Price ($)</label>
        <input type="number" step="0.01" name="price" required>
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="4" ... required></textarea>
    </div>
    <div class="form-group">
        <label>Game Cover Image</label>
        <input type="file" name="image" required>
    </div>
    <button type="submit" class="form-btn">Add Game</button>
</form>
```
**Explanation:**
- `enctype="multipart/form-data"`: **REQUIRED** for file uploads
- Without this, file upload won't work
- Form fields: title, genre, price, description, image
- `type="file"`: Creates file picker button
- `type="number" step="0.01"`: Allows decimal prices (e.g., 59.99)

## File Upload Process
1. User selects file → Browser sends file to server
2. PHP receives file → Stores in temporary location
3. `move_uploaded_file()` → Moves to permanent location (`images/` folder)
4. File path saved → Stored in database as `image_path`

## Important Notes
- **enctype Required**: Form must have `enctype="multipart/form-data"` for file uploads
- **Folder Permissions**: `images/` folder must be writable
- **File Size**: PHP has upload size limits (check php.ini)
- **File Types**: No validation for file types (should add image type check)

## Security Considerations
- Only admin can access (security check at top)
- SQL injection prevented with `real_escape_string()`
- **Missing**: File type validation (could upload non-images)
- **Missing**: File size limits
- **Missing**: Filename sanitization (could have security issues)

## User Flow
1. Admin fills form → Enters game details
2. Admin selects image → Chooses file from computer
3. Admin clicks "Add Game" → Form submits
4. PHP uploads image → Saves to `images/` folder
5. PHP saves to database → Game added to catalog
6. Success message shown → Admin can add more games

