<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=mi_base_de_datos;charset=utf8', 'mi_usuario', 'mi_contraseña');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si hay usos disponibles
    $stmt = $pdo->prepare('SELECT usos_restantes FROM api_keys WHERE user_id = :user_id');
    $stmt->bindParam(':user_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $apiKey = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$apiKey || $apiKey['usos_restantes'] <= 0) {
        echo json_encode(['success' => false, 'message' => 'No quedan usos disponibles.']);
        exit();
    }

    // Restar un uso
    $stmt = $pdo->prepare('UPDATE api_keys SET usos_restantes = usos_restantes - 1 WHERE user_id = :user_id');
    $stmt->bindParam(':user_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Uso restado correctamente.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
