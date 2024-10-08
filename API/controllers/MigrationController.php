<?php
namespace API\Controllers;

use API\Models\Database;
use PDO;

class MigrationController {
    private $conn;

    public function __construct($host, $db_name, $username, $password) {
        $database = new Database($host, $db_name, $username, $password);
        $this->conn = $database->getConnection();
    }

    public function compareAndMigrate($table, $data) {
        // Obtener las columnas de la tabla destino
        $query = "SHOW COLUMNS FROM " . $table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Validar que las columnas del archivo coinciden con las de la tabla
        $fileColumns = array_keys($data[0]);
        $diff = array_diff($fileColumns, $columns);
        if (!empty($diff)) {
            return "Columnas no coinciden: " . implode(', ', $diff);
        }

        // Preparar la consulta de inserción
        $placeholders = implode(',', array_fill(0, count($fileColumns), '?'));
        $query = "INSERT INTO $table (" . implode(',', $fileColumns) . ") VALUES ($placeholders)";
        $stmt = $this->conn->prepare($query);

        // Insertar los datos
        foreach ($data as $row) {
            try {
                $stmt->execute(array_values($row));
            } catch (\PDOException $e) {
                return "Error de inserción: " . $e->getMessage();
            }
        }

        return "Migración completada.";
    }
}
