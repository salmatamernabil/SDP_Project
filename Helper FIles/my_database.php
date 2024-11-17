<?php
class Database {
    private static $instance = null;
    private $connection;

    // Private constructor to prevent direct creation
    private function __construct() {
        $host = "localhost";
        $username = "root";
        $password = "";
        $db_name = "sdp";

        // Create a new database connection
        $this->connection = new mysqli($host, $username, $password, $db_name);

        // Check for connection errors
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    // Method to get the instance of the Database
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Method to get the connection
    public function getConnection() {
        return $this->connection;
    }
}
?>
