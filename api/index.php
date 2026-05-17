<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit();
}
require_once 'controllers/ImageController.php';
$controller = new ImageController();
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$resource = $request_uri[0] ?? '';
switch($method) {
    case 'GET':
        if($resource === 'images') {
            $controller->getImages();
        } 
        else {
            http_response_code(404);
            echo json_encode(array("message" => "Endpoint not found."));
        }
    break;
    case 'POST':
    if($resource === 'upload') {
    $controller->uploadImage();
    } else {
    http_response_code(404);
    echo json_encode(array("message" => "Endpoint not found."));
    }
    break;
    case 'DELETE':
    if($resource === 'images' && isset($request_uri[1]) && is_numeric($request_uri[1])) {
        $controller->deleteImage($request_uri[1]);
    } else {
    http_response_code(404);
    echo json_encode(array("message" => "Endpoint not found."));
    }
    break;
    default:
    http_response_code(405);
    echo json_encode(array("message!!" => "Method not allowed."));
    break;
}

echo json_encode(array("messagsxse" => "API endpoint not found."));    
?>