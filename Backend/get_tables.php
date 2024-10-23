<?php
$db = $_POST['database'];
$ip = $_POST['ip'] ?? 'localhost';
$user = $_POST['user'] ?? 'root';
$password = $_POST['password'] ?? '';

$conn = new mysqli($ip, $user, $password, $db);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

$result = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

echo json_encode(['success' => true, 'tables' => $tables]);
$conn->close();
?>
