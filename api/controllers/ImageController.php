<?php
// Include model and db
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Image.php';
class ImageController {
    private $db;
    private $image;
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->image = new Image($this->db);
    }
    // GET /images
    public function getImages() {
        $stmt = $this->image->read();
        $num = $stmt->rowCount();
        if($num > 0) {
            $images_arr = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $image_item = array(
                    "id" => $id,
                    "filename" => $filename,
                    "filepath" => $filepath,
                    "extension" => $extension,
                    "uploaded_at" => $uploaded_at
                );
                array_push($images_arr, $image_item);
            }
            http_response_code(200);
            echo json_encode($images_arr);
        } else {
        http_response_code(200); // still OK, empty array
        echo json_encode([]);
        }
    }
    // POST /upload
    public function uploadImage() {
        // Check if file was sent
        if(!isset($_FILES['image'])) {
            http_response_code(400);
            echo json_encode(array("message" => "No image file provided."));
            return;
        }
        $file = $_FILES['image'];
        $allowed_extensions = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'webm'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        // Validate extension
        if(!in_array($extension, $allowed_extensions)) {
            http_response_code(400);
            echo json_encode(array("message" => "File type not allowed."));
            return;
        }  
        // Generate unique filename to avoid conflicts
        $new_filename = uniqid() . '.' . $extension;
        $upload_dir = '../uploads/';
        $destination = $upload_dir . $new_filename;
        if(move_uploaded_file($file['tmp_name'], $destination)) {
        // Save metadata to database
        $this->image->filename = $file['name']; // original name
        $this->image->filepath = 'uploads/' . $new_filename; // relative path from root
        $this->image->extension = $extension;
        if($this->image->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Image uploaded successfully."));
        } else {
        // Delete the file if DB insert fails
        unlink($destination);
        http_response_code(500);
        echo json_encode(array("message" => "Failed to save image metadata."));
        }
        } else {
        http_response_code(500);
        echo json_encode(array("message" => "Failed to move uploaded file."));
        }
    }
    // DELETE /images/{id}
    public function deleteImage($id) {
        // First, fetch the image record to get filepath
        $query = "SELECT filepath FROM images WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$image) {
        http_response_code(404);
        echo json_encode(array("message" => "Image not found."));
        return;
        }
        // Delete file from server
        $filepath = '../' . $image['filepath']; // because filepath is relative to root
        if(file_exists($filepath)) {
        unlink($filepath);
        }
        // Delete from database
        $this->image->id = $id;
        if($this->image->delete()) {
        http_response_code(200);
        echo json_encode(array("message" => "Image deleted."));
        } else {
        http_response_code(500);
        echo json_encode(array("message" => "Failed to delete image record."));
        }
    }
}
?>