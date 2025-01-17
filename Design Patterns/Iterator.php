<?php
interface CustomIterator {
    public function hasNext(): bool;
    public function next();
}





class PendingAccountsIterator implements CustomIterator {
    private $accounts;
    private $position = 0;

    public function __construct(array $accounts) {
        $this->accounts = $accounts;
        error_log("[DEBUG] PendingAccountsIterator initialized with " . count($accounts) . " accounts.");
    }
    

    // Check if there are more elements
    public function hasNext(): bool {
        $hasNext = $this->position < count($this->accounts);
        error_log("[DEBUG] PendingAccountsIterator - hasNext() called. Position: {$this->position}, Has Next: " . ($hasNext ? "true" : "false"));
        return $hasNext;
    }

    // Return the next element
    public function next() {
        if (!$this->hasNext()) {
            error_log("[DEBUG] PendingAccountsIterator - next() called but no more elements available.");
            throw new Exception("No more elements.");
        }
        $currentAccount = $this->accounts[$this->position];
        error_log("[DEBUG] PendingAccountsIterator - next() called. Returning account at position {$this->position}: " . json_encode($currentAccount));
        $this->position++;
        return $currentAccount;
    }
}
?>
