<?php
// Configuración de la base de datos
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'garagex_db');

// Conexión a la base de datos
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Verificar conexión
if (!$conn) {
    die("ERROR: No se pudo conectar. " . mysqli_connect_error());
}

// Crear base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (mysqli_query($conn, $sql)) {
    // Seleccionar la base de datos
    mysqli_select_db($conn, DB_NAME);
    
    // Crear tabla de usuarios si no existe
    $sql_users = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($conn, $sql_users)) {
        echo "ERROR: No se pudo ejecutar $sql_users. " . mysqli_error($conn);
    }
    
    // Crear tabla de carros si no existe
    $sql_cars = "CREATE TABLE IF NOT EXISTS carros (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        id_usuario INT(11) NOT NULL,
        marca VARCHAR(50) NOT NULL,
        modelo VARCHAR(50) NOT NULL,
        año INT(4) NOT NULL,
        kilometraje INT(11) NOT NULL,
        fecha_ultimo_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        notificado BOOLEAN DEFAULT 0,
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
    )";
    
    if (!mysqli_query($conn, $sql_cars)) {
        echo "ERROR: No se pudo ejecutar $sql_cars. " . mysqli_error($conn);
    }

    // Verificar si existe usuario admin, si no, crearlo
    $check_admin = "SELECT * FROM usuarios WHERE role='admin' LIMIT 1";
    $result = mysqli_query($conn, $check_admin);
    
    if (mysqli_num_rows($result) == 0) {
        // Crear usuario admin por defecto
        $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
        $create_admin = "INSERT INTO usuarios (nombre, email, password, role) VALUES ('Administrador', 'admin@garagex.com', '$admin_password', 'admin')";
        
        if (!mysqli_query($conn, $create_admin)) {
            echo "ERROR: No se pudo crear el usuario administrador. " . mysqli_error($conn);
        }
    }
} else {
    echo "ERROR: No se pudo crear la base de datos. " . mysqli_error($conn);
}
?> 