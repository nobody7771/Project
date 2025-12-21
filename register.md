# register.php - User Registration Page

## What This File Does

`register.php` handles new user registration. It displays a registration form, validates that username/email don't already exist, hashes the password securely, and creates a new user account in the database. After successful registration, users are redirected to the login page.

## Role in the Project

- **User Account Creation**: Creates new user accounts in the database
- **Duplicate Prevention**: Checks if username or email already exists before creating account
- **Password Security**: Hashes passwords before storing (never stores plain text)
- **Form Validation**: Includes both server-side (PHP) and client-side (JavaScript) validation
- **User Flow**: Part of the authentication system - users register here, then login via `login.php`

## Code Breakdown

### 1. Database Connection & Variables
```php
include 'db.php'; 
$error = "";
$success = "";
```
- **`include 'db.php'`**: Loads database connection
- **`$error`**: Stores error messages (duplicate username/email, database errors)
- **`$success`**: Stores success message after registration

### 2. Form Submission Handler
```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
```
- **Purpose**: Processes registration form when submitted
- **`real_escape_string()`**: **Security** - prevents SQL injection on username and email
- **Note**: Password is NOT escaped because it will be hashed, not used directly in SQL

### 3. Duplicate Check
```php
$check = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
$result = $conn->query($check);

if ($result->num_rows > 0) {
    $error = "Username or Email already exists! Please try another.";
```
- **Purpose**: Prevents duplicate accounts
- **SQL Query**: Searches for existing username OR email
- **`num_rows > 0`**: If found, account already exists
- **Error Message**: Tells user to choose different username/email

### 4. Password Hashing & Account Creation
```php
} else {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
    
    if ($conn->query($sql) === TRUE) {
        $success = "Registration successful! Redirecting to Login...";
        header("refresh:2;url=login.php"); 
    } else {
        $error = "Error: " . $conn->error;
    }
}
```
- **`password_hash($password, PASSWORD_DEFAULT)`**: 
  - **Critical Security Function**: Encrypts password using bcrypt algorithm
  - **PASSWORD_DEFAULT**: Uses PHP's recommended hashing algorithm
  - **Why Important**: Passwords are NEVER stored as plain text
- **INSERT Query**: Creates new user record with hashed password
- **`$conn->query($sql) === TRUE`**: Checks if INSERT was successful
- **Success Handling**: 
  - Sets success message
  - `header("refresh:2;url=login.php")`: Redirects to login page after 2 seconds
- **Error Handling**: Shows database error if INSERT fails

### 5. Registration Form HTML
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
- **`onsubmit="validateForm(event)"`**: Calls JavaScript function before form submission
- **Form Fields**:
  - Username (text input)
  - Email (email input - HTML5 validation)
  - Password (password input - hidden)
  - Confirm Password (password input - for verification)
- **`required`**: HTML5 validation prevents empty fields

### 6. JavaScript Error Display Element
```html
<p id="js-error" class="error-msg" style="display: none;"></p>
```
- **Purpose**: Shows JavaScript validation errors (password mismatch)
- **Initially Hidden**: `display: none` - only shown if validation fails

### 7. Success/Error Message Display
```php
<?php if($error): ?>
    <p class="error-msg"><?php echo $error; ?></p>
<?php endif; ?>

<?php if($success): ?>
    <p class="success-msg"><?php echo $success; ?></p>
<?php endif; ?>
```
- **Conditional Display**: Shows error or success message based on registration result

## Important Functions/Features

### `password_hash($password, PASSWORD_DEFAULT)`
- **What it does**: Creates secure hash of password using bcrypt algorithm
- **Input**: Plain text password
- **Output**: Hashed password string (e.g., `$2y$10$...`)
- **Security**: One-way encryption - cannot be reversed to get original password
- **Used with**: `password_verify()` in `login.php` to check passwords

### `$conn->real_escape_string($string)`
- **What it does**: Escapes SQL special characters
- **Purpose**: Prevents SQL injection attacks
- **Applied to**: Username and email (user input)

### `validateForm(event)` (JavaScript)
- **Location**: Defined in `script.js`
- **What it does**: Client-side validation - checks if passwords match
- **Input**: Form submission event
- **Output**: Prevents form submission if passwords don't match
- **Why**: Provides immediate feedback without server round-trip

### Duplicate Check Query
- **SQL**: `SELECT * FROM users WHERE username = '$username' OR email = '$email'`
- **Purpose**: Ensures unique usernames and emails
- **Logic**: If any row found, account already exists

## Connections to Other Files

- **Includes**: `db.php` (database connection)
- **Uses**: `script.js` (password validation function `validateForm()`)
- **Uses**: `stylesheet.css` (form styling)
- **Links to**: 
  - `login.php` (redirects here after registration)
  - `Home.php` (navigation)
  - `cart.php` (navigation)
- **Related**: `login.php` (users created here can login there)

## Dependencies

- **Database Table**: `users` table with columns: id (auto-increment), username, email, password
- **JavaScript**: Requires `script.js` for password confirmation validation
- **CSS**: Requires `stylesheet.css` for form styling
- **PHP Functions**: `password_hash()` requires PHP 5.5+ (usually available)

## Security Features

1. **Password Hashing**: Passwords encrypted before storage
2. **SQL Injection Prevention**: Input sanitization with `real_escape_string()`
3. **Duplicate Prevention**: Checks for existing username/email
4. **Client-Side Validation**: JavaScript checks password match before submission
5. **Email Validation**: HTML5 `type="email"` provides basic email format check

## Common Issues

1. **"Username or Email already exists"**:
   - User trying to register with existing credentials
   - Need to choose different username/email

2. **Passwords don't match error**:
   - JavaScript validation catches mismatch
   - User needs to enter matching passwords

3. **Registration fails silently**:
   - Database error not displayed
   - Check `$conn->error` for details
   - Database table might not exist

4. **Password hash not working**:
   - PHP version too old (< 5.5)
   - `password_hash()` function not available

5. **Redirect not working**:
   - HTML output before `header()` call
   - Browser might block redirect

## Data Flow

1. User fills registration form
2. JavaScript validates password match (client-side)
3. Form submits to server (POST request)
4. PHP checks for duplicate username/email
5. If no duplicate: password is hashed
6. New user inserted into database
7. Success message displayed
8. Redirect to login page after 2 seconds

