<?php
// MailFacade.php
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

class MailFacade {
    private $mailers = [];

    public function __construct() {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $maxAccounts = 2;

        for ($i = 1; $i <= $maxAccounts; $i++) {
            if (!isset($_ENV["MAIL_HOST_{$i}"])) {
                continue;
            }

            try {
                $mailer = new PHPMailer(true);
                // Optional: enable debug for PHPMailer:
                // $mailer->SMTPDebug = 2; // or 3 or 4, higher is more verbose
                // $mailer->Debugoutput = function($str, $level) {
                //     error_log("[PHPMailer Debug $level] $str");
                // };

                $mailer->isSMTP();
                $mailer->Host       = $_ENV["MAIL_HOST_{$i}"];
                $mailer->SMTPAuth   = true;
                $mailer->Username   = $_ENV["MAIL_USERNAME_{$i}"];
                $mailer->Password   = $_ENV["MAIL_PASSWORD_{$i}"];
                $mailer->SMTPSecure = $_ENV["MAIL_ENCRYPTION_{$i}"];
                $mailer->Port       = $_ENV["MAIL_PORT_{$i}"];

                $mailer->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);

                $this->mailers[$i] = $mailer;
                error_log("[MailFacade] Loaded account #{$i} with host: " . $_ENV["MAIL_HOST_{$i}"]);
            } catch (Exception $e) {
                error_log("Error initializing Mailer for account {$i}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Send an email using a specific account (1, 2, etc.)
     */
    public function sendEmail($account, $to, $subject, $body, $altBody = null, $attachments = null) {
        if (!isset($this->mailers[$account])) {
            error_log("[MailFacade] Mailer for account #{$account} is not configured.");
            return false;
        }

        $mailer = $this->mailers[$account];
        try {
            error_log("[MailFacade] Sending email using account #{$account} to: $to");

            // Clear old data
            $mailer->clearAddresses();
            $mailer->clearAttachments();

            $mailer->addAddress($to);
            $mailer->Subject = $subject;
            $mailer->isHTML(true);
            $mailer->Body = $body;

            if ($altBody) {
                $mailer->AltBody = $altBody;
            }

            if ($attachments) {
                foreach ($attachments as $attachment) {
                    $mailer->addAttachment($attachment);
                }
            }

            $mailer->send();

            error_log("[MailFacade] Email to $to sent successfully (account #{$account}).");
            return true;
        } catch (Exception $e) {
            error_log("[MailFacade] Error sending email with account #{$account} to $to: " . $mailer->ErrorInfo);
            return false;
        }
    }
}
