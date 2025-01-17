<?php
interface DonationState {
    public function proceed(DonationStateManager $context);
    public function fail(DonationStateManager $context);
    public function complete(DonationStateManager $context);
}

class InitialState implements DonationState {
    public function proceed(DonationStateManager $context) {
        // Transition to PendingState when the user proceeds to the payment page
        $context->setState(new PendingState());
        error_log("[State] Transitioned from Initial to Pending.");
        return "Proceeding to payment page.";
    }

    public function fail(DonationStateManager $context) {
        error_log("[State] Cannot fail donation. It is still in Initial state.");
        return "Cannot fail. Donation is still in the initial state.";
    }

    public function complete(DonationStateManager $context) {
        error_log("[State] Cannot complete donation. It is still in Initial state.");
        return "Cannot complete. Donation is still in the initial state.";
    }
}

class PendingState implements DonationState {
    public function proceed(DonationStateManager $context) {
        // Transition to ProcessingState when the user submits payment details
        $context->setState(new ProcessingState());
        error_log("[State] Transitioned from Pending to Processing.");
        return "Payment details submitted. Processing donation.";
    }

    public function fail(DonationStateManager $context) {
        error_log("[State] Cannot fail donation. It is still in Pending state.");
        return "Cannot fail. Donation is still pending.";
    }

    public function complete(DonationStateManager $context) {
        error_log("[State] Cannot complete donation. It is still in Pending state.");
        return "Cannot complete. Donation is still pending.";
    }
}

class ProcessingState implements DonationState {
    public function proceed(DonationStateManager $context) {
        error_log("[State] Donation is already being processed.");
        return "Donation is already in Processing state.";
    }

    public function fail(DonationStateManager $context) {
        $context->setState(new FailedState());
        error_log("[State] Transitioned from Processing to Failed.");
        return "Donation failed.";
    }

    public function complete(DonationStateManager $context) {
        $context->setState(new CompletedState());
        error_log("[State] Transitioned from Processing to Completed.");
        return "Donation completed successfully.";
    }
}

class FailedState implements DonationState {
    public function proceed(DonationStateManager $context) {
        error_log("[State] Cannot proceed. Donation has failed.");
        return "Cannot proceed. Donation has failed.";
    }

    public function fail(DonationStateManager $context) {
        error_log("[State] Donation is already in Failed state.");
        return "Donation is already failed.";
    }

    public function complete(DonationStateManager $context) {
        error_log("[State] Cannot complete donation. It has failed.");
        return "Cannot complete. Donation has failed.";
    }
}

class CompletedState implements DonationState {
    public function proceed(DonationStateManager $context) {
        error_log("[State] Cannot proceed. Donation is already completed.");
        return "Cannot proceed. Donation is already completed.";
    }

    public function fail(DonationStateManager $context) {
        error_log("[State] Cannot fail donation. It is already completed.");
        return "Cannot fail. Donation is already completed.";
    }

    public function complete(DonationStateManager $context) {
        error_log("[State] Donation is already in Completed state.");
        return "Donation is already completed.";
    }
}

class DonationStateManager {
    private $state;

    public function __construct() {
        $this->state = new InitialState(); // Initial state is InitialState
        error_log("[StateManager] Initial state set to Initial.");
    }

    public function setState(DonationState $state) {
        $this->state = $state;
        error_log("[StateManager] State changed to: " . get_class($state));
    }

    public function getState() {
        error_log("[StateManager] Current state: " . get_class($this->state));
        return $this->state;
    }

    public function proceed() {
        error_log("[StateManager] Proceeding from current state: " . get_class($this->state));
        return $this->state->proceed($this);
    }

    public function fail() {
        error_log("[StateManager] Failing from current state: " . get_class($this->state));
        return $this->state->fail($this);
    }

    public function complete() {
        error_log("[StateManager] Completing from current state: " . get_class($this->state));
        return $this->state->complete($this);
    }
}
?>
