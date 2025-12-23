# GameStore - E-Commerce Game Store Website

## Project Overview

GameStore is a **PHP-based e-commerce website** that allows users to browse, search, and purchase video games online. It's a complete web application with user authentication, shopping cart functionality, and order processing.

### What This Project Does

Think of it like a simple version of Steam or Epic Games Store:
- Users can **browse** a catalog of games
- **Search** for games by name
- **View details** of individual games
- **Create an account** and **login**
- **Add games to cart** and **purchase** them
- **View order history** (orders stored in database)

### Technology Stack

- **Backend**: PHP (server-side scripting)
- **Database**: MySQL (stores users, games, orders)
- **Frontend**: HTML, CSS, JavaScript
- **Server**: Apache (via XAMPP)

---

## Project Structure & File Relationships

### How Files Work Together

```
┌─────────────┐
│   db.php    │ ← Database connection (used by ALL PHP files)
└─────────────┘
       │
       ├──→ Home.php (displays games, includes db.php)
       │       │
       │       ├──→ details.php (shows game details, includes db.php)
       │       │       │
       │       │       └──→ cart.php (adds to cart, includes db.php)
       │       │
       │       └──→ script.js (live search functionality)
       │
       ├──→ login.php (user authentication, includes db.php)
       │       │
       │       └──→ register.php (creates account, includes db.php)
       │               │
       │               └──→ script.js (password validation)
       │
       ├──→ cart.php (shopping cart, includes db.php)
       │       │
       │       ├──→ checkout.php (processes orders, includes db.php)
       │       │
       │       └──→ script.js (cart calculations)
       │
       └──→ logout.php (destroys session)

All pages use:
├── stylesheet.css (styling)
└── script.js (client-side functionality)
```

### File Flow Explanation

1. **User Visits Home Page** (`Home.php`)
   - Includes `db.php` to connect to database
   - Queries `games` table to display all games
   - Uses `script.js` for live search
   - Uses `stylesheet.css` for styling

2. **User Clicks "View Details"** (`details.php`)
   - Receives game ID from URL (`?id=1`)
   - Includes `db.php` to fetch game details
   - Displays game information
   - Provides "Add to Cart" form

3. **User Adds to Cart** (`cart.php`)
   - Receives POST data from `details.php`
   - Stores cart in `$_SESSION['cart']` array
   - Uses `script.js` to calculate totals
   - Links to `checkout.php` when ready

4. **User Checks Out** (`checkout.php`)
   - Requires login (checks `$_SESSION['user_id']`)
   - Creates order in `orders` table
   - Creates order items in `order_items` table
   - Clears cart
   - Redirects to home

5. **User Authentication** (`login.php` / `register.php`)
   - `register.php`: Creates new user account (hashes password)
   - `login.php`: Verifies credentials, creates session
   - Both include `db.php` to access `users` table

---

## Database Structure

### Required Tables

#### 1. `users` Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
```
- Stores user accounts
- Passwords are hashed (encrypted) using `password_hash()`

#### 2. `games` Table
```sql
CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    genre VARCHAR(50),
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image_path VARCHAR(255)
);
```
- Stores game catalog
- `image_path` should point to image files (e.g., `"Elden.jpg"`)

#### 3. `orders` Table
```sql
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```
- Stores order records
- Links to user via `user_id`

#### 4. `order_items` Table
```sql
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    game_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (game_id) REFERENCES games(id)
);
```
- Stores individual items in each order
- Allows multiple games per order

---

## Setup Instructions

### Prerequisites

1. **XAMPP** (or similar PHP/MySQL server)
   - Download from: https://www.apachefriends.org/
   - Includes: Apache (web server), MySQL (database), PHP

2. **Web Browser** (Chrome, Firefox, Safari, Edge)

### Step-by-Step Installation

#### Step 1: Install XAMPP
1. Download and install XAMPP
2. Start **Apache** and **MySQL** services from XAMPP Control Panel
3. Verify Apache is running (usually on `http://localhost`)

#### Step 2: Setup Project Files
1. Copy project folder to XAMPP's `htdocs` directory:
   - Windows: `C:\xampp\htdocs\Project-main\`
   - Mac: `/Applications/XAMPP/htdocs/Project-main/`
   - Linux: `/opt/lampp/htdocs/Project-main/`

2. Your project should be accessible at: `http://localhost/Project-main/`

#### Step 3: Create Database
1. Open **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Click "New" to create a database
3. Name it: `gamestore`
4. Click "Create"

#### Step 4: Create Tables
1. Select `gamestore` database in phpMyAdmin
2. Click "SQL" tab
3. Run the following SQL commands:

