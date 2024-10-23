<?php
header('Content-Type: application/json');

// Decodificar los datos recibidos desde el cliente
$data = json_decode(file_get_contents('php://input'), true);
$ip = $data['ip'] ?? '127.0.0.1';
$user = $data['user'] ?? 'root';
$password = $data['password'] ?? '';
$database = $data['database'] ?? null;

try {
    // Conexión PDO
    $dsn = $database ? "mysql:host=$ip;dbname=$database" : "mysql:host=$ip";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($database) {
        // Obtener las tablas si se proporciona una base de datos específica
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo json_encode(['success' => true, 'tables' => $tables]);
    } else {
        // Obtener todas las bases de datos si no se proporciona una específica
        $stmt = $pdo->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo json_encode(['success' => true, 'databases' => $databases]);
    }
} catch (PDOException $e) {
    // Enviar mensaje de error detallado al cliente
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
}
?>
