# db.php - Database Connection File

## What This File Does

`db.php` is the **foundation** of the entire project. It establishes a connection to the MySQL database that stores all the application's data (users, games, orders, etc.). Every other PHP file in the project includes this file to access the database.

## Role in the Project

- **Centralized Connection**: All PHP files (`Home.php`, `login.php`, `register.php`, `cart.php`, `checkout.php`, `details.php`) include this file using `include 'db.php'` to get database access.
- **Single Point of Configuration**: If you need to change database credentials (host, database name, username, password), you only modify this one file.
- **Error Handling**: If the connection fails, the script stops immediately and shows an error message, preventing other pages from trying to use a broken database connection.

## Code Breakdown

### 1. Database Configuration Variables
```php
$host = 'localhost';
$db   = 'gamestore';
$user = 'root';
$pass = '';
```
- **Purpose**: Stores the database connection details
- **`$host`**: The database server location (localhost = same machine as web server)
- **`$db`**: The name of the database (`gamestore`)
- **`$user`**: MySQL username (`root` is default for XAMPP)
- **`$pass`**: MySQL password (empty string is default for XAMPP)

### 2. Connection Object Creation
```php
$conn = new mysqli($host, $user, $pass, $db);
```
- **Purpose**: Creates a new MySQLi connection object
- **`$conn`**: This variable becomes available to any file that includes `db.php`
- **mysqli**: PHP's MySQL Improved Extension (modern way to connect to MySQL)

### 3. Error Checking
```php
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```
- **Purpose**: Checks if the connection was successful
- **`$conn->connect_error`**: Contains error message if connection failed
- **`die()`**: Stops script execution and displays error message
- **Why Important**: Prevents the rest of the application from running with a broken database connection

## Important Objects/Variables

### `$conn` (MySQLi Connection Object)
- **What it is**: The main database connection object
- **Used for**: 
  - Executing SQL queries: `$conn->query($sql)`
  - Escaping strings: `$conn->real_escape_string($input)`
  - Getting last insert ID: `$conn->insert_id`
  - Accessing errors: `$conn->error`
- **Available in**: Any PHP file that includes `db.php`

## Dependencies

- **PHP MySQLi Extension**: Must be enabled in PHP (usually enabled by default)
- **MySQL/MariaDB Database**: A database server must be running (XAMPP includes MySQL)
- **Database `gamestore`**: Must exist in MySQL with required tables:
  - `users` (id, username, email, password)
  - `games` (id, title, genre, price, description, image_path)
  - `orders` (id, user_id, total_amount, address)
  - `order_items` (id, order_id, game_id, quantity, price)

## Common Issues

1. **"Connection failed" error**: 
   - MySQL service not running (start it in XAMPP Control Panel)
   - Wrong database name (`gamestore` doesn't exist)
   - Wrong username/password

2. **"Access denied" error**:
   - MySQL password is incorrect
   - User doesn't have permission to access the database

3. **Database doesn't exist**:
   - Create the `gamestore` database in phpMyAdmin
   - Import/create the required tables

