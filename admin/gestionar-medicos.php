<?php
require_once __DIR__ . '/../api/configuracion.php';

// Verificar si el médico está logueado
if (!isset($_SESSION['medico_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar si el médico es admin
if (!isset($_SESSION['medico_es_admin']) || !$_SESSION['medico_es_admin']) {
    header('Location: panel-admin.php');
    exit;
}

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
    <?php 
    $pageActive = 'usuarios';
    include __DIR__ . '/nav-panel.php'; 
    ?>
    
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
                                    <button class="btn-icon" title="<?php echo $medico['activo'] ? 'Inactivar' : 'Activar'; ?>" onclick="eliminarMedico(<?php echo $medico['id']; ?>)">
                                        <i class="<?php echo $medico['activo'] ? 'fas fa-ban' : 'fas fa-check-circle'; ?>"></i>
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

    <!-- Modal de confirmación para activar/inactivar médico -->
    <div id="modalConfirmToggle" class="modal-confirm-overlay" style="display: none;">
        <div class="modal-confirm-container">
            <div class="modal-confirm-content">
                <div class="modal-confirm-icon" id="confirmIcon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h2 class="modal-confirm-title" id="confirmTitle">¿Está seguro?</h2>
                <p class="modal-confirm-message" id="confirmMessage">
                    Se va a inactivar este médico
                </p>
                <p class="modal-confirm-medico" id="confirmMedico">
                    Dr. Juan Pérez
                </p>
                <div class="modal-confirm-buttons">
                    <button class="modal-confirm-btn modal-confirm-btn-cancel" onclick="cerrarModalConfirm()">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button class="modal-confirm-btn modal-confirm-btn-confirm" id="confirmBtn" onclick="confirmarToggleMedico()">
                        <i class="fas fa-check"></i>
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/admin-panel.js"></script>
    <script src="assets/js/admin-medicos.js"></script>
</body>
</html>

