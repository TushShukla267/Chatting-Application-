<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Php</title>
</head>
<body>
    <h2>Hello there, you are seeing this page because you have entered the wrong username or password</h2>

    <?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            require 'ConnectChatbot.php';

            // Create a table if it doesn't exist
            $createTableSql = "CREATE TABLE IF NOT EXISTS USERDETAILS(
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL
            )";

            if (!mysqli_query($conn, $createTableSql)) {
                echo "Error creating table: " . mysqli_error($conn);
            }

            echo "Entered username is: " . htmlspecialchars($username) . "<br><br>" . "Entered password is: " . htmlspecialchars($password) . "<br><br>";

            // Query to get all rows from the Users table
            $displayRows = "SELECT * FROM USERDETAILS";
            $result = mysqli_query($conn, $displayRows);
            $num = mysqli_num_rows($result);

            if ($num > 0) {
                $usernameExists = false;
                $passwordVerified = false;
                while ($row = mysqli_fetch_assoc($result)) {
                    if ($username == $row['username']) {
                        $usernameExists = true;
                        // Verifying the hashed password
                        if (password_verify($password, $row['password'])) {
                            $passwordVerified = true;
                            break;
                        }
                    }
                }
                if ($usernameExists && $passwordVerified) {
                    session_start();
                    $_SESSION['username'] = $username;
                    echo "We have saved your session";
                    header("Location: http://localhost/ChatBotProject/index.php");
                    // header("Location: http://localhost/ChatBotProject/ChatBot.html");
                    exit;
                } else {
                    if (!$usernameExists) {
                        echo '<h1>Invalid username</h1>';
                    } else {
                        echo '<h1>Invalid password</h1>';
                    }
                }
            } else {
                echo "No rows found<br>";
            }

        } else {
            die("Something went wrong");
        }
    }

    mysqli_close($conn);
    ?>

</body>
</html>
