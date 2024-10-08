<?php
include 'conexion_bd.php'; // Conexión a la base de datos

function reducirUsosApi($apiKey, $conn) {
    // Primero, obtener el número de usos restantes
    $sql = "SELECT usos_restantes FROM api_keys WHERE api_key = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $result = $stmt->get_result();
    $apiData = $result->fetch_assoc();

    if ($apiData['usos_restantes'] > 0) {
        // Reducir el número de usos
        $nuevoUsosRestantes = $apiData['usos_restantes'] - 1;
        $sql = "UPDATE api_keys SET usos_restantes = ? WHERE api_key = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $nuevoUsosRestantes, $apiKey);
        $stmt->execute();
        return ['status' => 'success', 'usos_restantes' => $nuevoUsosRestantes];
    } else {
        throw new Exception('No te quedan usos disponibles.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiKey = $_POST['api_key'];
    try {
        $result = reducirUsosApi($apiKey, $conn);
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
