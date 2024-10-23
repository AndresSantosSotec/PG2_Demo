<?php
header('Content-Type: application/json');

// Obtener los datos enviados desde el frontend
$data = json_decode(file_get_contents('php://input'), true);

$ip = $data['ip'] ?? '127.0.0.1';
$user = $data['user'] ?? 'root';
$password = $data['password'] ?? '';
$database = $data['database'] ?? '';
$sql = $data['sql'] ?? '';
$jsonData = $data['jsonData'] ?? [];
$tableName = $data['tableName'] ?? '';  // Aseguramos que se reciba el nombre de la tabla

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

    // Preparar la consulta de inserción
    $columns = array_keys($jsonData[0]);
    $columnsList = implode(', ', array_map(fn($col) => "`$col`", $columns));
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));

    $insertSQL = "INSERT INTO `$tableName` ($columnsList) VALUES ($placeholders)";
    $stmt = $pdo->prepare($insertSQL);

    // Insertar cada fila del JSON
    foreach ($jsonData as $row) {
        $stmt->execute(array_values($row));
    }

    echo json_encode(['success' => true, 'message' => 'Tabla creada e inserción exitosa.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
