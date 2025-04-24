<?php
include "db.php";
session_start();

if (!isset($_SESSION['username'])) {
    die("User not logged in.");
}

if (!isset($_POST['receiver']) || !isset($_POST['message'])) {
    die("Receiver or message field is missing.");
}

$sender = $_SESSION['username'];
$receiver = mysqli_real_escape_string($connect, $_POST['receiver']);
$message = mysqli_real_escape_string($connect, $_POST['message']);

if (!empty($message) && !empty($receiver)) {
    $query = "INSERT INTO messages (sender, receiver, message, time) 
              VALUES ('$sender', '$receiver', '$message', NOW())";
    
    if (!mysqli_query($connect, $query)) {
        die("Error inserting message: " . mysqli_error($connect)); // ⚠️ Debugging ke liye error print karein
    } else {
        echo "Message sent successfully!";
    }
}


$query = "SELECT sender, message FROM messages 
          WHERE (sender = '$sender' AND receiver = '$receiver') 
          OR (sender = '$receiver' AND receiver = '$sender') 
          ORDER BY time ASC";
$result = mysqli_query($connect, $query);

if (!$result) {
    die("Error fetching massages: " . mysqli_error($connect));
}

while ($row = mysqli_fetch_assoc($result)) {
    $msg_class = ($row['sender'] == $sender) ? 'bg-green-200 text-right' : 'bg-white';
    echo '<div class="p-4 border rounded-lg shadow-sm ' . $msg_class . '">
              <p><strong>' . ($row['sender']) . ':</strong> ' . ($row['message']) . '</p>
          </div>';
}
?>