```sql
-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Create games table
CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    genre VARCHAR(50),
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image_path VARCHAR(255)
);

-- Create orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create order_items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    game_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL
);
```

#### Step 5: Add Sample Data
1. Insert sample games:

```sql
INSERT INTO games (title, genre, price, description, image_path) VALUES
('Elden Ring', 'Action RPG', 59.99, 'An epic action RPG adventure in a vast open world.', 'Elden.jpg'),
('Dispatch', 'Action', 49.99, 'Fast-paced action game with intense combat.', 'Dispatch.jpg'),
('Capsule', 'Puzzle', 29.99, 'Challenging puzzle game with unique mechanics.', 'capsule_616x353.jpg');
```

2. **Note**: Make sure image files (`Elden.jpg`, `Dispatch.jpg`, `capsule_616x353.jpg`) exist in your project folder

#### Step 6: Configure Database Connection
1. Open `db.php`
2. Verify database credentials match your setup:
   ```php
   $host = 'localhost';
   $db   = 'gamestore';
   $user = 'root';
   $pass = '';  // Empty for default XAMPP
   ```

#### Step 7: Test the Application
1. Open browser: `http://localhost/Project-main/Home.php`
2. You should see the game store homepage
3. Try registering a new account
4. Try adding games to cart
5. Try checking out (requires login)

---

## How to Run the Project - Complete Blueprint

### Prerequisites Checklist
- [ ] XAMPP installed and running
- [ ] Project files in `htdocs` folder
- [ ] Database created in phpMyAdmin
- [ ] Tables created (users, games, orders, order_items)
- [ ] Sample data inserted (optional)

---

### Step-by-Step Running Instructions

#### Step 1: Start XAMPP Services
1. **Open XAMPP Control Panel**
   - Windows: Search "XAMPP" in Start menu
   - Mac: Open XAMPP application
   - Linux: Run `sudo /opt/lampp/lampp start`

2. **Start Apache Server**
   - Click "Start" button next to Apache
   - Wait for status to turn green "Running"
   - If port 80 is busy, Apache will show error (change port or close conflicting program)

3. **Start MySQL Database**
   - Click "Start" button next to MySQL
   - Wait for status to turn green "Running"
   - MySQL must be running for database access

4. **Verify Services**
   - Both Apache and MySQL should show green "Running"
   - If red, check error messages in XAMPP Control Panel

#### Step 2: Verify Project Location
1. **Check Project Path**
   - Windows: `C:\xampp\htdocs\Project-main\`
   - Mac: `/Applications/XAMPP/htdocs/Project-main/`
   - Linux: `/opt/lampp/htdocs/Project-main/`

2. **Verify Files Exist**
   - Check that `Home.php`, `db.php`, and other files are present
   - Ensure `images/` folder exists (for game images)

#### Step 3: Access the Website
1. **Open Web Browser**
   - Use Chrome, Firefox, Edge, or Safari
   - Any modern browser works

2. **Navigate to Home Page**
   - Type in address bar: `http://localhost/Project-main/Home.php`
   - Or: `http://localhost/Project-main/`
   - Press Enter

3. **Expected Result**
   - Home page loads with game grid
   - Navigation bar visible at top
   - Search bar functional
   - Games displayed (if database has data)

#### Step 4: Test Basic Functionality
1. **Browse Games**
   - Home page should show all games
   - Click "View Details" on any game

2. **Test Search**
   - Type in search box
   - Games should filter as you type

3. **Test Navigation**
   - Click "Login" or "Register"
   - Navigation should work

#### Step 5: Create Admin Account (Optional)
1. **Register Admin User**
   - Go to Register page
   - Username: `admin`
   - Email: `admin@example.com`
   - Password: (choose any password)
   - Click Register

2. **Login as Admin**
   - Go to Login page
   - Enter admin credentials
   - Should redirect to admin dashboard

---

### Quick Start Commands

**Windows:**
```batch
# No command line needed - use XAMPP Control Panel GUI
```

**Mac/Linux:**
```bash
# Start XAMPP services
sudo /opt/lampp/lampp start

# Stop XAMPP services
sudo /opt/lampp/lampp stop

# Restart services
sudo /opt/lampp/lampp restart
```

---

### Access URLs

| Page | URL |
|------|-----|
| Home | `http://localhost/Project-main/Home.php` |
| Login | `http://localhost/Project-main/login.php` |
| Register | `http://localhost/Project-main/register.php` |
| Cart | `http://localhost/Project-main/cart.php` |
| Admin | `http://localhost/Project-main/admin.php` |

---

