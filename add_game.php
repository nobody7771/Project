<?php
session_start();
include 'db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $genre = $conn->real_escape_string($_POST['genre']);
    $price = $_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);

    // IMAGE UPLOAD LOGIC
    // 1. Get the file info
    $image_name = $_FILES['image']['name'];
    $target_dir = "images/"; // Folder where we save images
    $target_file = $target_dir . basename($image_name);

    // 2. Move file to folder
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        
        // 3. Save info to Database
        $sql = "INSERT INTO games (title, genre, price, description, image_path) 
                VALUES ('$title', '$genre', '$price', '$description', '$target_file')";

        if ($conn->query($sql) === TRUE) {
            $success = "Game added successfully!";
        } else {
            $error = "Database Error: " . $conn->error;
        }
    } else {
        $error = "Failed to upload image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Game</title>
    <link rel="stylesheet" href="stylesheet.css?v=7">
</head>
<body>

    <header>
        <div class="navbar">
            <div class="logo">Admin Panel</div>
            <ul class="nav-links">
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </header>

    <main class="container">
        <div class="form-container">
            <h2 class="form-title">Add New Game</h2>

            <?php if($error): ?> <p class="error-msg"><?php echo $error; ?></p> <?php endif; ?>
            <?php if($success): ?> <p class="success-msg"><?php echo $success; ?></p> <?php endif; ?>

            <form action="add_game.php" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Game Title</label>
                    <input type="text" name="title" required>
                </div>

                <div class="form-group">
                    <label>Genre</label>
                    <input type="text" name="genre" required>
                </div>

                <div class="form-group">
                    <label>Price ($)</label>
                    <input type="number" step="0.01" name="price" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" style="width: 100%; padding: 10px;" required></textarea>
                </div>

                <div class="form-group">
                    <label>Game Cover Image</label>
                    <input type="file" name="image" required>
                </div>

                <button type="submit" class="form-btn">Add Game</button>
            </form>
        </div>
    </main>
</body>
</html>