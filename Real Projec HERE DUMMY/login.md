# login.php - User Authentication Page

## What This File Does

`login.php` handles user authentication (logging in). It displays a login form, validates user credentials against the database, and creates a session if login is successful. Users must be logged in to place orders (see `checkout.php`).

## Role in the Project

- **User Authentication**: Verifies username and password match database records
- **Session Creation**: Sets `$_SESSION['user_id']` and `$_SESSION['username']` upon successful login
- **Security**: Uses password hashing verification (passwords are stored encrypted in database)
- **Navigation**: Redirects to `Home.php` after successful login
- **Error Handling**: Displays error messages for invalid credentials

## Code Breakdown

### 1. Session & Database Initialization
```php
session_start();
include 'db.php';
$error = "";
```
- **`session_start()`**: Starts PHP session to store login state
- **`include 'db.php'`**: Loads database connection
- **`$error`**: Variable to store error messages for display

### 2. Form Submission Handler (POST Request)
```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
```
- **Purpose**: Processes login form when user submits it
- **`$_SERVER["REQUEST_METHOD"] == "POST"`**: Checks if form was submitted (not just page load)
- **`$conn->real_escape_string()`**: **Security feature** - escapes special characters to prevent SQL injection
- **`$_POST['username']`**: Gets username from form input
- **`$_POST['password']`**: Gets password from form input (not escaped because it's hashed, not used in SQL directly)

### 3. Database Lookup - Find User
```php
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
```
- **Purpose**: Searches database for user with matching username
- **`$result->num_rows > 0`**: Checks if user was found
- **`$row = $result->fetch_assoc()`**: Gets user data as associative array

### 4. Password Verification
```php
if (password_verify($password, $row['password'])) {
    // Success! Log them in
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['username'] = $row['username'];
    header("Location: Home.php");
    exit;
} else {
    $error = "Invalid password!";
}
```
- **`password_verify()`**: PHP function that compares plain password with hashed password from database
- **Why Important**: Passwords are stored encrypted (hashed) - never stored as plain text
- **Session Variables Set**:
  - `$_SESSION['user_id']`: User's ID (used throughout site to identify logged-in user)
  - `$_SESSION['username']`: User's username (for display purposes)
- **`header("Location: Home.php")`**: Redirects to home page after successful login
- **`exit`**: Stops script execution (prevents code below from running)

### 5. Error Handling
```php
} else {
    $error = "User not found!";
}
```
- **Purpose**: Sets error message if username doesn't exist in database

### 6. HTML Form Display
```html
<form action="login.php" method="POST">
    <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required>
    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit" class="form-btn">Login</button>
</form>
```
- **`action="login.php"`**: Form submits to same page (self-processing form)
- **`method="POST"`**: Sends data via POST (not visible in URL)
- **`required`**: HTML5 validation - prevents empty submission
- **`type="password"`**: Hides password input (shows dots/asterisks)

### 7. Error Message Display
```php
<?php if($error): ?>
    <p class="error-msg"><?php echo $error; ?></p>
<?php endif; ?>
```
- **Purpose**: Shows error message if login failed
- **Conditional Display**: Only shows if `$error` variable has content

## Important Functions/Features

### `password_verify($password, $hash)`
- **What it does**: Compares plain text password with hashed password
- **Input**: 
  - `$password`: User's entered password (plain text)
  - `$hash`: Stored password from database (encrypted)
- **Output**: `true` if match, `false` if not
- **Security**: Uses secure hashing algorithm (bcrypt by default)

### `$conn->real_escape_string($string)`
- **What it does**: Escapes special SQL characters to prevent SQL injection
- **Input**: User input string (username)
- **Output**: Escaped string safe for SQL queries
- **Why Important**: Prevents malicious SQL code injection attacks

### Session Variables
- **`$_SESSION['user_id']`**: User's database ID (used in checkout, cart, etc.)
- **`$_SESSION['username']`**: User's username (for display)

### `header("Location: ...")`
- **What it does**: Sends HTTP redirect header to browser
- **Purpose**: Redirects user to different page after login
- **Note**: Must be called before any HTML output

## Connections to Other Files

- **Includes**: `db.php` (database connection)
- **Links to**: 
  - `Home.php` (redirects here after login)
  - `register.php` (link for new users)
  - `cart.php` (navigation)
- **Uses**: `stylesheet.css` (styling)
- **Related**: `register.php` (creates users that can login here)
- **Related**: `logout.php` (destroys session created here)

## Dependencies

- **Database Table**: `users` table with columns: id, username, email, password
- **Password Hashing**: Passwords must be hashed using `password_hash()` (done in `register.php`)
- **PHP Session**: Must be enabled
- **CSS**: Requires `stylesheet.css` for form styling

## Security Features

1. **SQL Injection Prevention**: Uses `real_escape_string()` on username
2. **Password Hashing**: Passwords stored encrypted, verified securely
3. **Session Management**: Uses PHP sessions (more secure than cookies alone)

## Common Issues

1. **"User not found" error**:
   - Username doesn't exist in database
   - Typo in username
   - User needs to register first

2. **"Invalid password" error**:
   - Wrong password entered
   - Password in database not properly hashed (should use `password_hash()`)

3. **Redirect not working**:
   - HTML output before `header()` call (even whitespace)
   - Session already started elsewhere

4. **Session not persisting**:
   - Session not started (`session_start()` missing)
   - Browser cookies disabled
   - Session directory not writable

