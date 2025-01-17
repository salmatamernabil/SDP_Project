<?php

// Observer Interface
interface IObserver {
    public function update($message);
}

// Subject Interface
interface ISubject {
    public function registerObserver(IObserver $observer);
    public function removeObserver(IObserver $observer);
    public function notifyObservers($message);
}

// Observer Implementation for Admin
class AdminObserver implements IObserver {
    private $email;
    private $mailer;

    public function __construct($email, $mailer) {
        $this->email = $email;
        $this->mailer = $mailer;
    }

    public function update($message) {
        $subject = "Notification";
        $body = "<p>$message</p>";
      //  $this->mailer->sendEmail($this->email, $subject, $body);
        $this->mailer->sendEmail(1, $this->email, $subject, $body);
        $this->mailer->sendEmail(2, $this->email, $subject, $body);
    }
}

// Subject Implementation
class NotificationSystem implements ISubject {
    private $observers = [];

    public function registerObserver(IObserver $observer) {
        $this->observers[] = $observer;
    }

    public function removeObserver(IObserver $observer) {
        $this->observers = array_filter($this->observers, function($obs) use ($observer) {
            return $obs !== $observer;
        });
    }

    public function notifyObservers($message) {
        foreach ($this->observers as $observer) {
            $observer->update($message);
        }
    }
}



?>
