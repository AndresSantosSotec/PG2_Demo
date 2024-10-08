<?php
namespace API\Controllers;

use API\Services\FileService;

class UploadController {
    private $fileService;

    public function __construct() {
        $this->fileService = new FileService();
    }

    // Método para subir archivos
    public function upload($file) {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        // Llama al servicio para leer archivos según su tipo
        switch ($extension) {
            case 'csv':
                return $this->fileService->readCSV($file['tmp_name']);
            case 'json':
                return $this->fileService->readJSON($file['tmp_name']);
            case 'xlsx':
                return $this->fileService->readExcel($file['tmp_name']);
            default:
                return "Formato no soportado.";
        }
    }
}
