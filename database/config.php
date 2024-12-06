<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "clinicalog";
    public $pdo;

    // Constructor to automatically connect when an instance is created
    public function __construct() {
        $this->connect(); 
    }

    // Method to create and return a PDO connection 
    private function connect() {
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname;
        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    } 

    // Method to get the PDO connection
    public function getConnection() {
        return $this->pdo;
    }

    // Method to execute a query and return results
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Method to fetch all results as an associative array
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function prepare($query) {
        return $this->pdo->prepare($query);
    }
}
?>
