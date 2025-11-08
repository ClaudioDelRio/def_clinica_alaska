<?php
require_once __DIR__ . '/../config/configuracion.php';

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

// Obtener lista de clientes
$clientes = [];
try {
    $stmt = $pdo->query('SELECT id, nombre, email, rut, telefono, direccion, activo, fecha_registro, ultimo_acceso 
                         FROM ca_usuarios 
                         ORDER BY fecha_registro DESC');
    $clientes = $stmt->fetchAll();
} catch (Throwable $e) {
    error_log('Error al obtener clientes: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Clientes - Clínica Alaska</title>
    <base href="../">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    $pageActive = 'clientes';
    include __DIR__ . '/nav-panel.php'; 
    ?>
    
    <div class="main-content">
        <div class="panel-header">
            <h1><i class="fas fa-users"></i> Gestión de Clientes</h1>
            <button class="btn-primary" onclick="abrirModalCrearCliente()">
                <i class="fas fa-plus"></i> Nuevo Cliente
            </button>
        </div>

        <div class="search-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar por nombre, RUT o nombre de mascota..." autocomplete="off">
                <button class="search-clear" id="searchClear" onclick="limpiarBusqueda()" style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="search-results-info" id="searchResultsInfo" style="display: none;">
                <span id="searchCount"></span>
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>RUT</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Último Acceso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clientes)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 10px; display: block;"></i>
                                No hay clientes registrados
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr class="cliente-row" data-cliente-id="<?php echo $cliente['id']; ?>">
                                <td class="expand-toggle">
                                    <button class="toggle-btn" onclick="toggleMascotas(<?php echo $cliente['id']; ?>)">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </td>
                                <td><?php echo htmlspecialchars($cliente['id']); ?></td>
                                <td><strong><?php echo htmlspecialchars($cliente['nombre']); ?></strong></td>
                                <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['rut']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                                <td>
                                    <?php if ($cliente['activo']): ?>
                                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($cliente['ultimo_acceso']) {
                                        echo date('d/m/Y H:i', strtotime($cliente['ultimo_acceso']));
                                    } else {
                                        echo '<span style="color: #999;">Nunca</span>';
                                    }
                                    ?>
                                </td>
                                <td class="actions">
                                    <button class="btn-icon" title="Editar" onclick="editarCliente(<?php echo $cliente['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-icon" title="<?php echo $cliente['activo'] ? 'Inactivar' : 'Activar'; ?>" onclick="toggleCliente(<?php echo $cliente['id']; ?>)">
                                        <i class="<?php echo $cliente['activo'] ? 'fas fa-ban' : 'fas fa-check-circle'; ?>"></i>
                                    </button>
                                    <button class="btn-icon" title="Eliminar Definitivamente" onclick="eliminarClienteDefinitivo(<?php echo $cliente['id']; ?>)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            <!-- Fila desplegable para mascotas -->
                            <tr class="mascotas-row" id="mascotas-<?php echo $cliente['id']; ?>" style="display: none;">
                                <td colspan="9" class="mascotas-container">
                                    <div class="loading-spinner">
                                        <i class="fas fa-spinner fa-spin"></i> Cargando mascotas...
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para crear/editar cliente -->
    <div id="modalCliente" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h2 id="modalTitulo">Nuevo Cliente</h2>
                <button class="modal-close" onclick="cerrarModalCliente()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formCliente" class="modal-body">
                <input type="hidden" id="cliente_id" name="id">
                
                <div class="form-group">
                    <label for="nombre"><i class="fas fa-user"></i> Nombre Completo *</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="rut"><i class="fas fa-id-card"></i> RUT *</label>
                    <input type="text" id="rut" name="rut" required>
                </div>

                <div class="form-group">
                    <label for="telefono"><i class="fas fa-phone"></i> Teléfono *</label>
                    <input type="tel" id="telefono" name="telefono" required>
                </div>

                <div class="form-group">
                    <label for="direccion"><i class="fas fa-map-marker-alt"></i> Dirección *</label>
                    <textarea id="direccion" name="direccion" rows="2" required></textarea>
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
                    <button type="button" class="btn-secondary" onclick="cerrarModalCliente()">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmación para activar/inactivar cliente -->
    <div id="modalConfirmToggle" class="modal-confirm-overlay" style="display: none;">
        <div class="modal-confirm-container">
            <div class="modal-confirm-content">
                <div class="modal-confirm-icon" id="confirmIcon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h2 class="modal-confirm-title" id="confirmTitle">¿Está seguro?</h2>
                <p class="modal-confirm-message" id="confirmMessage">
                    Se va a inactivar este cliente
                </p>
                <p class="modal-confirm-medico" id="confirmNombre">
                    Cliente
                </p>
                <div class="modal-confirm-buttons">
                    <button class="modal-confirm-btn modal-confirm-btn-cancel" onclick="cerrarModalConfirm()">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button class="modal-confirm-btn modal-confirm-btn-confirm" id="confirmBtn" onclick="confirmarToggleCliente()">
                        <i class="fas fa-check"></i>
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de advertencia para eliminar cliente definitivamente -->
    <div id="modalConfirmDelete" class="modal-confirm-overlay" style="display: none;">
        <div class="modal-confirm-container">
            <div class="modal-confirm-content">
                <div class="modal-confirm-icon icon-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="modal-confirm-title">¿Eliminar Cliente Definitivamente?</h2>
                <p class="modal-confirm-message" id="deleteMessage">
                    Esta acción es irreversible. Se eliminarán también:
                </p>
                <div class="conteo-eliminacion" id="conteoEliminacion">
                    <!-- Se llenará dinámicamente -->
                </div>
                <p class="modal-confirm-medico" id="deleteNombre" style="color: #e74c3c; font-size: 1.2rem;">
                    Cliente
                </p>
                <div class="modal-confirm-buttons">
                    <button class="modal-confirm-btn modal-confirm-btn-cancel" onclick="cerrarModalDelete()">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button class="modal-confirm-btn modal-confirm-btn-delete" id="confirmDeleteBtn" onclick="confirmarEliminarCliente()">
                        <i class="fas fa-trash-alt"></i>
                        Eliminar Definitivamente
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/admin-panel.js"></script>
    <script src="assets/js/admin-clientes.js"></script>
</body>
</html>

