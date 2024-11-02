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

// Consulta SQL con LEFT JOIN para obtener las migraciones realizadas por el usuario logueado
$sql = "
    SELECT 
        m.id AS migracion_id, 
        COALESCE(ac.nombre_archivo, 'Sin archivo') AS archivo_origen, 
        m.estado, 
        m.fecha_inicio, 
        m.fecha_fin, 
        TIMESTAMPDIFF(SECOND, m.fecha_inicio, m.fecha_fin) AS duracion,
        (SELECT COUNT(*) FROM logs_migraciones l WHERE l.migracion_id = m.id) AS total_logs
    FROM 
        migraciones m
    LEFT JOIN 
        archivos_cargados ac ON ac.id = m.archivo_origen
    WHERE 
        m.user_id = ?
    ORDER BY 
        m.fecha_inicio DESC
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

if ($result->num_rows === 0) {
    echo "No hay registros de migraciones para este usuario.";
} else {
    echo "Se encontraron registros.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Migraciones por Usuario</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .badge {
            padding: 0.5em 0.7em;
            font-size: 0.9em;
        }

        .log-details {
            font-size: 0.9em;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Reporte de Migraciones por Usuario</h1>

        <div class="d-flex justify-content-between mb-3">
            <a href="../dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
            <form method="POST" action="./pdf/Repo_USU.php">
                <button type="submit" name="descargar_pdf" class="btn btn-primary">Descargar PDF</button>
            </form>
        </div>


        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID Migración</th>
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
                            <td>
                                <span class="badge 
                                    <?php echo ($row['estado'] === 'Completada') ? 'bg-success' : (($row['estado'] === 'Error') ? 'bg-danger' : 'bg-warning'); ?>">
                                    <?php echo htmlspecialchars($row['estado']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['fecha_inicio']); ?></td>
                            <td><?php echo htmlspecialchars($row['fecha_fin']); ?></td>
                            <td><?php echo htmlspecialchars($row['duracion']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_logs']); ?></td>
                        </tr>
                        <tr class="log-details">
                            <td colspan="7">
                                <strong>Detalles de Logs:</strong><br>
                                <?php
                                // Consulta para obtener los logs específicos de esta migración
                                $log_sql = "SELECT mensaje, fecha FROM logs_migraciones WHERE migracion_id = ?";
                                $log_stmt = $conexion->prepare($log_sql);
                                $log_stmt->bind_param("i", $row['migracion_id']);
                                $log_stmt->execute();
                                $log_result = $log_stmt->get_result();

                                if ($log_result->num_rows > 0) {
                                    while ($log_row = $log_result->fetch_assoc()) {
                                        echo "<div>- " . htmlspecialchars($log_row['fecha']) . ": " . htmlspecialchars($log_row['mensaje']) . "</div>";
                                    }
                                } else {
                                    echo "<div>No hay logs disponibles para esta migración.</div>";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay migraciones registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>