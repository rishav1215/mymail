<?php
session_start();
include "db.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$logged_in_user = $_SESSION['username'];
$current_user = isset($_GET['user']) ? $_GET['user'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyMail Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg {
            background-image: url('wall.jpg');
            background-size: cover;
            background-position: center;
        }

        .message-bubble {
            max-width: 60%;
            display: inline-block;
            word-wrap: break-word;
            padding: 8px 12px;
            border-radius: 10px;
        }

        .sent {
            background-color: #4CAF50;
            color: white;
            align-self: flex-end;
            text-align: end;

        }

        .received {
            background-color: #E0E0E0;
            color: black;
            align-self: flex-start;
            text-align: start;
        }
    </style>
</head>

<body class="h-screen flex flex-col pt-16">

    <!-- Navbar -->
    <nav class="bg-gray-600 p-2 fixed w-full top-0 z-10">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-white text-xl font-bold">Mymail</a>
            <div class="relative w-full max-w-xs mx-auto">
                <input type="search" id="search-user" placeholder="Search people..."
                    class="w-full p-2 pl-10 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-400 focus:outline-none shadow-sm transition duration-200" />

            </div>
            <ul id="menu" class="hidden md:flex space-x-4 text-white">

                <li><a href="#" class="hover:text-gray-300">Home</a></li>
                <li>
                    <a href="profile.php?user=username"
                        class="w-40 h-9 flex items-center justify-center bg-white text-blue-600 rounded-full hover:bg-gray-300">
                        <?= isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?>
                    </a>
                </li>
                <li><a href="login.php" class="hover:text-gray-300">Logout</a></li>
                

            </ul>
        </div>
    </nav>

    <div class="flex flex-grow">
        <!-- Sidebar -->
        <aside class="w-1/4 bg-gray-100 p-4 border-r flex flex-col">
            <h2 class="text-lg font-semibold mb-5 text-center">My Connections</h2>
            <ul class="flex-grow overflow-y-auto">
                <?php
                $query = "SELECT username FROM signup WHERE username != '$logged_in_user' ORDER BY username ASC";
                $result = mysqli_query($connect, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    $username = $row['username'];
                    $active_class = ($username == $current_user) ? 'bg-blue-300' : 'bg-blue-100';
                    echo '<a href="index.php?user=' . $username . '" 
                      class="block p-2 rounded-lg mb-2 cursor-pointer text-center ' . $active_class . '">' . $username . '</a>';
                }
                ?>
            </ul>
        </aside>

        <!-- Chat Section -->
        <main class="w-3/4 p-4 flex flex-col bg">
            <div class="mb-4 text-center py-2 border-b flex items-center justify-center space-x-4">
                <?php
                if ($current_user) {
                    $profile_query = "SELECT profile_pic FROM signup WHERE username = '$current_user'";
                    $profile_result = mysqli_query($connect, $profile_query);
                    $profile_data = mysqli_fetch_assoc($profile_result);
                    $profile_pic = !empty($profile_data['profile_pic']) ? $profile_data['profile_pic'] : 'default_profile.jpg';
                    ?>

                    <img src="<?= $profile_pic ?>" alt="Profile Picture"
                        class="w-10 h-10 rounded-full border border-gray-300 object-cover">
                    <h2 class="text-lg font-semibold text-white">
                        Chat with <?= $current_user ?>
                    </h2>

                <?php } else { ?>
                    <h2 class="text-lg font-semibold text-white">Select a user to chat</h2>
                <?php } ?>
            </div>


            <div class="flex-grow max-h-[400px] overflow-y-auto space-y-4 p-2 border rounded-lg bg-gray-50 bg"
                id="message-list">
                <?php
                if ($current_user) {
                    $query = "SELECT sender, message FROM messages 
                    WHERE (sender = '$current_user' AND receiver = '$logged_in_user') 
                    OR (sender = '$logged_in_user' AND receiver = '$current_user') 
                    ORDER BY time ASC";

                    $result = mysqli_query($connect, $query);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $msg_class = ($row['sender'] == $logged_in_user) ? 'sent' : 'received';
                            $alignment_class = ($row['sender'] == $logged_in_user) ? 'justify-end' : 'justify-start';

                            echo '<div class="flex w-full ' . $alignment_class . '">
                                <div class="message-bubble ' . $msg_class . '">
                                    <p>' . $row['message'] . '</p>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<p class="text-gray-500 text-center">No messages yet</p>';
                    }
                }

                ?>
            </div>

            <?php if ($current_user): ?>
                <div class="mt-4 flex items-center p-2 border rounded-lg bg-grey shadow-md">
                    <input type="text" id="message-input" placeholder="Type a message..."
                        class="flex-grow p-2 border rounded-lg outline-none">
                    <button onclick="sendMessage()"
                        class="ml-2 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Send
                    </button>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function sendMessage() {
            let messageInput = document.getElementById("message-input");
            let message = messageInput.value.trim();
            let receiver = "<?= $current_user ?>";

            if (message === "") {
                alert("Message cannot be empty!");
                return;
            }

            let formData = new FormData();
            formData.append("receiver", receiver);
            formData.append("message", message);

            fetch("send_message.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    if (data.includes("Message sent successfully")) {
                        messageInput.value = "";
                        location.reload();
                    } else {
                        alert("Message send failed: " + data);
                    }
                })
                .catch(error => console.error("Error:", error));
        }
    </script>

</body>

</html>