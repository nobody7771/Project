# logout.php - User Logout

## Overview
This simple script logs the user out by destroying their session. Clears all session data including cart and login status.

## Code Explanation

### Block 1: Session Cleanup
```php
session_start(); // Find the session
session_unset(); // Remove all variables (Cart, User ID)
session_destroy(); // Destroy the session completely
```
**Explanation:**
- `session_start()`: Must call this first to access session
- `session_unset()`: Removes all session variables
  - Clears `$_SESSION['user_id']`
  - Clears `$_SESSION['username']`
  - Clears `$_SESSION['cart']`
- `session_destroy()`: Completely destroys the session
  - Removes session file from server
  - Session ID becomes invalid

### Block 2: Redirect
```php
// Redirect back to Home
header("Location: Home.php");
exit;
```
**Explanation:**
- Redirects user to home page
- `exit`: Stops PHP execution
- User is now logged out and on home page

## Session Functions Explained

### session_unset()
- Removes all variables from `$_SESSION` array
- Session still exists, just empty
- Equivalent to: `$_SESSION = []`

### session_destroy()
- Completely destroys the session
- Removes session file from server
- Session ID becomes invalid
- User must start new session on next page

## Why Both Functions?
- `session_unset()`: Clears data
- `session_destroy()`: Removes session completely
- Using both ensures complete cleanup
- Some PHP versions need both for full logout

## User Flow
1. User clicks "Logout" → Link goes to logout.php
2. PHP destroys session → All session data cleared
3. Redirect to home → User is logged out
4. Next page visit → New session starts (if needed)

## What Gets Cleared
- Login status (`$_SESSION['user_id']`)
- Username (`$_SESSION['username']`)
- Shopping cart (`$_SESSION['cart']`)
- Any other session variables

## Important Notes
- **No Database Changes**: Logout doesn't modify database
- **Cart Lost**: Shopping cart is cleared on logout
- **Immediate Effect**: Logout happens instantly
- **No Confirmation**: No "Are you sure?" prompt

## Security Benefits
- Prevents session hijacking
- Clears sensitive data
- Invalidates session ID
- Forces new login for next session

## Alternative Approach
Some systems use:
```php
session_start();
$_SESSION = []; // Clear all variables
session_destroy();
```
This is equivalent but less explicit.

