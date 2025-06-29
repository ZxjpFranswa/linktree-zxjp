<?php
class Database {
    private $host = "localhost";
    private $dbname = "i_linktree";
    private $username = "root";
    private $password = "";
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection Error?");
        }
        // echo "Connected to the database successfully!";
        return $this->conn;
    }
}
// $db = new Database();
// $db->connect();
?>

