<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si el usuario es admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Obtener todos los carros
$sql = "SELECT c.*, u.nombre as nombre_usuario 
        FROM carros c 
        JOIN usuarios u ON c.id_usuario = u.id 
        ORDER BY c.kilometraje DESC";
$result = mysqli_query($conn, $sql);

// Estadísticas
$total_users_sql = "SELECT COUNT(*) as total FROM usuarios WHERE role = 'usuario'";
$total_users_result = mysqli_query($conn, $total_users_sql);
$total_users = mysqli_fetch_assoc($total_users_result)['total'];

$total_cars_sql = "SELECT COUNT(*) as total FROM carros";
$total_cars_result = mysqli_query($conn, $total_cars_sql);
$total_cars = mysqli_fetch_assoc($total_cars_result)['total'];

$maintenance_needed_sql = "SELECT COUNT(*) as total FROM carros WHERE kilometraje >= 10000";
$maintenance_needed_result = mysqli_query($conn, $maintenance_needed_sql);
$maintenance_needed = mysqli_fetch_assoc($maintenance_needed_result)['total'];

// Incluir header
include 'includes/header.php';
?>

<div class="container py-4">
    <h2 class="mb-4">Panel de Administración</h2>
    
    <!-- Estadísticas -->
    <div class="row dashboard-stats">
        <div class="col-md-4">
            <div class="stats-card bg-primary text-white">
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3><?php echo $total_users; ?></h3>
                <p>Usuarios Registrados</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card bg-success text-white">
                <div class="stats-icon">
                    <i class="fas fa-car"></i>
                </div>
                <h3><?php echo $total_cars; ?></h3>
                <p>Vehículos Registrados</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card bg-warning text-dark">
                <div class="stats-icon">
                    <i class="fas fa-oil-can"></i>
                </div>
                <h3><?php echo $maintenance_needed; ?></h3>
                <p>Vehículos que Necesitan Mantenimiento</p>
            </div>
        </div>
    </div>
    
    <!-- Lista de Carros -->
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Todos los Vehículos</h3>
        </div>
        <div class="card-body">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Año</th>
                                <th>Kilometraje</th>
                                <th>Último Cambio</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($car = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $car['id']; ?></td>
                                    <td><?php echo $car['nombre_usuario']; ?></td>
                                    <td><?php echo $car['marca']; ?></td>
                                    <td><?php echo $car['modelo']; ?></td>
                                    <td><?php echo $car['año']; ?></td>
                                    <td><?php echo number_format($car['kilometraje'], 0, ',', '.'); ?> km</td>
                                    <td><?php echo date('d/m/Y', strtotime($car['fecha_ultimo_cambio'])); ?></td>
                                    <td>
                                        <?php if ($car['kilometraje'] >= 10000): ?>
                                            <span class="badge bg-danger">Cambio de Aceite</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">OK</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="edit_car.php?id=<?php echo $car['id']; ?>&admin=1" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="view_car.php?id=<?php echo $car['id']; ?>&admin=1" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="delete_car.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-danger delete-car" data-bs-toggle="tooltip" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info" role="alert">
                    No hay vehículos registrados en el sistema.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 