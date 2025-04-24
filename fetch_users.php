<?php
include "db.php";


$logged_in_user = $_SESSION['username'];
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($connect, $_GET['search']) : '';

$query = "SELECT username FROM signup WHERE username != '$logged_in_user'";

if (!empty($search_query)) {
    $query .= " AND username LIKE '%$search_query%'";
}

$query .= " ORDER BY username ASC";
$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $username = $row['username'];
        echo '<a href="index.php?user=' . $username . '" 
              class="block p-2 rounded-lg mb-2 cursor-pointer text-center bg-blue-100">' . $username . '</a>';
    }
} else {
    echo '<p class="text-gray-500 text-center">No users found</p>';
}
?>
