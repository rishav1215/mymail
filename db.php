<?php
$connect = mysqli_connect("localhost", "root", "", "mymail");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

?>

