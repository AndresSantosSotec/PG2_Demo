<?php
namespace API\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;

class FileService {
    // Leer archivo CSV
    public function readCSV($file) {
        $rows = [];
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    // Leer archivo JSON
    public function readJSON($file) {
        $data = file_get_contents($file);
        return json_decode($data, true);
    }

    // Leer archivo Excel
    public function readExcel($file) {
        $spreadsheet = IOFactory::load($file);
        return $spreadsheet->getActiveSheet()->toArray();
    }
}
