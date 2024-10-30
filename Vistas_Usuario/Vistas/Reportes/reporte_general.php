<?php
// Iniciar la sesión y verificar autenticación
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Obtener el ID del usuario logueado
$usuario_id = $_SESSION['usuario_id'];

// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'migraciones_pg2');

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Consulta SQL para obtener el reporte general para el usuario logueado
$sql = "
    SELECT 
        m.id AS migracion_id, 
        u.nombre_completo AS usuario, 
        ac.nombre_archivo AS archivo_origen, 
        m.estado, 
        m.fecha_inicio, 
        m.fecha_fin, 
        TIMESTAMPDIFF(SECOND, m.fecha_inicio, m.fecha_fin) AS duracion,
        (SELECT COUNT(*) FROM logs_migraciones l WHERE l.migracion_id = m.id) AS total_logs
    FROM 
        migraciones m
    INNER JOIN 
        tb_users u ON m.user_id = u.id
    INNER JOIN 
        archivos_cargados ac ON ac.id = m.archivo_origen
    WHERE 
        m.user_id = ?
";

// Preparar la consulta
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

// Enlazar el parámetro de usuario
$stmt->bind_param("i", $usuario_id);

// Ejecutar la consulta
if (!$stmt->execute()) {
    die("Error en la ejecución de la consulta: " . $stmt->error);
}

$result = $stmt->get_result();

// Función para descargar CSV
if (isset($_POST['descargar_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="reporte_general.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID Migración', 'Usuario', 'Archivo Origen', 'Estado', 'Fecha Inicio', 'Fecha Fin', 'Duración (s)', 'Total Logs']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte General de Migraciones</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Reporte General de Migraciones</h1>

        <div class="d-flex justify-content-between mb-3">
            <a href="../reportes.php" class="btn btn-secondary">Volver al Dashboard</a>
            <form method="POST">
                <button type="submit" name="descargar_csv" class="btn btn-primary">Descargar CSV</button>
            </form>
        </div>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID Migración</th>
                    <th>Usuario</th>
                    <th>Archivo Origen</th>
                    <th>Estado</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Duración (s)</th>
                    <th>Total Logs</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) : ?>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['migracion_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                            <td><?php echo htmlspecialchars($row['archivo_origen']); ?></td>
                            <td>
                                <span class="badge 
                                    <?php echo ($row['estado'] === 'Completada') ? 'bg-success' : 
                                                 (($row['estado'] === 'Error') ? 'bg-danger' : 'bg-warning'); ?>">
                                    <?php echo htmlspecialchars($row['estado']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['fecha_inicio']); ?></td>
                            <td><?php echo htmlspecialchars($row['fecha_fin']); ?></td>
                            <td><?php echo htmlspecialchars($row['duracion']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_logs']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8" class="text-center">No hay migraciones registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
