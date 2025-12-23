# delete_game.php - Delete Game (Admin Only)

## Overview
This is a simple script that deletes a game from the database. It's called when admin clicks "Delete" button on admin dashboard.

## Code Explanation

### Block 1: Security Check
```php
session_start();
include 'db.php';

if (isset($_SESSION['user_id']) && $_SESSION['username'] === 'admin' && isset($_GET['id'])) {
```
**Explanation:**
- `session_start()`: Starts session to check login
- Checks THREE conditions:
  1. User is logged in (`$_SESSION['user_id']`)
  2. User is admin (`$_SESSION['username'] === 'admin'`)
  3. Game ID provided (`$_GET['id']`)
- All three must be true to proceed

### Block 2: Delete Query
```php
    $id = $_GET['id'];
    $conn->query("DELETE FROM games WHERE id = $id");
}
```
**Explanation:**
- Gets game ID from URL parameter
- `DELETE FROM games`: Removes record from games table
- `WHERE id = $id`: Deletes only the game with matching ID
- Query executes immediately

### Block 3: Redirect
```php
header("Location: admin.php");
exit;
```
**Explanation:**
- Redirects back to admin dashboard
- `exit`: Stops PHP execution
- Admin sees updated games list (without deleted game)

## How It's Called
From admin.php, delete link:
```html
<a href="delete_game.php?id=<?php echo $game['id']; ?>" 
   class="delete-btn"
   onclick="return confirm('Are you sure?');">Delete</a>
```
- Link includes game ID in URL
- `onclick="return confirm(...)"`: JavaScript confirmation dialog
- User must confirm before deletion

## DELETE Query Structure
```sql
DELETE FROM games WHERE id = $id
```
- `DELETE FROM games`: Removes records from games table
- `WHERE id = $id`: Only deletes record with matching ID
- Without WHERE clause, would delete ALL games!

## Security Features
1. **Admin Check**: Only admin can delete
2. **Login Check**: Must be logged in
3. **ID Check**: Must provide game ID
4. **Confirmation**: JavaScript confirmation before delete

## Important Notes
- **No Undo**: Deletion is permanent (no confirmation in database)
- **No Image Cleanup**: Deleted game's image file remains on server
- **No Cascade**: If orders reference this game, they might break
- **Simple Script**: Very minimal - just deletes and redirects

## User Flow
1. Admin clicks "Delete" → JavaScript confirmation appears
2. Admin confirms → Link goes to delete_game.php?id=X
3. PHP checks security → Verifies admin access
4. DELETE query executes → Game removed from database
5. Redirect to admin.php → Admin sees updated list

## Potential Issues
- **No Error Handling**: Doesn't check if deletion succeeded
- **No Image Cleanup**: Image files not deleted from server
- **No Order Check**: Doesn't verify if game has orders
- **SQL Injection Risk**: ID not sanitized (should use prepared statements)

## Best Practices (Not Implemented)
- Use prepared statements for SQL
- Delete associated image file
- Check for existing orders before deletion
- Show success/error messages
- Log deletion activity

