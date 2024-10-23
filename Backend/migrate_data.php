<?php
$originDb = $_POST['originDb'];
$originTable = $_POST['originTable'];
$destinationDb = $_POST['destinationDb'];
$destinationTable = $_POST['destinationTable'];

$ip = $_POST['ip'] ?? 'localhost';
$user = $_POST['user'] ?? 'root';
$password = $_POST['password'] ?? '';

$conn = new mysqli($ip, $user, $password, $originDb);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error conectando al origen.']);
    exit();
}

$result = $conn->query("SELECT * FROM $originTable");

$insertData = [];
while ($row = $result->fetch_assoc()) {
    $values = implode("','", array_values($row));
    $insertData[] = "('$values')";
}
$conn->close();

$conn = new mysqli($ip, $user, $password, $destinationDb);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error conectando al destino.']);
    exit();
}

$query = "INSERT INTO $destinationTable VALUES " . implode(',', $insertData);
if ($conn->query($query) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Migración exitosa.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error en la migración: ' . $conn->error]);
}
$conn->close();
?>
