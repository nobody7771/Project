# db.php - Database Connection File

## Overview
This file establishes the connection to the MySQL database. It's included in all PHP files that need database access.

## Code Explanation

### Block 1: Database Configuration
```php
$host = 'localhost';
$db   = 'gamestore';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
```
**Explanation:**
- `$host`: The database server location (localhost means the database is on the same computer)
- `$db`: The name of the database we want to connect to
- `$user`: Database username (root is the default admin user in XAMPP)
- `$pass`: Database password (empty string is default for XAMPP)

### Block 2: Create Connection
```php
$conn = new mysqli($host, $user, $pass, $db);
```
**Explanation:**
- `new mysqli()`: Creates a new MySQL connection object
  - This is object-oriented programming (OOP) syntax
  - `mysqli` is a PHP class for MySQL database connections
- Parameters passed in order:
  1. `$host` - Where the database server is located
  2. `$user` - Username to connect with
  3. `$pass` - Password for that username
  4. `$db` - Which database to use
- `$conn`: Stores the connection object
  - This variable holds all database connection information
  - Other files use `$conn` to run queries
- **Think of it like**: Opening a door to the database room

### Block 3: Error Checking
```php
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```
**Explanation:**
- `$conn->connect_error`: Checks if connection has an error
  - `->` is the object operator (accesses object properties/methods)
  - `connect_error` is a property that contains error message if connection failed
  - If connection succeeded, `connect_error` is empty/false
- `if ($conn->connect_error)`: 
  - If there IS an error (value is not empty) → Execute code inside
  - If NO error (value is empty/false) → Skip this block
- `die("Connection failed: " . $conn->connect_error)`:
  - `die()`: Stops PHP script immediately (like emergency stop)
  - `"Connection failed: "`: Text message to show user
  - `.`: String concatenation operator (joins strings together)
  - `$conn->connect_error`: Adds the actual error message
  - Example output: "Connection failed: Access denied for user 'root'"
- **Why this is important**: Without this check, if database is down, your page would show confusing errors. This gives a clear message.

## Usage
Every PHP file that needs database access includes this file at the top:
```php
include 'db.php';
```

## Important Notes
- Make sure MySQL is running in XAMPP before accessing any page
- Database name must match exactly: `gamestore`
- If you change database credentials, update this file

