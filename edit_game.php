<?php
session_start();
include 'db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
$error = "";
$success = "";

// 1. Fetch current game data
$sql = "SELECT * FROM games WHERE id = $id";
$result = $conn->query($sql);
$game = $result->fetch_assoc();

// 2. Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $genre = $conn->real_escape_string($_POST['genre']);
    $price = $_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);
    
    // Check if new image was uploaded
    if (!empty($_FILES['image']['name'])) {
        $image_name = $_FILES['image']['name'];
        $target_dir = "images/";
        $target_file = $target_dir . basename($image_name);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        
        // Update query WITH image
        $update_sql = "UPDATE games SET title='$title', genre='$genre', price='$price', description='$description', image_path='$target_file' WHERE id=$id";
    } else {
        // Update query WITHOUT image (keep old one)
        $update_sql = "UPDATE games SET title='$title', genre='$genre', price='$price', description='$description' WHERE id=$id";
    }

    if ($conn->query($update_sql) === TRUE) {
        $success = "Game updated! Redirecting...";
        header("refresh:1;url=admin.php");
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Game</title>
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
            <h2 class="form-title">Edit Game</h2>
            
            <?php if($success): ?> <p class="success-msg"><?php echo $success; ?></p> <?php endif; ?>

            <form action="edit_game.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?php echo $game['title']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Genre</label>
                    <input type="text" name="genre" value="<?php echo $game['genre']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" value="<?php echo $game['price']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" style="width: 100%; padding: 10px;"><?php echo $game['description']; ?></textarea>
                </div>
                <div class="form-group">
                    <label>New Image (Optional)</label>
                    <input type="file" name="image">
                    <p>Current: <img src="<?php echo $game['image_path']; ?>" style="height: 50px; vertical-align: middle;"></p>
                </div>
                <button type="submit" class="form-btn">Update Game</button>
            </form>
        </div>
    </main>
</body>
</html>