### Troubleshooting Running Issues

#### Issue: Apache Won't Start
**Problem**: Port 80 already in use  
**Solution**: 
- Close Skype or other programs using port 80
- Or change Apache port in XAMPP config
- Or use port 8080: `http://localhost:8080/Project-main/Home.php`

#### Issue: MySQL Won't Start
**Problem**: Port 3306 already in use  
**Solution**:
- Close other MySQL instances
- Check if MySQL service is already running
- Restart computer if needed

#### Issue: Page Shows "Connection Failed"
**Problem**: Database not connected  
**Solution**:
- Verify MySQL is running (green in XAMPP)
- Check database name in `db.php` matches your database
- Verify database exists in phpMyAdmin

#### Issue: "404 Not Found"
**Problem**: Wrong file path  
**Solution**:
- Verify project is in `htdocs` folder
- Check URL spelling (case-sensitive on Linux/Mac)
- Ensure file names match exactly

#### Issue: Blank White Page
**Problem**: PHP error  
**Solution**:
- Check XAMPP error logs
- Enable error display in PHP
- Verify all files are present
- Check for syntax errors

---

### Development Workflow

1. **Make Code Changes**
   - Edit PHP files in your editor
   - Save files

2. **Refresh Browser**
   - Press F5 or Ctrl+R to reload page
   - Changes appear immediately (no restart needed)

3. **Check Errors**
   - Browser console (F12) for JavaScript errors
   - XAMPP error logs for PHP errors
   - phpMyAdmin for database issues

4. **Test Functionality**
   - Test each feature after changes
   - Clear browser cache if CSS/JS not updating

---

### Production Deployment Notes

**For Learning/Development**: Current setup is fine  
**For Production**: 
- Change database credentials
- Use prepared statements (security)
- Add error handling
- Enable HTTPS
- Use production web server (not XAMPP)

---

## Common Issues & Troubleshooting

### Issue 1: "Connection failed" Error
**Problem**: Database connection error  
**Solutions**:
- Check MySQL is running in XAMPP Control Panel
- Verify database name is `gamestore` (case-sensitive)
- Check `db.php` credentials match your setup
- Ensure database exists in phpMyAdmin

### Issue 2: "Headers already sent" Error
**Problem**: HTML output before `header()` or `session_start()`  
**Solutions**:
- Remove any whitespace before `<?php` tags
- Ensure `session_start()` is first line
- Check for BOM (Byte Order Mark) in files
- Don't echo anything before redirects

### Issue 3: Images Not Showing
**Problem**: Game images don't display  
**Solutions**:
- Check `image_path` in database matches actual file names
- Verify image files exist in project folder
- Check file paths are correct (case-sensitive on Linux/Mac)
- Ensure images are in same directory as PHP files

### Issue 4: Cart Empty After Refresh
**Problem**: Shopping cart clears on page refresh  
**Solutions**:
- Ensure `session_start()` is called on all pages
- Check browser allows cookies
- Verify session directory is writable
- Don't clear session accidentally

### Issue 5: Login Not Working
**Problem**: Can't login after registration  
**Solutions**:
- Verify password was hashed during registration (`password_hash()`)
- Check username/password match database
- Ensure `password_verify()` is used (not comparing plain text)
- Check for typos in username/password

### Issue 6: Search Not Filtering
**Problem**: Live search doesn't work  
**Solutions**:
- Check `script.js` is loaded (`<script src="script.js"></script>`)
- Verify `onkeyup="liveSearch()"` attribute on search input
- Check browser console for JavaScript errors
- Ensure game cards have class `game-card`

### Issue 7: CSS Not Loading
**Problem**: Page looks unstyled  
**Solutions**:
- Check `<link rel="stylesheet" href="stylesheet.css">` in HTML
- Verify file path is correct
- Try adding version parameter: `stylesheet.css?v=6`
- Clear browser cache (Ctrl+F5)

### Issue 8: Can't Access Checkout
**Problem**: Redirected to login when trying to checkout  
**Solutions**:
- This is **expected behavior** - checkout requires login
- Login first, then add items to cart
- Ensure `$_SESSION['user_id']` is set after login

---

## File-by-File Documentation

Each source file has its own detailed documentation:

- **[db.md](db.md)** - Database connection file
- **[Home.md](Home.md)** - Main landing page
- **[login.md](login.md)** - User authentication
- **[register.md](register.md)** - User registration
- **[logout.md](logout.md)** - Session cleanup
- **[cart.md](cart.md)** - Shopping cart management
- **[checkout.md](checkout.md)** - Order processing
- **[details.md](details.md)** - Game details page
- **[script.md](script.md)** - JavaScript functionality
- **[stylesheet.md](stylesheet.md)** - CSS styling

