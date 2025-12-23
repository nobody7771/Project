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
- `$_FILES['image']`: 
  - **Breaking this down**:
    1. `$_FILES` → PHP superglobal array containing uploaded files
    2. `['image']` → Gets file from form field named "image"
    3. This is a nested array with file information
  - **Structure**: `$_FILES['image']` contains:
    - `['name']` → Original filename (e.g., "game.jpg")
    - `['tmp_name']` → Temporary file location
    - `['size']` → File size in bytes
    - `['type']` → File MIME type (e.g., "image/jpeg")
- `$image_name = $_FILES['image']['name']`: 
  - Gets the original filename user uploaded
  - **Example**: If user selected "my-game.jpg", `$image_name` = "my-game.jpg"
- `$target_dir = "images/"`: 
  - Directory where we want to save the image
  - **Important**: This folder must exist and be writable
- `basename($image_name)`: 
  - **Breaking this down**:
    1. `basename()` → PHP function that extracts filename from path
    2. Removes directory path, keeps only filename
  - **Why needed**: 
    - User might upload: "C:\Users\John\Pictures\game.jpg"
    - We only want: "game.jpg"
    - Prevents security issues with paths
  - **Example**: 
    - Input: "C:\Users\John\game.jpg" → Output: "game.jpg"
    - Input: "game.jpg" → Output: "game.jpg"
- `$target_file = $target_dir . basename($image_name)`: 
  - **Breaking this down**:
    1. `.` → String concatenation (joins strings)
    2. `$target_dir` → "images/"
    3. `basename($image_name)` → "game.jpg"
    4. Result: "images/game.jpg"
  - **Final path**: Where the file will be permanently saved

### Block 5: Move Uploaded File
```php
    // 2. Move file to folder
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
```
**Explanation:**
- `move_uploaded_file()`: 
  - **Breaking this down**:
    1. PHP function that moves uploaded file
    2. Takes two parameters: source path, destination path
    3. Moves file from temporary location to permanent location
    4. Returns `true` if successful, `false` if failed
  - **Why "move" not "copy"**: 
    - Temporary files are deleted automatically
    - We move it to save it permanently
- `$_FILES['image']['tmp_name']`: 
  - **Breaking this down**:
    1. `$_FILES['image']` → Gets the uploaded file array
    2. `['tmp_name']` → Gets temporary file path
    3. PHP automatically stores uploaded file in temp directory
  - **Example temp path**: 
    - Windows: `C:\xampp\tmp\phpABC123.tmp`
    - Linux: `/tmp/phpABC123.tmp`
  - **Why temporary**: 
    - PHP stores uploads temporarily
    - We must move it before script ends
    - Temp files are deleted automatically
- `$target_file`: 
  - Destination path where file should be saved
  - **Example**: "images/game.jpg"
- `if (move_uploaded_file(...))`: 
  - Checks if move was successful
  - If `true` → File moved successfully → Continue with database save
  - If `false` → File move failed → Show error message

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

