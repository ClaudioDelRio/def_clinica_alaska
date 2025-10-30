<?php
require_once __DIR__ . '/../api/configuracion.php';

// Solo permitir acceso si usuario está logueado y es médico administrador
if (!estaLogueado()) {
    header('Location: ../index.html');
    exit;
}

$usuario = obtenerUsuarioActual(); // Asumiendo helper en configuracion.php

// Verificar si el usuario corresponde a un médico con rol admin
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Clínica Alaska</title>
    <base href="../">
    <link rel="stylesheet" href="./assets/css/estilos.css">
</head>
<body>
    <div style="max-width:1200px;margin:40px auto;padding:20px;background:#fff;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,.1);font-family:'Poppins',sans-serif;">
        <h1 style="margin:0 0 10px;">Panel de Administración</h1>
        <p style="color:#666;margin:0 0 20px;">Bienvenido, <?php echo htmlspecialchars($medico['nombre'] ?? ''); ?>.</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="usuarios/panel-usuario.php" class="btn-secondary" style="text-decoration:none;display:inline-block;padding:10px 16px;border-radius:10px;background:#f5f5f5;color:#666;font-weight:600;">Volver al panel de usuario</a>
        </div>
    </div>
</body>
<script>
// Aquí montaremos el panel admin real más adelante
</script>
</html>


