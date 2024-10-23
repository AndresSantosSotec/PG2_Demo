<?php
// Cargar PhpSpreadsheet (asegúrate de que autoload.php esté en la ubicación correcta)
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

// Iniciamos un array para almacenar la respuesta
$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificamos si el archivo fue enviado y si no hubo errores en la subida
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Obtenemos la ruta temporal del archivo
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

        // Verificamos si el archivo es Excel o JSON
        if ($fileType === 'xlsx' || $fileType === 'xls') {
            // Llamamos a la función para convertir el archivo Excel a JSON
            $jsonData = excelToJson($fileTmpPath);

            // Generamos el nombre del archivo JSON a partir del nombre original del archivo Excel
            $jsonFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.json';
            $jsonFilePath = '../uploads/' . $jsonFileName;

            // Guardamos el archivo JSON generado en el servidor
            if (file_put_contents($jsonFilePath, $jsonData)) {
                // Si se guarda correctamente, preparamos la respuesta
                $response = [
                    'success' => true,
                    'fileName' => $jsonFileName,
                    'fileUrl' => $jsonFilePath,
                    'jsonData' => $jsonData // Enviamos los datos JSON generados para mostrarlos
                ];
            } else {
                // Si ocurre un error al guardar el archivo JSON
                $response = [
                    'success' => false,
                    'message' => 'Error al guardar el archivo JSON en el servidor.'
                ];
            }
        } else {
            // Si el archivo no es un archivo Excel válido
            $response = [
                'success' => false,
                'message' => 'Por favor, sube un archivo Excel válido (xlsx o xls).'
            ];
        }
    } else {
        // Si ocurre un error al subir el archivo
        $response = [
            'success' => false,
            'message' => 'Error al subir el archivo. Por favor, intenta nuevamente.'
        ];
    }
} else {
    // Si la solicitud no es POST
    $response = [
        'success' => false,
        'message' => 'Método de solicitud no válido.'
    ];
}

// Enviamos la respuesta en formato JSON
echo json_encode($response, JSON_UNESCAPED_UNICODE);  // Asegura que se manejen correctamente los caracteres especiales

// Función para convertir un archivo Excel a formato JSON
function excelToJson($filePath) {
    // Cargamos el archivo Excel utilizando PhpSpreadsheet
    $spreadsheet = IOFactory::load($filePath);
    
    // Convertimos el contenido de la hoja de cálculo activa a un array
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    // Obtener la primera fila como encabezados de las columnas
    $header = array_shift($sheetData);

    // Normalizar los encabezados para reemplazar espacios y eliminar acentos
    $normalizedHeader = array_map(function ($columnName) {
        // Reemplaza espacios en blanco con guiones bajos y elimina acentos
        $columnName = preg_replace('/\s+/', '_', trim($columnName));
        return removeAccents($columnName);
    }, $header);

    // Formatear las filas restantes con los encabezados normalizados como claves
    $formattedData = [];
    foreach ($sheetData as $row) {
        $formattedRow = [];
        foreach ($normalizedHeader as $columnKey => $normalizedColumnName) {
            $formattedRow[$normalizedColumnName] = $row[$columnKey] ?? ''; // Evita errores con campos vacíos
        }
        $formattedData[] = $formattedRow;
    }

    // Convertimos el array a formato JSON, asegurando que no se escapen los caracteres especiales
    $json = json_encode($formattedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Asegurarnos de que las fechas no tengan '\/'
    $json = str_replace('\/', '/', $json);

    // Retornamos el JSON generado
    return $json;
}

// Función para eliminar acentos y caracteres especiales
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
