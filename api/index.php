<?php
// API REST para GarageX
header('Content-Type: application/json');

// Permitir solicitudes desde el mismo origen (CORS)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Verificar que el usuario esté autenticado
session_start();
if (!isset($_SESSION['user_id']) && !preg_match('/\/api\/login/', $_SERVER['REQUEST_URI'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]);
    exit;
}

// Incluir la conexión a la base de datos
require_once '../config/database.php';

// Sanitizar entrada
function sanitize_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

// Obtener parámetros
$resource = isset($_GET['resource']) ? sanitize_input($_GET['resource']) : null;
$action = isset($_GET['action']) ? sanitize_input($_GET['action']) : null;
$id = isset($_GET['id']) ? sanitize_input($_GET['id']) : null;

// Si no hay resource en GET, intentar obtenerlo de la URL
if (!$resource) {
    // Parsear la URL para obtener el endpoint y los parámetros
    $request_uri = $_SERVER['REQUEST_URI'];
    $uri_parts = explode('/api/', $request_uri);

    if (count($uri_parts) < 2) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint no encontrado'
        ]);
        exit;
    }

    $endpoint = $uri_parts[1];
    $endpoint_parts = explode('/', $endpoint);
    $resource = $endpoint_parts[0];
    
    // Si hay más partes, la segunda es la acción o ID
    if (isset($endpoint_parts[1])) {
        if ($action === null) {
            $action = $endpoint_parts[1];
        }
    }
    
    // Si hay más partes, la tercera es el ID
    if (isset($endpoint_parts[2])) {
        $id = $endpoint_parts[2];
    }
}

// Guardamos los valores en el endpoint_parts para mantener compatibilidad
$endpoint_parts = [];
$endpoint_parts[0] = $resource;
if ($action) {
    $endpoint_parts[1] = $action;
}
if ($id) {
    $endpoint_parts[2] = $id;
}

// Obtener el método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Obtener los datos enviados
$data = json_decode(file_get_contents('php://input'), true);
if ($data === null && $method !== 'GET' && $method !== 'DELETE') {
    $data = $_POST;
}

// Manejar las solicitudes según el recurso y el método
switch ($resource) {
    case 'cars':
        require_once 'resources/cars.php';
        break;
    
    case 'users':
        require_once 'resources/users.php';
        break;
    
    case 'login':
        require_once 'resources/auth.php';
        break;
    
    case 'reports':
        require_once 'resources/reports.php';
        break;
    
    default:
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Recurso no encontrado'
        ]);
        break;
} 