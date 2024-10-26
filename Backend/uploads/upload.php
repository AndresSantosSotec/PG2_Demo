<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

        if ($fileType === 'xlsx' || $fileType === 'xls') {
            $jsonData = excelToJson($fileTmpPath);

            $jsonFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.json';
            $jsonFilePath = '../uploads/' . $jsonFileName;

            if (file_put_contents($jsonFilePath, $jsonData)) {
                $response = [
                    'success' => true,
                    'fileName' => $jsonFileName,
                    'fileUrl' => $jsonFilePath,
                    'jsonData' => $jsonData
                ];
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

function excelToJson($filePath) {
    $spreadsheet = IOFactory::load($filePath);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    $header = array_shift($sheetData);

    $normalizedHeader = array_map(function ($columnName) {
        return removeAccents(preg_replace('/\s+/', '_', trim($columnName)));
    }, $header);

    $formattedData = [];
    foreach ($sheetData as $row) {
        $formattedRow = [];
        foreach ($normalizedHeader as $columnKey => $normalizedColumnName) {
            $value = $row[$columnKey] ?? '';

            // Ignorar el ID (será autoincrementado en la base de datos)
            if (preg_match('/^id_/i', $normalizedColumnName) || strtolower($normalizedColumnName) === 'id') {
                continue; // Omitir este campo del JSON
            }

            // Validar si es un campo de fecha y convertir a formato DD-MM-YYYY
            if (isDateField($normalizedColumnName) && validateDate($value)) {
                $value = convertToDMY($value);
            }

            // Detectar códigos alfanuméricos
            if (isCodeField($normalizedColumnName)) {
                $value = (string) $value;
            }

            // Procesar números con comas correctamente
            if (is_string($value) && preg_match('/^-?\d{1,3}(,\d{3})*(\.\d+)?\s*$/', $value)) {
                $value = str_replace(',', '', trim($value));
            }

            // Convertir solo valores numéricos
            if (is_numeric($value)) {
                $value = number_format((float) $value, 2, '.', '');
            }

            $formattedRow[$normalizedColumnName] = $value;
        }
        $formattedData[] = $formattedRow;
    }

    $json = json_encode($formattedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return str_replace('\/', '/', $json);
}

// Validar si el campo es relacionado con fechas
function isDateField($fieldName) {
    return preg_match('/fecha|date|nacimiento|alta|creacion|modificacion/i', $fieldName);
}

// Convertir fecha al formato DD-MM-YYYY
function convertToDMY($date) {
    $timestamp = strtotime($date);
    return date('d-m-Y', $timestamp);
}

// Validar si la fecha está en formato aceptado (YYYY-MM-DD)
function validateDate($date) {
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && strtotime($date) !== false;
}

// Detectar si el campo es un código alfanumérico
function isCodeField($fieldName) {
    return preg_match('/codigo|code|pedido/i', $fieldName);
}

// Remover acentos de los nombres de columnas
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
