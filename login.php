<?php
include "db.php";
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);

    echo "Email: $email <br>";

    $query = "SELECT * FROM signup WHERE email='$email'";
    $result = mysqli_query($connect, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($connect));
    }

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        echo "User found: " . print_r($user, true);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo "<script>alert('Login successful! Redirecting to dashboard...'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Invalid password!');</script>";
        }
    } else {
        echo "<script>alert('Email not found!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animated Login Form</title>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #e0e5ec;
            font-family: Arial, sans-serif;
        }

        .log-card {
            background: #e0e5ec;
            border-radius: 20px;
            box-shadow: 10px 10px 20px #a3b1c6, -10px -10px 20px #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px;
            width: 320px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        .log-input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 20px;
            background: #e0e5ec;
            box-shadow: inset 5px 5px 10px #a3b1c6, inset -5px -5px 10px #ffffff;
            font-size: 16px;
            outline: none;
            transition: 0.3s ease-in-out;
        }

        .log-input:focus {
            box-shadow: 0px 0px 10px rgba(52, 152, 219, 0.6);
            transform: scale(1.05);
        }

        .log-button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 20px;
            background: grey;
            color: white;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 5px 5px 10px #a3b1c6, -5px -5px 10px #ffffff;
            margin-top: 15px;
            transition: 0.3s ease-in-out;
        }

        .log-button:hover {
            background: #2980b9;
            transform: scale(1.1);
            box-shadow: 0px 10px 20px rgba(52, 152, 219, 0.5);
        }
    </style>
</head>
<body>
    <div class="log-card">
        <h1>Login</h1>
        <form method="POST">
            <input type="email" class="log-input" name="email" placeholder="Email" required>
            <input type="password" class="log-input" name="password" placeholder="Password" required>
            <button type="submit" class="log-button">Login</button>
        </form>
        <p>
            <a href="signin.php">Signup</a>
        </p>
    </div>
</body>
</html>
