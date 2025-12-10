<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {

    private $mail;
    private $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../config/email_config.php';
        
        // Check if PHPMailer class exists (Autoloaded)
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $this->mail = new PHPMailer(true);
            $this->setup();
        }
    }

    private function setup() {
        if (!$this->mail) return;

        try {
            //Server settings
            $this->mail->isSMTP();
            $this->mail->Host       = $this->config['host'];
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $this->config['username'];
            $this->mail->Password   = $this->config['password'];
            $this->mail->SMTPSecure = $this->config['encryption'];
            $this->mail->Port       = $this->config['port'];

            //Recipients
            $this->mail->setFrom($this->config['from_address'], $this->config['from_name']);
        } catch (Exception $e) {
            // Log error?
            error_log("EmailService Setup Error: " . $this->mail->ErrorInfo);
        }
    }

    public function sendEmail($to, $subject, $body) {
        if (!$this->mail) {
            // Fallback to mail() if strict required, or just log error that PHPMailer is missing
            error_log("PHPMailer not available. Cannot send email to $to");
            return false;
        }

        try {
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            $this->mail->send();
            $this->mail->clearAddresses(); // Clear for next use if persistent
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    public function sendBookingRequestNotification($to, $requestDetails) {
        $subject = "New Booking Request Submitted";
        $body = "
            <h3>New Booking Request</h3>
            <p><strong>Room:</strong> {$requestDetails['room_name']}</p>
            <p><strong>Date:</strong> {$requestDetails['date']}</p>
            <p><strong>Time:</strong> {$requestDetails['start_time']} ({$requestDetails['duration']} hrs)</p>
            <p><strong>Purpose:</strong> {$requestDetails['purpose']}</p>
            <p>Please log in to the dashboard to approve or deny this request.</p>
        ";
        return $this->sendEmail($to, $subject, $body);
    }

    public function sendRequestStatusNotification($to, $status, $comments, $requestDetails = []) {
        $subject = "Booking Request Update: " . ucfirst($status);
        $body = "
            <h3>Your booking request has been {$status}</h3>
            <p><strong>Comments:</strong> " . ($comments ?: "None") . "</p>
        ";
        // If we have details, add them
        if (!empty($requestDetails)) {
             $body .= "<p><strong>Details:</strong> {$requestDetails['room_name']} on {$requestDetails['date']}</p>";
        }

        return $this->sendEmail($to, $subject, $body);
    }

    public function sendAccountCreatedNotification($to, $name, $password) {
        $subject = "Welcome to Freedooom";
        $body = "
            <h3>Welcome, $name!</h3>
            <p>Your account has been created.</p>
            <p><strong>Username/Email:</strong> $to</p>
            <p><strong>Password:</strong> $password</p>
            <p>Please change your password after logging in.</p>
        ";
        return $this->sendEmail($to, $subject, $body);
    }
}
