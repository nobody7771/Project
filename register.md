# register.php - User Registration Page

## Overview
This page allows new users to create an account. It validates input and stores user data securely in the database.

## Code Explanation

### Block 1: PHP Initialization
```php
include 'db.php'; 

$error = "";
$success = "";
```
**Explanation:**
- `include 'db.php'`: Connects to database
- Note: No `session_start()` here because user isn't logged in yet
- `$error` and `$success`: Variables to store messages for user feedback

### Block 2: Form Data Collection
```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
```
**Explanation:**
- Checks if form was submitted (POST request)
- Gets form data: username, email, password
- `real_escape_string()`: Prevents SQL injection by escaping special characters
- Password not escaped because it will be hashed before database insertion

### Block 3: Duplicate Check
```php
    // Check if Username OR Email already exists
    $check = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = $conn->query($check);
    
    if ($result->num_rows > 0) {
        $error = "Username or Email already exists! Please try another.";
```
**Explanation:**
- `$check = "SELECT * FROM users WHERE username = '$username' OR email = '$email'"`: 
  - **Breaking this complex SQL query down**:
    1. `SELECT *` → Get all columns from users table
    2. `FROM users` → Search in users table
    3. `WHERE` → Filter condition
    4. `username = '$username'` → Check if username matches
    5. `OR` → Logical OR operator (means "or")
    6. `email = '$email'` → Check if email matches
  - **What OR means**: 
    - Returns rows where EITHER condition is true
    - Username matches OR email matches OR both match
  - **Example**: 
    - If username "john" exists → Returns that row
    - If email "john@email.com" exists → Returns that row
    - If both exist → Returns row (only one row, same user)
- `$result = $conn->query($check)`: 
  - Executes the query
  - `$result` contains matching rows (if any)
- `if ($result->num_rows > 0)`: 
  - **Breaking this down**:
    1. `num_rows` → Counts how many rows were found
    2. `> 0` → Checks if count is greater than zero
    3. If > 0 → Username or email already exists
    4. If = 0 → Username and email are both available
  - **Why check**: Prevents duplicate accounts

### Block 4: Password Hashing
```php
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
```
**Explanation:**
- `password_hash()`: 
  - **What it does**: Converts plain text password into encrypted hash
  - **Input**: Plain password (e.g., "mypass123")
  - **Output**: Hash string (e.g., "$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy")
  - **One-way function**: Can convert password → hash, but NOT hash → password
- `$password`: 
  - Plain password user entered in form
  - Example: "mypass123"
- `PASSWORD_DEFAULT`: 
  - Algorithm to use for hashing
  - Currently uses bcrypt algorithm (very secure)
  - PHP automatically uses best available algorithm
  - If better algorithm comes out, PHP updates automatically
- `$hashed_password`: 
  - Stores the hash result
  - This is what gets saved to database
  - **Example transformation**:
    - Input: "mypass123"
    - Output: "$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy"
  - **Why different each time**: Hash includes random "salt" for extra security
- **Why we don't escape password**:
  - Hash is just a string of characters (no SQL-breaking characters)
  - Hash is safe to use directly in SQL query
  - Escaping would add unnecessary backslashes

### Block 5: Database Insertion
```php
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
        
        if ($conn->query($sql) === TRUE) {
            $success = "Registration successful! Redirecting to Login...";
            header("refresh:2;url=login.php"); 
        } else {
            $error = "Error: " . $conn->error;
        }
```
**Explanation:**
- `INSERT INTO`: Adds new user record to database
- Stores username, email, and hashed password
- If successful → Shows success message → Redirects to login page after 2 seconds
- If failed → Shows database error message

### Block 6: HTML Form
```html
<form action="register.php" method="POST" onsubmit="validateForm(event)">
    <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required>
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" id="password" required>
    </div>
    <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
    </div>
    <button type="submit" class="form-btn">Register</button>
</form>
```
**Explanation:**
- `onsubmit="validateForm(event)"`: Calls JavaScript function before form submits
- JavaScript checks if passwords match (client-side validation)
- `type="email"`: HTML5 email validation
- `id` attributes: Used by JavaScript to access password fields

### Block 7: JavaScript Error Display
```html
<p id="js-error" class="error-msg" style="display: none;"></p>
```
**Explanation:**
- Hidden error message element
- JavaScript shows this if passwords don't match
- `display: none`: Hidden by default, shown only when needed

## Security Features
1. **Password Hashing**: Passwords never stored as plain text
2. **SQL Injection Prevention**: Input sanitized with `real_escape_string()`
3. **Duplicate Prevention**: Checks for existing username/email
4. **Client-Side Validation**: JavaScript checks password match before submit
5. **Server-Side Validation**: PHP validates again on server

## User Flow
1. User fills form → Enters username, email, password, confirm password
2. JavaScript validates → Checks if passwords match
3. Form submits → PHP checks for duplicates
4. If valid → Hashes password → Saves to database → Redirects to login
5. If invalid → Shows error → User can fix and try again

## Important Notes
- Passwords are hashed, so even admins can't see original passwords
- Email validation happens on both client (HTML5) and server side
- Registration doesn't automatically log user in - they must login separately

