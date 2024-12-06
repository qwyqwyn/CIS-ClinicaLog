<?php
class SystemLogs {
    private $conn;
    private $table_name = "systemlog";

    public $id;
    public $userid;
    public $date;
    public $time;
    public $action;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllSystemLogs() {
        $query = "
            SELECT 
                s.syslog_id AS id, 
                a.user_idnum AS idnum, 
                CONCAT(a.user_fname, ' ', a.user_lname) AS name, 
                s.syslog_date AS date, 
                s.syslog_time AS time, 
                s.syslog_action AS action
            FROM " . $this->table_name . " s
            LEFT JOIN adminusers a ON s.syslog_userid = a.user_idnum
            ORDER BY s.syslog_date DESC, s.syslog_time DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}

?> 
