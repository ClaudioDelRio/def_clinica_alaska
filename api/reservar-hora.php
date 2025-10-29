<?php
/* ============================================
   ENDPOINT: RESERVAR HORA
   Clínica Veterinaria Alaska Pets Center
   ============================================ */

require_once 'configuracion.php';

// Configurar headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Solo acepta peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    enviarError('Método no permitido');
}

// Verificar que el usuario esté logueado
if (!estaLogueado()) {
    enviarError('Debe iniciar sesión primero');
}

$usuarioId = obtenerUsuarioId();

// Obtener los datos del formulario
$datos = json_decode(file_get_contents('php://input'), true);

if (!$datos) {
    enviarError('No se recibieron datos');
}

// Extraer y limpiar los datos
$mascotaId = isset($datos['mascota_id']) ? (int)$datos['mascota_id'] : 0;
$doctorId = isset($datos['doctor_id']) ? (int)$datos['doctor_id'] : null; // Puede ser NULL
$servicio = isset($datos['servicio']) ? limpiarInput($datos['servicio']) : '';
$fechaCita = isset($datos['fecha_cita']) ? $datos['fecha_cita'] : '';
$horaCita = isset($datos['hora_cita']) ? $datos['hora_cita'] : '';
$motivo = isset($datos['motivo']) ? limpiarInput($datos['motivo']) : '';

/* ============================================
   VALIDACIONES
   ============================================ */

$errores = [];

// Validar mascota
if ($mascotaId <= 0) {
    $errores[] = 'Debe seleccionar una mascota';
} else {
    // Verificar que la mascota pertenezca al usuario
    try {
        $sqlVerificar = "SELECT id, nombre FROM ca_mascotas WHERE id = :id AND usuario_id = :usuario_id";
        $stmtVerificar = $pdo->prepare($sqlVerificar);
        $stmtVerificar->execute([
            'id' => $mascotaId,
            'usuario_id' => $usuarioId
        ]);
        
        $mascota = $stmtVerificar->fetch();
        
        if (!$mascota) {
            $errores[] = 'La mascota seleccionada no existe o no te pertenece';
        }
    } catch (PDOException $e) {
        error_log('Error verificando mascota: ' . $e->getMessage());
        $errores[] = 'Error al verificar la mascota';
    }
}

// Validar servicio
if (empty($servicio)) {
    $errores[] = 'Debe seleccionar un tipo de servicio';
} elseif (!array_key_exists($servicio, SERVICIOS)) {
    $errores[] = 'El servicio seleccionado no es válido';
}

// Validar doctor (si se seleccionó uno)
if ($doctorId !== null && $doctorId > 0) {
    try {
        $sqlVerificarDoctor = "SELECT id, nombre FROM ca_medicos WHERE id = :id AND activo = 1";
        $stmtVerificarDoctor = $pdo->prepare($sqlVerificarDoctor);
        $stmtVerificarDoctor->execute(['id' => $doctorId]);
        
        $doctor = $stmtVerificarDoctor->fetch();
        
        if (!$doctor) {
            $errores[] = 'El doctor seleccionado no está disponible';
        }
    } catch (PDOException $e) {
        error_log('Error verificando doctor: ' . $e->getMessage());
        $errores[] = 'Error al verificar el doctor';
    }
}

// Validar fecha
if (empty($fechaCita)) {
    $errores[] = 'La fecha es obligatoria';
} else {
    $fechaActual = date('Y-m-d');
    if ($fechaCita < $fechaActual) {
        $errores[] = 'La fecha no puede ser anterior a hoy';
    }
    
    // Validar que la fecha no sea un domingo
    $diaSemana = date('N', strtotime($fechaCita));
    if ($diaSemana == 7) {
        $errores[] = 'No atendemos los domingos';
    }
}

// Validar hora
if (empty($horaCita)) {
    $errores[] = 'La hora es obligatoria';
} elseif (!in_array($horaCita, HORARIOS)) {
    $errores[] = 'La hora seleccionada no está disponible';
}

// Validar motivo
if (empty($motivo)) {
    $errores[] = 'El motivo de la consulta es obligatorio';
} elseif (strlen($motivo) < 10) {
    $errores[] = 'El motivo debe tener al menos 10 caracteres';
} elseif (strlen($motivo) > 500) {
    $errores[] = 'El motivo no puede exceder los 500 caracteres';
}

// Si hay errores, devolverlos
if (!empty($errores)) {
    enviarError(implode('. ', $errores));
}

/* ============================================
   VERIFICAR DISPONIBILIDAD
   IMPORTANTE: Solo UNA cita por horario (consultorio compartido)
   ============================================ */

try {
    $sqlDisponibilidad = "SELECT COUNT(*) as total 
                          FROM ca_citas 
                          WHERE fecha_cita = :fecha 
                          AND hora_cita = :hora 
                          AND estado IN ('pendiente', 'confirmada')";
    $stmtDisponibilidad = $pdo->prepare($sqlDisponibilidad);
    $stmtDisponibilidad->execute([
        'fecha' => $fechaCita,
        'hora' => $horaCita
    ]);
    
    $citasEnHorario = $stmtDisponibilidad->fetch()['total'];
    
    // Solo puede haber UNA cita por horario (consultorio compartido entre doctores)
    if ($citasEnHorario >= 1) {
        enviarError('Este horario ya no está disponible. Por favor, selecciona otro horario.');
    }
    
} catch (PDOException $e) {
    error_log('Error verificando disponibilidad: ' . $e->getMessage());
    enviarError('Error al verificar disponibilidad');
}

/* ============================================
   CREAR CITA
   ============================================ */

try {
    $sql = "INSERT INTO ca_citas 
            (usuario_id, mascota_id, doctor_id, servicio, fecha_cita, hora_cita, motivo, estado, fecha_creacion) 
            VALUES 
            (:usuario_id, :mascota_id, :doctor_id, :servicio, :fecha_cita, :hora_cita, :motivo, 'pendiente', NOW())";
    
    $stmt = $pdo->prepare($sql);
    $resultado = $stmt->execute([
        'usuario_id' => $usuarioId,
        'mascota_id' => $mascotaId,
        'doctor_id' => $doctorId, // Puede ser NULL si no eligió doctor
        'servicio' => $servicio,
        'fecha_cita' => $fechaCita,
        'hora_cita' => $horaCita,
        'motivo' => $motivo
    ]);
    
    if ($resultado) {
        $citaId = $pdo->lastInsertId();
        
        $respuestaData = [
            'cita_id' => $citaId,
            'mascota' => $mascota['nombre'],
            'servicio' => SERVICIOS[$servicio],
            'fecha' => formatearFecha($fechaCita),
            'hora' => $horaCita
        ];
        
        // Agregar nombre del doctor si se seleccionó uno
        if (isset($doctor)) {
            $respuestaData['doctor'] = $doctor['nombre'];
        }
        
        enviarExito('¡Hora reservada exitosamente!', $respuestaData);
    } else {
        enviarError('Error al reservar la hora');
    }
    
} catch (PDOException $e) {
    error_log('Error en reservar-hora.php: ' . $e->getMessage());
    enviarError('Error al reservar la hora');
}

