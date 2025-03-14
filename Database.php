<?php
class Database {
    const DSN = 'mysql:host=localhost';
    const USERNAME = 'root';
    const PASSWORD = '';

    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $this->conn = new PDO(self::DSN, self::USERNAME, self::PASSWORD);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->conn->exec("CREATE DATABASE IF NOT EXISTS radovi CHARACTER SET utf8 COLLATE utf8_general_ci");
            $this->conn->exec("USE radovi");

            $this->conn->exec("CREATE TABLE IF NOT EXISTS diplomski_radovi (
                id INT AUTO_INCREMENT PRIMARY KEY,
                naziv_rada VARCHAR(255) NOT NULL,
                tekst_rada TEXT,
                link_rada VARCHAR(255) NOT NULL,
                oib_tvrtke VARCHAR(11) NOT NULL
            )");
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}