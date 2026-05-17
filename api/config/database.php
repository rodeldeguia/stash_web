 <?php

class Database {
private $host = "127.0.0.1";
private $db_name = "gallery_db";
private $username = "root";
private $password = "rooting3";
private $port = "3308";
public $conn;
public function getConnection() {
$this->conn = null;
try {
$this->conn = new PDO("mysql:host=" . $this->host .";port=" . $this->port .";dbname=" . $this->db_name, $this->username, $this->password);

$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $exception) {
echo "Connection error: " . $exception->getMessage();
}
return $this->conn;
}
}
echo "Database connection file loaded successfully.";
?>
