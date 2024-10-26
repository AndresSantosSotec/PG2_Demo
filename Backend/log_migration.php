<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

// Conectar a la base de datos
$data = json_decode(file_get_contents('php://input'), true);
$migrationId = $data['migrationId'] ?? null;
$message = $data['message'] ?? '';

if (!$migrationId || !$message) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit();
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=tu_base_de_datos', 'usuario', 'contraseña');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO logs_migraciones (migracion_id, mensaje, fecha) VALUES (?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$migrationId, $message]);

    echo json_encode(['success' => true, 'message' => 'Log registrado correctamente.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
