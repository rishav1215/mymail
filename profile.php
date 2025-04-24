<?php
session_start();
include "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$logged_in_user = $_SESSION['username'];


$query = "SELECT * FROM signup WHERE username = '$logged_in_user'";
$result = mysqli_query($connect, $query);
$user = mysqli_fetch_assoc($result);


$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"])) {
    $file_name = basename($_FILES["profile_pic"]["name"]); 
    $target_file = $upload_dir . $file_name; 
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  
    $allowed_types = ["jpg", "jpeg", "png", "gif"];

    if (!in_array($imageFileType, $allowed_types)) {
        $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
    } else {
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            // Save file path in database
            $update_query = "UPDATE signup SET profile_pic = '$target_file' WHERE username = '$logged_in_user'";
            mysqli_query($connect, $update_query);
            header("Location: profile.php"); // Refresh page
            exit();
        } else {
            $error = "Error uploading image.";
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $new_email = mysqli_real_escape_string($connect, $_POST["email"]);
    $new_bio = mysqli_real_escape_string($connect, $_POST["bio"]);

    $update_query = "UPDATE signup SET email = '$new_email', bio = '$new_bio' WHERE username = '$logged_in_user'";
    mysqli_query($connect, $update_query);
    header("Location: index.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md mt-10">
        <h2 class="text-2xl font-semibold mb-4 text-center">My Profile</h2>

        <!-- Profile Picture -->
        <div class="flex justify-center mb-4">
            <img src="<?= !empty($user['profile_pic']) ? $user['profile_pic'] : 'wall.jpg'; ?>" 
                 alt="Profile Picture"
                 class="w-32 h-32 rounded-full border border-gray-300 object-cover">
        </div>

        <!-- Upload Picture Form -->
        <form action="profile.php" method="post" enctype="multipart/form-data" class="text-center mb-4">
            <input type="file" name="profile_pic" accept="image/*" required class="mb-2">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Upload Picture
            </button>
        </form>

        <!-- Error Message -->
        <?php if (isset($error)): ?>
            <p class="text-red-500 text-center"><?= $error ?></p>
        <?php endif; ?>

        <!-- Profile Details -->
        <form action="profile.php" method="post" class="space-y-4">
            <div>
                <label class="block text-gray-700 font-medium">Username:</label>
                <input type="text" value="<?= $user['username'] ?>" class="w-full p-2 border rounded" disabled>
            </div>

            <div>
                <label class="block text-gray-700 font-medium">Email:</label>
                <input type="email" name="email" value="<?= $user['email'] ?>" class="w-full p-2 border rounded">
            </div>

            <div>
                <label class="block text-gray-700 font-medium">Bio:</label>
                <textarea name="bio" class="w-full p-2 border rounded"><?= $user['bio'] ?></textarea>
            </div>

            <button type="submit" name="update" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Update Profile
            </button>
        </form>
    </div>

</body>
</html>
