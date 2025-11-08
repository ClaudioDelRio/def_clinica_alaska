<?php
/* ============================================
   ARCHIVO DE PRUEBA DE CONEXIÓN
   Ejecuta este archivo para ver el error exacto
   ============================================ */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Probando Conexión a Base de Datos</h2>";
echo "<hr>";

// Datos de conexión
$host = 'cldelrio.laboratoriodiseno.cl';
$dbname = 'cldelriolaborato_c_alaska';
$user = 'cldelriolaborato_c_delriom';
$pass = 'vO)8Yx7[I-4~BXVo';
$charset = 'utf8mb4';

echo "<h3>📋 Datos de Conexión:</h3>";
echo "<ul>";
echo "<li><strong>Host:</strong> $host</li>";
echo "<li><strong>Database:</strong> $dbname</li>";
echo "<li><strong>User:</strong> $user</li>";
echo "<li><strong>Password:</strong> " . str_repeat('*', strlen($pass)) . "</li>";
echo "<li><strong>Charset:</strong> $charset</li>";
echo "</ul>";
echo "<hr>";

// ============================================
// PRUEBA 1: MySQLi
// ============================================
echo "<h3>🔌 Prueba 1: Conexión MySQLi</h3>";
try {
    $mysqli = new mysqli($host, $user, $pass, $dbname);
    
    if ($mysqli->connect_error) {
        echo "❌ <strong style='color:red;'>Error MySQLi:</strong> " . $mysqli->connect_error . "<br>";
        echo "<strong>Código de error:</strong> " . $mysqli->connect_errno . "<br>";
    } else {
        echo "✅ <strong style='color:green;'>MySQLi conectado exitosamente</strong><br>";
        echo "<strong>Versión del servidor:</strong> " . $mysqli->server_info . "<br>";
        echo "<strong>Host info:</strong> " . $mysqli->host_info . "<br>";
        
        // Probar una consulta simple
        $result = $mysqli->query("SELECT DATABASE() as db_name");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<strong>Base de datos actual:</strong> " . $row['db_name'] . "<br>";
        }
        
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "❌ <strong style='color:red;'>Excepción MySQLi:</strong> " . $e->getMessage() . "<br>";
}
echo "<hr>";

// ============================================
// PRUEBA 2: PDO
// ============================================
echo "<h3>🔌 Prueba 2: Conexión PDO</h3>";
try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    echo "✅ <strong style='color:green;'>PDO conectado exitosamente</strong><br>";
    echo "<strong>Driver:</strong> " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "<br>";
    echo "<strong>Versión del servidor:</strong> " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "<br>";
    
    // Probar una consulta simple
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $row = $stmt->fetch();
    echo "<strong>Base de datos actual:</strong> " . $row['db_name'] . "<br>";
    
} catch (PDOException $e) {
    echo "❌ <strong style='color:red;'>Error PDO:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Código de error:</strong> " . $e->getCode() . "<br>";
}
echo "<hr>";

// ============================================
// PRUEBA 3: Clase DB
// ============================================
echo "<h3>🔌 Prueba 3: Clase DB Custom</h3>";
try {
    require_once __DIR__ . '/../config/db.php';
    $db = new DB($host, $user, $pass, $dbname, $charset);
    
    echo "✅ <strong style='color:green;'>Clase DB conectada exitosamente</strong><br>";
    
    $info = $db->getConnectionInfo();
    echo "<strong>Host info:</strong> " . $info['host'] . "<br>";
    echo "<strong>Versión del servidor:</strong> " . $info['server_version'] . "<br>";
    echo "<strong>Charset:</strong> " . $info['character_set'] . "<br>";
    
    $db->close();
    
} catch (Exception $e) {
    echo "❌ <strong style='color:red;'>Error Clase DB:</strong> " . $e->getMessage() . "<br>";
}
echo "<hr>";

// ============================================
// PRUEBA 4: Verificar tablas
// ============================================
echo "<h3>📊 Prueba 4: Verificar Tablas</h3>";
try {
    $mysqli = new mysqli($host, $user, $pass, $dbname);
    
    if (!$mysqli->connect_error) {
        $result = $mysqli->query("SHOW TABLES");
        
        if ($result) {
            echo "✅ <strong style='color:green;'>Tablas encontradas:</strong><br>";
            echo "<ul>";
            while ($row = $result->fetch_array()) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
            
            // Verificar tabla ca_usuarios
            $result = $mysqli->query("SELECT COUNT(*) as total FROM ca_usuarios");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "<strong>Total de usuarios:</strong> " . $row['total'] . "<br>";
            }
        }
        
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "❌ <strong style='color:red;'>Error al verificar tablas:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><strong>✅ Prueba completada</strong></p>";
echo "<p><em>Si ves algún error, copia el mensaje completo para poder ayudarte.</em></p>";
?>

