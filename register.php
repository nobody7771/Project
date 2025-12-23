<?php
include 'db.php'; 

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    // Check if Username OR Email already exists
    $check = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = $conn->query($check);
    
    if ($result->num_rows > 0) {
        $error = "Username or Email already exists! Please try another.";
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameStore - Register</title>
    <link rel="stylesheet" href="stylesheet.css?v=3">
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
            <h2 class="form-title">Create Account</h2>
            
            <?php if($error): ?>
                <p class="error-msg"><?php echo $error; ?></p>
            <?php endif; ?>

            <p id="js-error" class="error-msg" style="display: none;"></p>

            <?php if($success): ?>
                <p class="success-msg"><?php echo $success; ?></p>
            <?php endif; ?>

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
            
            <p class="register-link-text">
                Already have an account? <a href="login.php" class="register-link">Login here</a>
            </p>
        </div>
    </main>

    <footer>
        <p>&copy; Games For You</p>
    </footer>

    <script src="script.js"></script>

</body>
</html>