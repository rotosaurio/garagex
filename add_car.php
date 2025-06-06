<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marca = mysqli_real_escape_string($conn, $_POST['marca']);
    $modelo = mysqli_real_escape_string($conn, $_POST['modelo']);
    $año = mysqli_real_escape_string($conn, $_POST['año']);
    $kilometraje = mysqli_real_escape_string($conn, $_POST['kilometraje']);
    $id_usuario = $_SESSION['user_id'];
    
    // Validar campos
    if (empty($marca) || empty($modelo) || empty($año) || empty($kilometraje)) {
        $_SESSION['message'] = "Por favor, completa todos los campos.";
        $_SESSION['alert_type'] = "danger";
    } else {
        // Insertar nuevo carro
        $sql = "INSERT INTO carros (id_usuario, marca, modelo, año, kilometraje) 
                VALUES ('$id_usuario', '$marca', '$modelo', '$año', '$kilometraje')";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Vehículo agregado correctamente.";
            $_SESSION['alert_type'] = "success";
            
            // Verificar si es necesario mostrar alerta de mantenimiento
            if ($kilometraje >= 10000) {
                $_SESSION['maintenance_alert'] = "Tu vehículo $marca $modelo necesita un cambio de aceite.";
            }
            
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['message'] = "Error al agregar el vehículo: " . mysqli_error($conn);
            $_SESSION['alert_type'] = "danger";
        }
    }
}

// Incluir header
include 'includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Agregar Nuevo Vehículo</h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="marca" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="marca" name="marca" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa la marca del vehículo.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="modelo" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="modelo" name="modelo" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa el modelo del vehículo.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="año" class="form-label">Año</label>
                            <input type="number" class="form-control" id="año" name="año" min="1900" max="<?php echo date("Y") + 1; ?>" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa un año válido.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="kilometraje" class="form-label">Kilometraje</label>
                            <input type="number" class="form-control" id="kilometraje" name="kilometraje" min="0" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa el kilometraje actual.
                            </div>
                        </div>
                        
                        <div class="alert alert-maintenance mb-3 d-none" id="alerta-mantenimiento">
                            <i class="fas fa-exclamation-triangle"></i> Vehículo con kilometraje superior a 10,000 km. Se recomienda cambio de aceite.
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Guardar Vehículo</button>
                            <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 