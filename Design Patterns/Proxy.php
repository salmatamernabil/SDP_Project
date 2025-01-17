<?php

interface IDBInterface {
    public function executeQuery($query);
}

class ConcreteImpl implements IDBInterface {
    private $connection;

    public function __construct() {
        $this->connection = Database::getInstance()->getConnection();
    }

    public function executeQuery($query, $params = null) {
        error_log("[DEBUG] ConcreteImpl - Query: $query | Params: " . json_encode($params));
    
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement. Query: $query | Error: " . $this->connection->error);
        }
    
        if (!is_null($params) && is_array($params)) {
            $types = str_repeat('s', count($params));
            if (!$stmt->bind_param($types, ...$params)) {
                throw new Exception("Failed to bind parameters. Query: $query | Params: " . json_encode($params));
            }
        }
    
        // Here is where you put your snippet:
        if (!$stmt->execute()) {
            error_log("[ERROR] Query execution failed: " . $stmt->error);
            return false;
        }
    
        // If it’s a SELECT query, return the result
        if (str_starts_with(strtoupper($query), 'SELECT')) {
            return $stmt->get_result();
        }
    
        // For INSERT, UPDATE, or DELETE queries, return true on success
        return true;
    }
     
    
}


class DBProxy implements IDBInterface {
    private $realDb;
    private $userRole;

    public function __construct($userRole) {
        $this->realDb = new ConcreteImpl();
        $this->userRole = $userRole;
    }

    public function executeQuery($query, $params = null) {
        error_log("[DEBUG] DBProxy - Query: $query | Params: " . json_encode($params));
    
        // Check the type of query
        $queryType = $this->getQueryType($query);
    
        // Extract the table name from the query
        preg_match('/\\b(?:FROM|INTO|UPDATE)\\s+(\\w+)/i', $query, $matches);
        $table = $matches[1] ?? 'unknown';
    
        // Enforce permissions based on user roles and tables
        if (!$this->hasPermission($queryType, $table)) {
            $error = "Access Denied: User role '{$this->userRole}' does not have permission for {$queryType} on table {$table}.";
            error_log("[ERROR] $error");
            return $error; // Return the error message instead of throwing
        }
    
        // Delegate to the real database implementation
        return $this->realDb->executeQuery($query, $params);
    }
    
    
    private function hasPermission($queryType, $table) {
        $permissions = [
            'ChiefAdmin' => [
                'SELECT' => ['*'], // Full access to all tables
                'INSERT' => ['*'],
                'UPDATE' => ['*'],
                'DELETE' => ['*']
            ],
            'SuperAdmin' => [
                'SELECT' => ['*'], // Full SELECT access
                'INSERT' => ['*'],
                'UPDATE' => ['patient', 'course', 'member'], // Can update patients, courses, and members
                'DELETE' => ['pending_members']// No DELETE permissions
            ],
            'BaseAdmin' => [
                'SELECT' => ['patient', 'bmi', 'pending_members'], // Restricted SELECT access
                'INSERT' => ['patient', 'add_patient_detail', 'add_bmi_detail', 'add_bmi','member', 'approve_account_member', 'approve_account_member_detail','doctor','trainee'], // Can insert into patient and related tables
                'UPDATE' => [], // No UPDATE permissions
                'DELETE' =>['pending_members'] // No DELETE permissions
            ],
            'DonationAdmin' => [
                'SELECT' => ['patient', 'bmi', 'pending_members','donatesupplies_verb','donatesupplies_verb_detail','donation_supplies_item'], // Restricted SELECT access
                'INSERT' => ['patient', 'add_patient_detail', 'add_bmi_detail', 'add_bmi','member', 'approve_account_member', 'approve_account_member_detail','doctor','trainee','donatesupplies_verb','donatesupplies_verb_detail','donation_supplies_item'], // Can insert into patient and related tables
                'UPDATE' => ['donatesupplies_verb','donatesupplies_verb_detail','donation_supplies_item'], // No UPDATE permissions
                'DELETE' =>['pending_members'] // No DELETE permissions
            ],
            'PaymentAdmin' => [
                'SELECT' => ['patient', 'bmi', 'pending_members',], // Restricted SELECT access
                'INSERT' => ['patient', 'add_patient_detail', 'add_bmi_detail', 'add_bmi','member', 'approve_account_member', 'approve_account_member_detail','doctor','trainee',], // Can insert into patient and related tables
                'UPDATE' => ['donationobject', 'course'],
                'DELETE' =>['pending_members'] // No DELETE permissions
            ]
        ];
    
        // Get allowed tables for this role and query type
        $allowedTables = $permissions[$this->userRole][$queryType] ?? [];
    
        // Allow access if '*' (all tables) or the specific table is allowed
        return in_array('*', $allowedTables) || in_array($table, $allowedTables);
    }
    
    
    // Helper function to determine the type of query
    private function getQueryType($query) {
        $query = strtoupper(trim($query));
        if (str_starts_with($query, 'SELECT')) return 'SELECT';
        if (str_starts_with($query, 'INSERT')) return 'INSERT';
        if (str_starts_with($query, 'UPDATE')) return 'UPDATE';
        if (str_starts_with($query, 'DELETE')) return 'DELETE';

        return 'UNKNOWN'; // Handle edge cases
    }
}

?>
