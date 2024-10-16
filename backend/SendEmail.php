<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Get the JSON data from the request
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData, true);

// Check if the data is valid JSON
if ($data === null) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data.']);
    exit();
}

// Extract fields from the decoded JSON data and sanitize them
$email = htmlspecialchars($data['email']);
$phone = htmlspecialchars($data['phone']);
$message = htmlspecialchars($data['message']);

// Send emails to both the admin and the client
$adminEmailSent = sendAdminEmail($email, $phone, $message);
$clientEmailSent = sendClientConfirmationEmail($email, $phone, $message);

// Prepare the response array based on whether the emails were sent successfully
$response = array();
if ($adminEmailSent && $clientEmailSent) {
    $response['status'] = 'success';
    $response['message'] = 'Emails sent successfully.';
} else {
    $response['status'] = 'error';
    $response['message'] = 'There was an issue sending the emails.';
}

// Return the JSON response
echo json_encode($response);

/**
 * Function to send the admin email
 */
function sendAdminEmail($email, $phone, $message)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP server configuration for Zoho
        $mail->isSMTP();
        $mail->Host = 'smtp.zoho.com'; // Change from gmail to zoho
        $mail->SMTPAuth = true;
        $mail->Username = 'admin@lexvictoria.com'; // Your Zoho email
        $mail->Password = 'BmLPzH3hB625'; // Your Zoho email password or app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS
        $mail->Port = 587; // TLS port

        // Set sender and recipient details
        $mail->setFrom('admin@lexvictoria.com', 'Lex Victoria');
        $mail->addAddress('admin@lexvictoria.com'); // Admin address

        // Email subject and body
        $mail->isHTML(true);
        $mail->Subject = 'Please Answer, a new client has a query';

        // Build the email body with the submitted data
        $mail->Body = '<p>You have received a new contact form submission. Details are as follows:</p>' .
            '<table border="1" cellpadding="5" cellspacing="0">' .
            '<tr><td>Email:</td><td>' . $email . '</td></tr>' .
            '<tr><td>Phone:</td><td>' . $phone . '</td></tr>' .
            '<tr><td>Message:</td><td>' . nl2br($message) . '</td></tr>' .
            '</table>';

        $mail->Body .= '<p>Thank you!</p>';

        // Send the email
        return $mail->send();
    } catch (Exception $e) {
        error_log('PHPMailer Error: ' . $e->getMessage());
        return false;
    }
}


/**
 * Function to send confirmation email to the client
 */
function sendClientConfirmationEmail($email, $phone, $message)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP server configuration for Zoho Mail
        $mail->isSMTP();
        $mail->Host = 'smtp.zoho.com'; // Zoho SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'admin@lexvictoria.com'; // Your Zoho business email
        $mail->Password = 'BmLPzH3hB625'; // Your Zoho email password or app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Set sender and recipient details
        $mail->setFrom('admin@lexvictoria.com', 'Lex Victoria');
        $mail->addAddress($email); // Replace with the actual recipient

        // Email subject and body
        $mail->isHTML(true);
        $mail->Subject = 'Confirmation of Your Contact Form Submission';

        // Build the email body with the submitted data and a confirmation message
        $mail->Body = '<p>Dear Client,</p>' .
            '<p>Thank you for contacting us! We have successfully received your request and our team is currently reviewing the details. We appreciate your interest in our services and will get back to you within the next 24 hours.</p>' .

            '<p>Here are the details you provided in your submission:</p>' .
            '<table border="1" cellpadding="5" cellspacing="0">' .
            '<tr><td><strong>Email:</strong></td><td>' . $email . '</td></tr>' .
            '<tr><td><strong>Phone:</strong></td><td>' . $phone . '</td></tr>' .
            '<tr><td><strong>Message:</strong></td><td>' . nl2br($message) . '</td></tr>' .
            '</table>' .

            '<p>One of our representatives will reach out to you via email, phone, or WhatsApp shortly to assist you further. In the meantime, if you have any urgent inquiries, please feel free to reply to this email or contact us directly at <a href="tel:+923056302799">+92-305-6302799</a>.</p>' .

            '<p>We value your time and look forward to providing you with exceptional service.</p>' .

            '<p>Best regards,<br><strong>Lex Victoria</strong><br>admin@lexvictoria.com<br><a href="tel:+923056302799">+92-305-6302799</a></p>';

        // Send the email
        return $mail->send();
    } catch (Exception $e) {
        error_log('PHPMailer Error: ' . $e->getMessage());
        return false;
    }
}

