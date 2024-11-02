<?php

// Include PHPMailer classes and autoload
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Function to send email
function sendMail($to, $subject, $message, $cc = null, $additionalHeaders = null) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tushar.shukla@somaiya.edu'; // Your SMTP username
        $mail->Password = 'jgyt lhlc yivq idkc';            // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('tushar.shukla@somaiya.edu', 'Tushar J Shukla');
        $mail->addAddress($to); // Add recipient email

        if ($cc) {
            $mail->addCC($cc); // Add CC if provided
        }

        if ($additionalHeaders) {
            foreach ($additionalHeaders as $key => $value) {
                $mail->addCustomHeader($key, $value);
            }
        }

        $mail->isHTML(true); // Email format
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        echo 'Signup email has been sent<br>';
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['Name'], $_POST['username'], $_POST['Email'], $_POST['PhoneNumber'], $_POST['password'])) {
        $name = $_POST['Name'];
        $username = $_POST['username'];
        $email = $_POST['Email'];
        $PhoneNumber = $_POST['PhoneNumber'];
        $password = $_POST['password'];

        require 'ConnectChatbot.php'; // Your database connection file

        // Check if table exists, else create it
        $createTableSql = "CREATE TABLE IF NOT EXISTS USERDETAILS(
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            PhoneNumber VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL
        )";

        mysqli_query($conn, $createTableSql);

        // Check if username already exists
        $displayRows = "SELECT * FROM USERDETAILS WHERE username='$username'";
        $result = mysqli_query($conn, $displayRows);

        if (mysqli_num_rows($result) > 0) {
            echo '<h1>User already exists. Please try logging in.</h1>';
        } else {
            // Secure the input data
            $name = mysqli_real_escape_string($conn, $name);
            $username = mysqli_real_escape_string($conn, $username);
            $email = mysqli_real_escape_string($conn, $email);
            $PhoneNumber = mysqli_real_escape_string($conn, $PhoneNumber);
            $passwordHash = mysqli_real_escape_string($conn, password_hash($password, PASSWORD_DEFAULT));

            // Insert user details into USERDETAILS table
            $insertTableSql = "INSERT INTO USERDETAILS (name, username, email, PhoneNumber, password)
                               VALUES ('$name', '$username', '$email', '$PhoneNumber', '$passwordHash')";

            if (mysqli_query($conn, $insertTableSql)) {
                echo "New user record created.<br>";

                // Send welcome email after successful registration
                $subject = "Welcome to our platform!";
                $message = "<h1>Welcome, $name!</h1><p>Thank you for signing up. Your username is: $username.</p>";
                sendMail($email, $subject, $message);

                // Simulate chatbot response
                echo "<h2>Chatbot says: 'Hello $name, welcome to our platform! You are all set up. Let's get started!'</h2>";

                // Redirect to login page
                header("Location: http://localhost/ChatBotProject/LoginPage.html");
                exit;
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }

        mysqli_close($conn);
    }
}
?>
