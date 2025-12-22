<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

            // --- ADMIN CHECK ---
            if ($row['username'] === 'admin') {
                header("Location: admin.php"); // Go to Dashboard
            } else {
                header("Location: Home.php"); // Go to Normal Home
            }
            exit;
            
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameStore - Login</title>
    <link rel="stylesheet" href="stylesheet.css?v=7">
</head>
<body>

    <header>
        <div class="navbar">
            <div class="logo">GameStore</div>
            <ul class="nav-links">
                <li><a href="Home.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="cart.php">Cart</a></li>
            </ul>
        </div>
    </header>

    <main class="container">
        <div class="form-container">
            <h2 class="form-title">Login</h2>
            
            <?php if($error): ?>
                <p class="error-msg"><?php echo $error; ?></p>
            <?php endif; ?>

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
            
            <p class="register-link-text">
                Don't have an account? <a href="register.php" class="register-link">Register here</a>
            </p>
        </div>
    </main>

    <footer>
        <p>&copy; Games For You</p>
    </footer>

</body>
</html>
