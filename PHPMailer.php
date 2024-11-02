<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendMail($to , $subject , $message , $cc , $additionalHeaders = null) {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Set mailer to use SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';    // Set the SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tushar.shukla@somaiya.edu'; // SMTP username
        $mail->Password   = 'jgyt lhlc yivq idkc';    // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Set email format to HTML
        $mail->isHTML(true);

        // Recipients
        $mail->setFrom('tushar.shukla@somaiya.edu', 'Tushar J Shukla');
        $mail->addAddress($to);

        // Add CC if provided
        if ($cc) {
            $mail->addCC($cc);
        }

        // Add additional headers if provided
        if ($additionalHeaders) {
            foreach ($additionalHeaders as $key => $value) {
                $mail->addCustomHeader($key, $value);
            }
        }

        // Content
        $mail->Subject = $subject;
        $mail->Body    = $message;

        // Send email
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Example usage
$to = "tushar.shukla@somaiya.edu";
$subject = "Test Email";
$message = "<h1>Hello!</h1><p>This is a test email.</p>";
$cc = "parv.golchha@somaiya.edu";
$additionalHeaders = ['Reply-To' => 'support@yourdomain.com'];

sendMail($to, $subject, $message, $cc, $additionalHeaders);


?>
