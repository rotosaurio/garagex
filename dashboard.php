<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si el usuario es admin, redirigir al dashboard de admin
if ($_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

// Obtener los carros del usuario actual
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM carros WHERE id_usuario = '$user_id'";
$result = mysqli_query($conn, $sql);

// Verificar notificaciones de mantenimiento
$maintenance_sql = "SELECT * FROM carros WHERE id_usuario = '$user_id' AND kilometraje >= 10000 AND notificado = 0";
$maintenance_result = mysqli_query($conn, $maintenance_sql);

if (mysqli_num_rows($maintenance_result) > 0) {
    $cars_needing_maintenance = [];
    while ($maintenance_car = mysqli_fetch_assoc($maintenance_result)) {
        $cars_needing_maintenance[] = $maintenance_car['marca'] . ' ' . $maintenance_car['modelo'];
        
        // Actualizar el estado de notificación
        $car_id = $maintenance_car['id'];
        $update_notification = "UPDATE carros SET notificado = 1 WHERE id = '$car_id'";
        mysqli_query($conn, $update_notification);
    }
    
    // Crear mensaje de notificación
    $message = "¡Atención! Los siguientes vehículos necesitan un cambio de aceite: " . implode(", ", $cars_needing_maintenance);
    $_SESSION['message'] = $message;
    $_SESSION['alert_type'] = "warning";
}

// Incluir header
include 'includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Mis Vehículos</h2>
        <a href="add_car.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Agregar Vehículo
        </a>
    </div>
    
    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="row">
            <?php while ($car = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4">
                    <div class="card car-card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $car['marca'] . ' ' . $car['modelo']; ?></h5>
                            <p class="card-text">
                                <strong>Año:</strong> <?php echo $car['año']; ?><br>
                                <strong>Kilometraje:</strong> <?php echo number_format($car['kilometraje'], 0, ',', '.'); ?> km<br>
                                <strong>Último cambio:</strong> <?php echo date('d/m/Y', strtotime($car['fecha_ultimo_cambio'])); ?>
                            </p>
                            
                            <?php if ($car['kilometraje'] >= 10000): ?>
                                <div class="alert alert-maintenance mb-3" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i> ¡Es tiempo de cambiar el aceite!
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between">
                                <a href="edit_car.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="view_car.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i> Ver detalles
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <p>Aún no has registrado ningún vehículo. ¡Comienza agregando uno!</p>
            <a href="add_car.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Agregar Vehículo
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?> 