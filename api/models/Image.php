<?php
class Image {
private $conn;
private $table = "images";
public $id;
public $filename;
public $filepath;
public $extension;
public function __construct($db) {
$this->conn = $db;
}
public function read() {
$query = "SELECT * FROM " . $this->table . " ORDER BY uploaded_at DESC";
$stmt = $this->conn->prepare($query);
$stmt->execute();
return $stmt;
}
public function create() {
$query = "INSERT INTO " . $this->table . " SET filename=:filename, filepath=:filepath, 
extension=:extension";
$stmt = $this->conn->prepare($query);
$this->filename = htmlspecialchars(strip_tags($this->filename));
$this->filepath = htmlspecialchars(strip_tags($this->filepath));
$this->extension = htmlspecialchars(strip_tags($this->extension));
$stmt->bindParam(":filename", $this->filename);
$stmt->bindParam(":filepath", $this->filepath);
$stmt->bindParam(":extension", $this->extension);
if($stmt->execute()) {
return true;
}
return false;
}
public function delete() {
$query = "DELETE FROM " . $this->table . " WHERE id = :id";
$stmt = $this->conn->prepare($query);
$this->id = htmlspecialchars(strip_tags($this->id));
$stmt->bindParam(':id', $this->id);
if($stmt->execute()) {
return true;
}
return false;
}
}
?>