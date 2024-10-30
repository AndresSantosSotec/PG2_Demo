<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$migracion_id = $data['migracionId'] ?? null;
$mensaje = $data['mensaje'] ?? '';

if (!$migracion_id || !$mensaje) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos para el log.']);
    exit();
}

try {
    $db = conectarBD();
    $stmt = $db->prepare('INSERT INTO logs_migraciones (migracion_id, mensaje, fecha) VALUES (?, ?, NOW())');
    $stmt->execute([$migracion_id, $mensaje]);

    echo json_encode(['success' => true, 'message' => 'Log registrado con éxito.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

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
