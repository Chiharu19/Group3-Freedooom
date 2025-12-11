<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{

    private $mail;
    private $config;


    public function __construct()
    {
        $configFile = __DIR__ . '/../config/email_config.php';

        if (!file_exists($configFile)) {
            error_log("Email config file missing: $configFile");
            return;
        }

        $this->config = require $configFile;

        // Check if PHPMailer class exists (Autoloaded)
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $this->mail = new PHPMailer(true);
            $this->setup();
        }
    }

    private function setup()
    {
        if (!$this->mail)
            return;

        try {
            //Server settings
            $this->mail->isSMTP();
            $this->mail->Host = $this->config['host'];
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $this->config['username'];
            $this->mail->Password = $this->config['password'];
            $this->mail->SMTPSecure = $this->config['encryption'];
            $this->mail->Port = $this->config['port'];

            //Recipients
            $this->mail->setFrom($this->config['from_address'], $this->config['from_name']);
        } catch (Exception $e) {
            // Log error?
            error_log("EmailService Setup Error: " . $this->mail->ErrorInfo);
        }
    }

    public function sendEmail($to, $subject, $body)
    {
        if (!$this->mail) {
            // Fallback to mail() if strict required, or just log error that PHPMailer is missing
            error_log("PHPMailer not available. Cannot send email to $to");
            return false;
        }

        try {
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;

            $this->mail->send();
            $this->mail->clearAddresses(); // Clear for next use if persistent
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    private function renderTemplate($template, $data)
    {
        $path = __DIR__ . '/../views/emails/' . $template . '.php';
        if (file_exists($path)) {
            ob_start();
            include $path;
            return ob_get_clean();
        }
        return "";
    }

    public function sendBookingRequestNotification($to, $requestDetails)
    {
        $subject = "New Booking Request Submitted";
        $body = $this->renderTemplate('booking_request', $requestDetails);
        return $this->sendEmail($to, $subject, $body);
    }

    public function sendRequestStatusNotification($to, $status, $comments, $requestDetails = [])
    {
        $subject = "Booking Request Update: " . ucfirst($status);
        $data = [
            'status' => $status,
            'comments' => $comments,
            'request_details' => $requestDetails
        ];
        $body = $this->renderTemplate('status_update', $data);
        return $this->sendEmail($to, $subject, $body);
    }

    public function sendAccountCreatedNotification($to, $name, $password)
    {
        $subject = "Welcome to Freedooom";
        $data = ['name' => $name, 'email' => $to, 'password' => $password];
        $body = $this->renderTemplate('account_created', $data);
        return $this->sendEmail($to, $subject, $body);
    }

    public function sendPasswordResetLink($to, $token)
    {
        $subject = "Password Reset Request";
        $link = "http://" . $_SERVER['HTTP_HOST'] . "/public/reset_password.php?token=" . $token;
        $data = ['link' => $link];
        $body = $this->renderTemplate('password_reset', $data);
        return $this->sendEmail($to, $subject, $body);
    }

    public function sendBookingCancellationNotification($to, $details)
    {
        $subject = "Booking Request Cancelled";
        $body = $this->renderTemplate('booking_cancellation', $details);
        return $this->sendEmail($to, $subject, $body);
    }
}
