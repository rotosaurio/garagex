// Script personalizado para GarageX

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Confirmación para eliminar carros
    const deleteButtons = document.querySelectorAll('.delete-car');
    if (deleteButtons) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('¿Estás seguro de que deseas eliminar este carro? Esta acción no se puede deshacer.')) {
                    e.preventDefault();
                }
            });
        });
    }

    // Validación de formularios
    const forms = document.querySelectorAll('.needs-validation');
    if (forms) {
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }

    // Actualizar automáticamente el kilometraje si hay un campo de kilometraje
    const kilometrajeInput = document.getElementById('kilometraje');
    if (kilometrajeInput) {
        kilometrajeInput.addEventListener('input', function() {
            const valor = parseInt(this.value);
            const alertaMantenimiento = document.getElementById('alerta-mantenimiento');
            
            if (valor >= 10000 && alertaMantenimiento) {
                alertaMantenimiento.classList.remove('d-none');
            } else if (alertaMantenimiento) {
                alertaMantenimiento.classList.add('d-none');
            }
        });
    }
}); 