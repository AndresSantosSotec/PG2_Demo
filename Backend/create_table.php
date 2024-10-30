<?php
header('Content-Type: application/json');
session_start(); // Asegúrate de iniciar la sesión

// Obtener los datos enviados desde el frontend
$data = json_decode(file_get_contents('php://input'), true);

$ip = $data['ip'] ?? '127.0.0.1';
$user = $data['user'] ?? 'root';
$password = $data['password'] ?? '';
$database = $data['database'] ?? '';
$sql = $data['sql'] ?? '';
$jsonData = $data['jsonData'] ?? [];
$tableName = $data['tableName'] ?? ''; 
$migracion_id = $data['migracionId'] ?? null;
$usuario_id = $_SESSION['usuario_id'] ?? null; // Obtener el usuario autenticado

if (empty($sql) || empty($database) || empty($tableName)) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos para la creación de la tabla.']);
    exit();
}

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$ip;dbname=$database;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ejecutar la creación de la tabla
    $pdo->exec($sql);
    registrarLog($migracion_id, "Tabla '$tableName' creada con éxito.");

    // Preparar la consulta de inserción
    $columns = array_keys($jsonData[0]);
    $columnsList = implode(', ', array_map(fn($col) => "`$col`", $columns));
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));

    $insertSQL = "INSERT INTO `$tableName` ($columnsList) VALUES ($placeholders)";
    $stmt = $pdo->prepare($insertSQL);

    $logs = [];

    // Realizar las inserciones y registrar logs
    foreach ($jsonData as $index => $row) {
        try {
            $stmt->execute(array_values($row));
            $mensaje = "Inserción #$index en la tabla '$tableName' realizada con éxito.";
            registrarLog($migracion_id, $mensaje);
            $logs[] = ['index' => $index, 'status' => 'success', 'message' => $mensaje];
        } catch (PDOException $e) {
            $mensaje = "Error en la inserción #$index: " . $e->getMessage();
            registrarLog($migracion_id, $mensaje);
            $logs[] = ['index' => $index, 'status' => 'error', 'message' => $mensaje];
        }
    }

    // Finalizar la migración para el usuario autenticado
    finalizarMigracion($usuario_id);

    echo json_encode(['success' => true, 'logs' => $logs]);
} catch (PDOException $e) {
    registrarLog($migracion_id, 'Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

// Función para registrar logs
function registrarLog($migracion_id, $mensaje) {
    if (!$migracion_id) return;

    try {
        $db = conectarBD();
        $stmt = $db->prepare('INSERT INTO logs_migraciones (migracion_id, mensaje, fecha) VALUES (?, ?, NOW())');
        $stmt->execute([$migracion_id, $mensaje]);
    } catch (PDOException $e) {
        error_log('Error al registrar log: ' . $e->getMessage());
    }
}

// Función para finalizar la última migración del usuario
function finalizarMigracion($usuario_id) {
    try {
        $db = conectarBD();

        // Actualizar la última migración en estado "En Proceso" para el usuario
        $stmt = $db->prepare(
            'UPDATE migraciones 
             SET estado = "Completada", fecha_fin = NOW() 
             WHERE user_id = ? AND estado = "En Proceso" 
             ORDER BY fecha_inicio DESC LIMIT 1'
        );
        $stmt->execute([$usuario_id]);

        if ($stmt->rowCount() > 0) {
            registrarLog($usuario_id, 'Migración completada exitosamente.');
        } else {
            registrarLog($usuario_id, 'No se encontró ninguna migración en proceso para completar.');
        }
    } catch (PDOException $e) {
        error_log('Error al finalizar migración: ' . $e->getMessage());
    }
}

// Función para conectar a la base de datos
function conectarBD() {
    $host = 'localhost';
    $dbname = 'migraciones_pg2';
    $user = 'root';
    $password = '';

    try {
        return new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    } catch (PDOException $e) {
        die('Error al conectar con la base de datos: ' . $e->getMessage());
    }
}
?>
