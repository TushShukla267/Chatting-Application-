<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
</head>
<body>
    <h1>Page to Reset Password</h1>
    <?php

    // Include PHPMailer classes and autoload
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'vendor/autoload.php';

    // Function to send email
    function sendMail($to, $subject, $message) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'tushar.shukla@somaiya.edu';  // Your SMTP username
            $mail->Password = 'jgyt lhlc yivq idkc';        // Your SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('tushar.shukla@somaiya.edu', 'Tushar J Shukla');
            $mail->addAddress($to); // Add recipient email

            $mail->isHTML(true); // Email format
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            echo 'Reset password email has been sent.<br>';
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    // Function to generate a new password
    function generatePassword($length) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$';
        $charactersLength = strlen($characters);
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, $charactersLength - 1);
            $password .= $characters[$randomIndex];
        }

        return $password;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['email']) && isset($_POST['username'])) {
            $email = $_POST['email'];
            $username = $_POST['username'];

            echo "Email Entered: " . $email . "<br>";
            echo "Username Entered: " . $username . "<br>";

            require 'ConnectChatbot.php'; // Database connection

            // Query to check if email exists in the USERDETAILS table
            $displayRows = "SELECT * FROM USERDETAILS WHERE email = '$email'";
            $result = mysqli_query($conn, $displayRows);

            if (mysqli_num_rows($result) > 0) {
                // Generate a new password
                $newPassword = generatePassword(8);
                echo "Your new password is: " . $newPassword . "<br>";
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update password in the database
                $updatePasswordSql = "UPDATE USERDETAILS SET password = '$hashedPassword' WHERE email = '$email'";
                if (mysqli_query($conn, $updatePasswordSql)) {
                    echo '<h1>Password Updated Successfully</h1>';

                    // Prepare email content
                    $subject = "Password Reset Notification";
                    $message = "<h1>Password Reset</h1>
                                <p>Hello, your new password is: <strong>$newPassword</strong></p>
                                <p>Please change it after logging in for security purposes.</p>";

                    // Send email with new password
                    sendMail($email, $subject, $message);

                    // Redirect script
                    echo '<script>
                    setTimeout(() => {
                        alert("Please Copy the Given Password. The page will be redirected soon to protect your password.");
                        setTimeout(() => {
                            window.location.href = "http://localhost/ChatBotProject/LoginPage.html";
                        }, 15000); // 15 seconds after the alert
                    }, 5000); // 5 seconds before showing the alert
                    </script>';
                } else {
                    echo 'Cannot update password. Please try again.';
                }
            } else {
                echo "User does not exist. Please check the entered email.";
            }
        }
    } else {
        die("Please submit the form using POST method.");
    }

    ?>

</body>
</html>
