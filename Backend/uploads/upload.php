<?php
require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');
$response = [];

// Conectar a la base de datos
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

// Registrar logs de migración
function logMigracion($migracion_id, $mensaje) {
    $db = conectarBD();
    $stmt = $db->prepare('INSERT INTO logs_migraciones (migracion_id, mensaje, fecha) VALUES (?, ?, NOW())');
    $stmt->execute([$migracion_id, $mensaje]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        $response = ['success' => false, 'message' => 'Usuario no autenticado.'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit();
    }

    $usuario_id = $_SESSION['usuario_id'];

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

        if ($fileType === 'xlsx' || $fileType === 'xls') {
            $jsonData = excelToJson($fileTmpPath);

            $jsonFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.json';
            $jsonFilePath = '../uploads/' . $jsonFileName;

            if (file_put_contents($jsonFilePath, $jsonData)) {
                $db = conectarBD();

                // Registrar la migración con el estado "En Proceso"
                $stmt = $db->prepare(
                    'INSERT INTO migraciones (user_id, archivo_origen, estado, fecha_inicio) 
                     VALUES (?, ?, "En Proceso", NOW())'
                );
                $stmt->execute([$usuario_id, $fileName]);
                $migracion_id = $db->lastInsertId();

                // Registrar log inicial
                logMigracion($migracion_id, "Migración iniciada para el archivo: $fileName");

                if (descontarUsoApiKey($usuario_id)) {
                    // Completar la migración y actualizar estado y fecha de fin
                    finalizarMigracion($migracion_id);

                    $response = [
                        'success' => true,
                        'fileName' => $jsonFileName,
                        'fileUrl' => $jsonFilePath,
                        'jsonData' => $jsonData,
                        'migracionId' => $migracion_id,
                        'message' => 'Migración realizada y uso descontado.'
                    ];
                } else {
                    logMigracion($migracion_id, 'Error al descontar uso de API.');
                    $response = ['success' => false, 'message' => 'Error al descontar el uso de la API.'];
                }
            } else {
                $response = ['success' => false, 'message' => 'Error al guardar el archivo JSON en el servidor.'];
            }
        } else {
            $response = ['success' => false, 'message' => 'Por favor, sube un archivo Excel válido (xlsx o xls).'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Error al subir el archivo. Por favor, intenta nuevamente.'];
    }
} else {
    $response = ['success' => false, 'message' => 'Método de solicitud no válido.'];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

// Función para descontar uso de API Key
function descontarUsoApiKey($usuario_id) {
    $db = conectarBD();
    $stmt = $db->prepare(
        'UPDATE api_keys SET usos_restantes = usos_restantes - 1 
         WHERE user_id = :user_id AND usos_restantes > 0'
    );
    $stmt->bindParam(':user_id', $usuario_id, PDO::PARAM_INT);

    return $stmt->execute() && $stmt->rowCount() > 0;
}

// Función para finalizar la migración
function finalizarMigracion($migracion_id) {
    $db = conectarBD();
    $stmt = $db->prepare(
        'UPDATE migraciones SET estado = "En Proceso", fecha_fin = NOW() WHERE id = ?'
    );
    $stmt->execute([$migracion_id]);

    logMigracion($migracion_id, 'Migración completada exitosamente.');
}

// Función para convertir Excel a JSON
function excelToJson($filePath) {
    $spreadsheet = IOFactory::load($filePath);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    $header = array_shift($sheetData);
    $normalizedHeader = array_map(fn($col) => removeAccents(preg_replace('/\s+/', '_', trim($col))), $header);

    $formattedData = array_map(function ($row) use ($normalizedHeader) {
        return array_combine($normalizedHeader, $row);
    }, $sheetData);

    return json_encode($formattedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

// Función para remover acentos
function removeAccents($string) {
    $unwantedArray = [
        'á' => 'a', 'Á' => 'A', 'é' => 'e', 'É' => 'E',
        'í' => 'i', 'Í' => 'I', 'ó' => 'o', 'Ó' => 'O',
        'ú' => 'u', 'Ú' => 'U', 'ñ' => 'n', 'Ñ' => 'N',
        'ü' => 'u', 'Ü' => 'U'
    ];
    return strtr($string, $unwantedArray);
}
?>
