<?php
// Strategy.php

interface DonationStrategy {
    public function donate(float $amount);
    public function getInfo();
}

class CashDonation implements DonationStrategy {
    public function donate(float $amount) {
        // Implement cash donation logic here
        echo "Donated {$amount} in cash.<br>";
    }

    public function getInfo() {
        return "Proceed with cash payment at our office.";
    }
}

class VisaDonation implements DonationStrategy {
    public function donate(float $amount) {
        // Implement Visa donation logic here
        echo "Donated {$amount} via Visa.<br>";
    }

    public function getInfo() {
        return "Enter your Visa card details to complete the payment.";
    }
}

class FawryDonation implements DonationStrategy {
    public function donate(float $amount) {
        // Implement Fawry donation logic here
        echo "Donated {$amount} via Fawry.<br>";
    }

    public function getInfo() {
        return "Use your Fawry account or Fawry code for payment.";
    }
}

class DonationContext {
    private $donationStrategy;

    public function setStrategy(DonationStrategy $donationStrategy) {
        $this->donationStrategy = $donationStrategy;
    }

    public function executeDonation(float $amount) {
        if ($this->donationStrategy) {
            $this->donationStrategy->donate($amount);
        }
    }

    public function getStrategyInfo() {
        return $this->donationStrategy ? $this->donationStrategy->getInfo() : null;
    }
}
?>
