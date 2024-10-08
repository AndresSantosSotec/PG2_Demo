<?php
use API\Controllers\UploadController;
use API\Controllers\MigrationController;

require 'vendor/autoload.php';  // Asegúrate de tener autoload de Composer para cargar las clases

$uploadController = new UploadController();
$migrationController = new MigrationController('localhost', 'nombre_db', 'usuario', 'contraseña');

// Verificar si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        // Subir el archivo y obtener los datos procesados
        $data = $uploadController->upload($_FILES['file']);
        
        if (is_array($data)) {
            // Migrar los datos a la tabla
            echo $migrationController->compareAndMigrate('nombre_tabla', $data);
        } else {
            echo $data;  // Error o mensaje sobre el archivo
        }
    } else {
        echo "No se ha subido ningún archivo.";
    }
} else {
    echo "Utiliza una solicitud POST para cargar archivos.";
}
