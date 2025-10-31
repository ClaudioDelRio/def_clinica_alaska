<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../api/configuracion.php';

// TEMPORALMENTE: Sin restricciones de login mientras desarrollamos
/*
if (!estaLogueado()) {
    header('Location: ../index.html');
    exit;
}

$usuario = obtenerUsuarioActual();
try {
    $stmt = $pdo->prepare('SELECT id, nombre, es_admin FROM ca_medicos WHERE usuario_id = :uid AND activo = 1');
    $stmt->execute(['uid' => $usuario['id']]);
    $medico = $stmt->fetch();
    if (!$medico || (int)$medico['es_admin'] !== 1) {
        header('Location: ../index.html');
        exit;
    }
} catch (Throwable $e) {
    header('Location: ../index.html');
    exit;
}
*/

// Obtener lista de médicos
$medicos = [];
try {
    $stmt = $pdo->query('SELECT id, nombre, especialidad, telefono, email, es_admin, activo, fecha_registro 
                         FROM ca_medicos 
                         ORDER BY fecha_registro DESC');
    $medicos = $stmt->fetchAll();
} catch (Throwable $e) {
    error_log('Error al obtener médicos: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Médicos - Clínica Alaska</title>
    <base href="../">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-paw"></i> Admin Panel
        </div>
        <a href="./admin/panel-admin.php" style="text-decoration: none; color: inherit; display: block;">
            <div class="menu-item">
                <i class="fas fa-chart-line"></i> Inicio Panel
            </div>
        </a>
        <div class="menu-item active">
            <i class="fas fa-users"></i> Usuarios
        </div>
        <div class="menu-item">
            <i class="fas fa-paw"></i> Mascotas
        </div>
        <div class="menu-item">
            <i class="fas fa-calendar-check"></i> Citas
        </div>
        <div class="menu-item">
            <i class="fas fa-chart-bar"></i> Reportes
        </div>
        <div class="menu-item">
            <i class="fas fa-cog"></i> Configuración
        </div>
        <a href="../index.html" style="text-decoration: none; color: inherit; display: block;">
            <div class="menu-item" style="margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                <i class="fas fa-home"></i> Volver al inicio
            </div>
        </a>
    </div>
    
    <div class="main-content">
        <div class="panel-header">
            <h1><i class="fas fa-user-md"></i> Gestión de Médicos</h1>
            <button class="btn-primary" onclick="abrirModalCrearMedico()">
                <i class="fas fa-plus"></i> Nuevo Médico
            </button>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Especialidad</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Es Admin</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($medicos)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 10px; display: block;"></i>
                                No hay médicos registrados
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($medicos as $medico): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($medico['id']); ?></td>
                                <td><strong><?php echo htmlspecialchars($medico['nombre']); ?></strong></td>
                                <td><?php echo htmlspecialchars($medico['especialidad'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($medico['telefono'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($medico['email'] ?? '-'); ?></td>
                                <td>
                                    <?php if ($medico['es_admin']): ?>
                                        <span class="badge badge-admin"><i class="fas fa-shield-alt"></i> Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-normal"><i class="fas fa-user"></i> Usuario</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($medico['activo']): ?>
                                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($medico['fecha_registro'])); ?></td>
                                <td class="actions">
                                    <button class="btn-icon" title="Editar" onclick="editarMedico(<?php echo $medico['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-icon" title="Eliminar" onclick="eliminarMedico(<?php echo $medico['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para crear/editar médico -->
    <div id="modalMedico" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h2 id="modalTitulo">Nuevo Médico</h2>
                <button class="modal-close" onclick="cerrarModalMedico()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formMedico" class="modal-body">
                <input type="hidden" id="medico_id" name="id">
                
                <div class="form-group">
                    <label for="nombre"><i class="fas fa-user"></i> Nombre Completo *</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="especialidad"><i class="fas fa-stethoscope"></i> Especialidad</label>
                    <input type="text" id="especialidad" name="especialidad">
                </div>

                <div class="form-group">
                    <label for="telefono"><i class="fas fa-phone"></i> Teléfono</label>
                    <input type="tel" id="telefono" name="telefono">
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email">
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" id="es_admin" name="es_admin"> 
                        <span>Es Administrador</span>
                    </label>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Contraseña <?php echo '<span id="password-label">*</span>'; ?></label>
                    <input type="password" id="password" name="password">
                </div>

                <div class="form-group">
                    <label for="activo">Estado</label>
                    <select id="activo" name="activo">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="cerrarModalMedico()">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/admin-panel.js"></script>
    <script>
        // Funciones del modal
        function abrirModalCrearMedico() {
            document.getElementById('modalTitulo').textContent = 'Nuevo Médico';
            document.getElementById('formMedico').reset();
            document.getElementById('medico_id').value = '';
            document.getElementById('password').required = true;
            document.getElementById('password-label').textContent = '*';
            document.getElementById('modalMedico').style.display = 'flex';
        }

        function cerrarModalMedico() {
            document.getElementById('modalMedico').style.display = 'none';
        }

        function editarMedico(id) {
            // Aquí cargarías los datos del médico vía AJAX
            alert('Funcionalidad de edición en desarrollo');
        }

        function eliminarMedico(id) {
            if (confirm('¿Está seguro de eliminar este médico?')) {
                // Aquí implementarías la eliminación vía AJAX
                alert('Funcionalidad de eliminación en desarrollo');
            }
        }

        // Manejar envío del formulario
        document.getElementById('formMedico').addEventListener('submit', function(e) {
            e.preventDefault();
            // Aquí implementarías el guardado vía AJAX
            alert('Funcionalidad de guardado en desarrollo');
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('modalMedico').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModalMedico();
            }
        });
    </script>
</body>
</html>

