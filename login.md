# login.php - User Login Page

## Overview
This page handles user authentication. Users enter their username and password to log into their account.

## Code Explanation

### Block 1: PHP Initialization
```php
session_start();
include 'db.php';

$error = "";
```
**Explanation:**
- `session_start()`: Starts session to store login status
- `include 'db.php'`: Connects to database
- `$error`: Variable to store error messages for display

### Block 2: Form Processing
```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
```
**Explanation:**
- `$_SERVER["REQUEST_METHOD"]`: 
  - `$_SERVER` is a PHP superglobal array (available everywhere)
  - Contains information about the server and request
  - `["REQUEST_METHOD"]` gets how the page was accessed
  - Can be "GET" (normal page load) or "POST" (form submitted)
- `== "POST"`: 
  - Checks if request method equals "POST"
  - `==` is comparison operator (checks if equal)
  - Only true when form was submitted
- `$_POST['username']`: 
  - `$_POST` is superglobal array containing form data
  - `['username']` gets the value from form field named "username"
  - Example: If user typed "john", `$_POST['username']` = "john"
- `$conn->real_escape_string($_POST['username'])`: 
  - **Breaking this down step by step**:
    1. `$_POST['username']` → Gets raw input (e.g., "john' OR '1'='1")
    2. `real_escape_string()` → Escapes special characters
    3. Result: "john\' OR \'1\'=\'1" (safe for SQL)
  - **Why needed**: Prevents SQL injection attacks
  - **What it does**: Adds backslashes before quotes and special characters
  - Example: `'` becomes `\'`, `"` becomes `\"`
- `$password = $_POST['password']`: 
  - Gets password from form
  - **Not escaped** because:
    - Password will be hashed (converted to hash string)
    - Hash is safe - no special characters that break SQL
    - We use `password_verify()` which handles it securely

### Block 3: Database Query
```php
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
```
**Explanation:**
- SQL query searches for user with matching username
- `$result`: Stores the query result

### Block 4: User Verification
```php
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if (password_verify($password, $row['password'])) {
```
**Explanation:**
- `$result->num_rows`: 
  - `$result` is the query result object
  - `num_rows` is a property that counts how many rows were found
  - Example: If user exists, `num_rows` = 1. If not, `num_rows` = 0
- `> 0`: 
  - Checks if number is greater than zero
  - If true → User found in database
  - If false → User doesn't exist
- `$row = $result->fetch_assoc()`: 
  - **Breaking this down**:
    1. `fetch_assoc()` → Gets one row from result as associative array
    2. Associative array means: `$row['id']`, `$row['username']`, etc.
    3. `$row` now contains: `['id' => 1, 'username' => 'john', 'password' => '$2y$10...']`
  - **Why "assoc"**: Keys are column names (associative), not numbers
  - Example: `$row['username']` gets username, `$row['id']` gets ID
- `password_verify($password, $row['password'])`: 
  - **Breaking this down**:
    1. `$password` → Plain password user entered (e.g., "mypass123")
    2. `$row['password']` → Hashed password from database (e.g., "$2y$10$abc123...")
    3. `password_verify()` → Compares them securely
    4. Returns `true` if match, `false` if don't match
  - **How it works**: 
    - Takes plain password and hash
    - Uses same algorithm that created hash
    - Checks if plain password creates same hash
    - **Cannot reverse**: Hash cannot be converted back to password
  - **Why secure**: Even if database is hacked, attacker sees hash, not password

### Block 5: Session Creation
```php
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            // --- ADMIN CHECK ---
            if ($row['username'] === 'admin') {
                header("Location: admin.php"); // Go to Dashboard
            } else {
                header("Location: Home.php"); // Go to Normal Home
            }
            exit;
```
**Explanation:**
- `$_SESSION['user_id']`: Stores user ID in session (used to identify logged-in user)
- `$_SESSION['username']`: Stores username in session
- Checks if user is admin → redirects to admin dashboard
- Regular users → redirects to home page
- `exit`: Stops PHP execution after redirect

### Block 6: Error Handling
```php
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
```
**Explanation:**
- If password doesn't match → sets error message
- If username not found → sets error message
- Error messages displayed to user in HTML form

### Block 7: HTML Form
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
**Explanation:**
- `action="login.php"`: Form submits to same page
- `method="POST"`: Uses POST method (secure, data not visible in URL)
- `required`: HTML5 validation - fields must be filled
- `type="password"`: Hides password as user types

### Block 8: Error Display
```php
<?php if($error): ?>
    <p class="error-msg"><?php echo $error; ?></p>
<?php endif; ?>
```
**Explanation:**
- Conditionally displays error message if `$error` variable has value
- Only shows when login fails

## Security Features
1. **Password Hashing**: Passwords stored as hash, never plain text
2. **SQL Injection Prevention**: `real_escape_string()` sanitizes input
3. **Session Management**: Login status stored in server-side session
4. **Password Verification**: Uses `password_verify()` to compare hashes

## User Flow
1. User enters username/password → Clicks "Login"
2. PHP checks database → Verifies credentials
3. If valid → Creates session → Redirects to appropriate page
4. If invalid → Shows error message → User can try again

