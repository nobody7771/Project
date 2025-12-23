# logout.php - Session Destruction Script

## What This File Does

`logout.php` is a simple script that logs users out by destroying their PHP session. It removes all session data (including `user_id` and cart contents) and redirects the user back to the home page.

## Role in the Project

- **Session Cleanup**: Ends user's login session
- **Security**: Prevents unauthorized access after logout
- **Cart Clearing**: Also clears shopping cart (stored in session)
- **User Flow**: Part of authentication system - completes the login/logout cycle

## Code Breakdown

### 1. Session Start
```php
session_start();
```
- **Purpose**: Must start session before you can destroy it
- **Why**: PHP needs to access the session to clear it

### 2. Clear Session Variables
```php
session_unset();
```
- **What it does**: Removes all variables stored in `$_SESSION` array
- **Clears**: 
  - `$_SESSION['user_id']` (login status)
  - `$_SESSION['username']` (user info)
  - `$_SESSION['cart']` (shopping cart contents)
- **Note**: Session file still exists, but is empty

### 3. Destroy Session
```php
session_destroy();
```
- **What it does**: Completely destroys the session file on server
- **Effect**: Session ID becomes invalid, cannot be reused
- **Security**: Ensures user cannot access session data after logout

### 4. Redirect to Home
```php
header("Location: Home.php");
exit;
```
- **`header("Location: Home.php")`**: Sends HTTP redirect to browser
- **`exit`**: Stops script execution immediately
- **Why**: Prevents any code after redirect from executing

## Important Functions/Features

### `session_start()`
- **What it does**: Initializes/resumes PHP session
- **Required**: Must be called before accessing `$_SESSION` or destroying session
- **Note**: Must be first line (before any HTML output)

### `session_unset()`
- **What it does**: Removes all session variables
- **Effect**: `$_SESSION` array becomes empty
- **Alternative**: Could use `$_SESSION = []` (same effect)

### `session_destroy()`
- **What it does**: Deletes session file from server
- **Effect**: Session ID becomes invalid
- **Security**: Prevents session hijacking after logout

### `header("Location: ...")`
- **What it does**: Sends HTTP redirect header
- **Purpose**: Redirects user to home page
- **Important**: Must be called before any HTML output

## Connections to Other Files

- **Redirects to**: `Home.php` (home page)
- **Related**: `login.php` (creates session that this destroys)
- **Related**: `cart.php` (cart stored in session, cleared here)
- **Related**: `checkout.php` (requires login, logout prevents access)

## Dependencies

- **PHP Sessions**: Must be enabled in PHP configuration
- **No Database**: Doesn't require database connection
- **No HTML**: Pure PHP script (no HTML output)

## Security Considerations

1. **Complete Logout**: Both `session_unset()` and `session_destroy()` ensure complete cleanup
2. **Redirect**: Immediately redirects to prevent access to protected pages
3. **Exit**: Stops script execution to prevent any code after redirect

## Common Issues

1. **"Headers already sent" error**:
   - HTML output (even whitespace) before `header()` call
   - Solution: Ensure `session_start()` and `header()` are first lines

2. **Session not destroyed**:
   - `session_start()` not called
   - Session already destroyed elsewhere
   - Browser caching session

3. **Redirect not working**:
   - HTML output before `header()`
   - Browser blocking redirect
   - Wrong file path in `Location` header

## Why Two Steps?

- **`session_unset()`**: Clears variables (quick, but session file remains)
- **`session_destroy()`**: Deletes session file (complete cleanup)
- **Both Together**: Ensures complete logout and prevents session reuse

## Alternative Approach

Some developers use only `session_destroy()`, but using both is more thorough:
```php
session_start();
session_destroy(); // This also clears variables, but being explicit is better
header("Location: Home.php");
exit;
```