---

## Key Features Explained

### 1. User Authentication System
- **Registration**: Users create accounts with username, email, password
- **Login**: Secure password verification using `password_verify()`
- **Session Management**: PHP sessions track logged-in users
- **Security**: Passwords are hashed (never stored as plain text)

### 2. Shopping Cart
- **Session-Based**: Cart stored in `$_SESSION['cart']` array
- **Add Items**: From game details page
- **Update Quantities**: Real-time JavaScript calculations
- **Remove Items**: One-click removal
- **Persists**: Cart survives page refreshes (until logout)

### 3. Order Processing
- **Requires Login**: Only logged-in users can checkout
- **Order Storage**: Saves to `orders` and `order_items` tables
- **Price Snapshot**: Stores price at purchase time
- **Cart Clearing**: Automatically clears after successful order

### 4. Search Functionality
- **Live Search**: Filters games as you type (no page reload)
- **Client-Side**: JavaScript filters pre-loaded games
- **Case-Insensitive**: Works regardless of capitalization

### 5. Responsive Design
- **CSS Grid**: Auto-adjusting game grid
- **Flexbox**: Flexible layouts for forms and details
- **Mobile-Friendly**: Adapts to different screen sizes

---

## Security Considerations

### Current Security Features
✅ Password hashing (`password_hash()` / `password_verify()`)  
✅ SQL injection prevention (`real_escape_string()`)  
✅ Session-based authentication  
✅ Login required for checkout  

### Potential Improvements
⚠️ **SQL Injection**: Should use prepared statements instead of string interpolation  
⚠️ **XSS Protection**: Should escape HTML output (`htmlspecialchars()`)  
⚠️ **CSRF Protection**: Should add CSRF tokens to forms  
⚠️ **Input Validation**: Should validate all user inputs more thoroughly  

**Note**: This is a learning project. For production use, implement additional security measures.

---

## Learning Points

### What Students Should Understand

1. **PHP Sessions**: How `$_SESSION` works for user state
2. **Database Queries**: SQL SELECT, INSERT operations
3. **Form Handling**: POST vs GET requests
4. **Password Security**: Hashing vs plain text
5. **Client-Server Interaction**: PHP (server) + JavaScript (client)
6. **CSS Layout**: Grid and Flexbox for responsive design
7. **File Structure**: How files connect and depend on each other

### Key Concepts Demonstrated

- **MVC-like Structure**: Separation of concerns (database, logic, presentation)
- **State Management**: Sessions for maintaining user state
- **Database Relationships**: Foreign keys linking tables
- **Responsive Design**: CSS Grid for automatic layout adaptation
- **Form Validation**: Both client-side (JS) and server-side (PHP)

---

## Project Requirements Checklist

If this is for a course project, here's what's typically required:

- ✅ User registration and login
- ✅ Product catalog display
- ✅ Product search functionality
- ✅ Shopping cart (add, remove, update)
- ✅ Checkout process
- ✅ Order storage in database
- ✅ Responsive design
- ✅ Session management
- ✅ Database integration

---

## Next Steps / Future Enhancements

If you want to extend this project:

1. **Admin Panel**: Add admin login to manage games
2. **Order History**: Show user's past orders
3. **Payment Integration**: Real payment processing (Stripe, PayPal)
4. **Email Notifications**: Send order confirmation emails
5. **Product Reviews**: Allow users to review games
6. **Wishlist**: Save games for later
7. **Image Upload**: Admin can upload game images
8. **Advanced Search**: Filter by genre, price range
9. **Pagination**: Limit games per page
10. **Better Security**: Prepared statements, CSRF protection

---

## Support & Resources

### If You Get Stuck

1. **Check Error Messages**: PHP and JavaScript errors tell you what's wrong
2. **Browser Console**: Press F12 to see JavaScript errors
3. **PHP Error Log**: Check XAMPP logs for PHP errors
4. **Database**: Use phpMyAdmin to verify data exists
5. **File Paths**: Ensure all file paths are correct

### Useful Resources

- **PHP Documentation**: https://www.php.net/docs.php
- **MySQL Documentation**: https://dev.mysql.com/doc/
- **CSS Grid Guide**: https://css-tricks.com/snippets/css/complete-guide-grid/
- **JavaScript MDN**: https://developer.mozilla.org/en-US/docs/Web/JavaScript

---

## License & Credits

This is an educational project. Feel free to use and modify for learning purposes.

**Remember**: Always test thoroughly and implement proper security measures before deploying to production!

