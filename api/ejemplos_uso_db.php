<?php
/* ============================================
   EJEMPLOS DE USO DE LA CLASE DB
   Clínica Veterinaria Alaska Pets Center
   ============================================ */

require_once 'config.php';

// ============================================
// EJEMPLO 1: CONSULTA SELECT SIMPLE
// ============================================

echo "<h2>Ejemplo 1: Obtener todos los usuarios</h2>";

$usuarios = $db->query("SELECT id, nombre, email FROM usuarios")->fetchAll();

echo "<pre>";
print_r($usuarios);
echo "</pre>";

// ============================================
// EJEMPLO 2: CONSULTA CON PARÁMETROS
// ============================================

echo "<h2>Ejemplo 2: Buscar usuario por email</h2>";

$email = 'prueba@clinicaalaska.cl';
$usuario = $db->query("SELECT * FROM usuarios WHERE email = ?", $email)->fetchOne();

echo "<pre>";
print_r($usuario);
echo "</pre>";

// ============================================
// EJEMPLO 3: INSERT
// ============================================

echo "<h2>Ejemplo 3: Insertar nuevo usuario</h2>";

$nombre = "Juan Pérez";
$email = "juan@ejemplo.cl";
$telefono = "+56912345678";
$password = password_hash("123456", PASSWORD_BCRYPT);

$db->query(
    "INSERT INTO usuarios (nombre, email, telefono, password, fecha_registro) VALUES (?, ?, ?, ?, NOW())",
    $nombre,
    $email,
    $telefono,
    $password
);

$nuevoId = $db->getInsertId();
echo "Usuario insertado con ID: " . $nuevoId;

// ============================================
// EJEMPLO 4: UPDATE
// ============================================

echo "<h2>Ejemplo 4: Actualizar usuario</h2>";

$nuevoNombre = "Juan Pérez Actualizado";
$usuarioId = 1;

$db->query(
    "UPDATE usuarios SET nombre = ? WHERE id = ?",
    $nuevoNombre,
    $usuarioId
);

echo "Filas afectadas: " . $db->affectedRows();

// ============================================
// EJEMPLO 5: DELETE
// ============================================

echo "<h2>Ejemplo 5: Eliminar usuario</h2>";

$usuarioId = 5;

$db->query("DELETE FROM usuarios WHERE id = ?", $usuarioId);

echo "Usuario eliminado. Filas afectadas: " . $db->affectedRows();

// ============================================
// EJEMPLO 6: VERIFICAR SI EXISTE
// ============================================

echo "<h2>Ejemplo 6: Verificar si existe un email</h2>";

$emailVerificar = "prueba@clinicaalaska.cl";

$db->query("SELECT id FROM usuarios WHERE email = ?", $emailVerificar);

if ($db->exists()) {
    echo "El email ya existe en la base de datos";
} else {
    echo "El email no existe";
}

// ============================================
// EJEMPLO 7: CONTAR REGISTROS
// ============================================

echo "<h2>Ejemplo 7: Contar usuarios</h2>";

$db->query("SELECT COUNT(*) as total FROM usuarios");
$resultado = $db->fetchOne();

echo "Total de usuarios: " . $resultado['total'];

// ============================================
// EJEMPLO 8: TRANSACCIONES
// ============================================

echo "<h2>Ejemplo 8: Usar transacciones</h2>";

try {
    // Iniciar transacción
    $db->beginTransaction();
    
    // Insertar usuario
    $db->query(
        "INSERT INTO usuarios (nombre, email, telefono, password) VALUES (?, ?, ?, ?)",
        "Usuario Trans",
        "trans@ejemplo.cl",
        "+56987654321",
        password_hash("123456", PASSWORD_BCRYPT)
    );
    
    $usuarioId = $db->getInsertId();
    
    // Insertar mascota para ese usuario
    $db->query(
        "INSERT INTO mascotas (usuario_id, nombre, especie, edad) VALUES (?, ?, ?, ?)",
        $usuarioId,
        "Firulais",
        "perro",
        3
    );
    
    // Si todo está bien, confirmar
    $db->commit();
    echo "Transacción completada exitosamente";
    
} catch (Exception $e) {
    // Si hay error, revertir
    $db->rollBack();
    echo "Error en la transacción: " . $e->getMessage();
}

// ============================================
// EJEMPLO 9: CONSULTA CON JOIN
// ============================================

echo "<h2>Ejemplo 9: Consulta con JOIN</h2>";

$sql = "SELECT u.nombre as usuario_nombre, m.nombre as mascota_nombre, m.especie 
        FROM usuarios u 
        INNER JOIN mascotas m ON u.id = m.usuario_id 
        ORDER BY u.nombre";

$resultados = $db->query($sql)->fetchAll();

echo "<pre>";
print_r($resultados);
echo "</pre>";

// ============================================
// EJEMPLO 10: MÚLTIPLES PARÁMETROS
// ============================================

echo "<h2>Ejemplo 10: Búsqueda con múltiples parámetros</h2>";

$nombreBuscar = "%Juan%";
$edadMin = 18;
$edadMax = 65;

$sql = "SELECT * FROM usuarios WHERE nombre LIKE ? LIMIT 10";
$resultados = $db->query($sql, $nombreBuscar)->fetchAll();

echo "<pre>";
print_r($resultados);
echo "</pre>";

// ============================================
// EJEMPLO 11: INFORMACIÓN DE LA CONEXIÓN
// ============================================

echo "<h2>Ejemplo 11: Información de la conexión</h2>";

$info = $db->getConnectionInfo();

echo "<pre>";
print_r($info);
echo "</pre>";

// ============================================
// EJEMPLO 12: CALLBACK EN FETCHALL
// ============================================

echo "<h2>Ejemplo 12: Usar callback en fetchAll</h2>";

$db->query("SELECT * FROM usuarios LIMIT 5");

echo "<ul>";
$db->fetchAll(function($row) {
    echo "<li>{$row['nombre']} - {$row['email']}</li>";
});
echo "</ul>";

// ============================================
// CERRAR CONEXIÓN
// ============================================

$db->close();

echo "<p><strong>Conexión cerrada</strong></p>";

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejemplos de Uso - Clase DB</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h2 {
            color: #D4A574;
            border-bottom: 2px solid #8B7355;
            padding-bottom: 10px;
            margin-top: 30px;
        }
        pre {
            background: #fff;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #D4A574;
            overflow-x: auto;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>📚 Ejemplos de Uso de la Clase DB</h1>
    <div class="info">
        <p><strong>Nota:</strong> Este archivo muestra ejemplos de cómo usar la clase DB.</p>
        <p>Para ver los ejemplos en acción, accede a: <code>http://localhost/clinicaakasjadef/api/ejemplos_uso_db.php</code></p>
    </div>
</body>
</html>

