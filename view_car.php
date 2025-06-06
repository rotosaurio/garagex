<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si se proporcionó ID del carro
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ID de vehículo no proporcionado.";
    $_SESSION['alert_type'] = "danger";
    
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

$car_id = mysqli_real_escape_string($conn, $_GET['id']);
$is_admin = isset($_GET['admin']) && $_GET['admin'] == 1;

// Obtener información del carro
$sql = "SELECT c.*, u.nombre as nombre_usuario 
        FROM carros c 
        JOIN usuarios u ON c.id_usuario = u.id 
        WHERE c.id = '$car_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['message'] = "El vehículo no existe.";
    $_SESSION['alert_type'] = "danger";
    
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

$car = mysqli_fetch_assoc($result);

// Verificar si el usuario tiene permiso para ver este carro
if ($_SESSION['role'] !== 'admin' && $car['id_usuario'] != $_SESSION['user_id']) {
    $_SESSION['message'] = "No tienes permiso para ver este vehículo.";
    $_SESSION['alert_type'] = "danger";
    header("Location: dashboard.php");
    exit();
}

// Incluir header
include 'includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Detalles del Vehículo</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4 class="text-primary"><?php echo $car['marca'] . ' ' . $car['modelo']; ?></h4>
                            <p class="text-muted">Año: <?php echo $car['año']; ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <?php if ($car['kilometraje'] >= 10000): ?>
                                <div class="alert alert-maintenance mb-3">
                                    <i class="fas fa-exclamation-triangle"></i> ¡Necesita cambio de aceite!
                                </div>
                            <?php else: ?>
                                <span class="badge bg-success">Estado: OK</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h5>Información del Vehículo</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <th>ID:</th>
                                        <td><?php echo $car['id']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Marca:</th>
                                        <td><?php echo $car['marca']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Modelo:</th>
                                        <td><?php echo $car['modelo']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Año:</th>
                                        <td><?php echo $car['año']; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h5>Información de Mantenimiento</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Kilometraje:</th>
                                        <td><?php echo number_format($car['kilometraje'], 0, ',', '.'); ?> km</td>
                                    </tr>
                                    <tr>
                                        <th>Último cambio:</th>
                                        <td><?php echo date('d/m/Y H:i', strtotime($car['fecha_ultimo_cambio'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Estado:</th>
                                        <td>
                                            <?php if ($car['kilometraje'] >= 10000): ?>
                                                <span class="text-danger">Necesita cambio de aceite</span>
                                            <?php else: ?>
                                                <span class="text-success">Normal</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="mb-4">
                                <h5>Información del Propietario</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Propietario:</th>
                                        <td><?php echo $car['nombre_usuario']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>ID Usuario:</th>
                                        <td><?php echo $car['id_usuario']; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?php echo $is_admin ? 'admin_dashboard.php' : 'dashboard.php'; ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        
                        <div>
                            <a href="edit_car.php?id=<?php echo $car['id']; ?><?php echo $is_admin ? '&admin=1' : ''; ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a href="delete_car.php?id=<?php echo $car['id']; ?>" class="btn btn-danger delete-car">
                                <i class="fas fa-trash"></i> Eliminar
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 