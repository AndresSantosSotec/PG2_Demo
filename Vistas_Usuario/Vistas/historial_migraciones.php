<?php
// Iniciar la sesión y verificar autenticación
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php"); // Redirigir al login si no está autenticado
    exit();
}

// Conexión a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'migraciones_pg2');

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener el ID del usuario logueado
$usuario_id = $_SESSION['usuario_id'];

// Consulta con INNER JOIN para obtener las migraciones del usuario logueado
$sql = "
    SELECT 
        m.archivo_origen, 
        m.estado, 
        m.fecha_inicio, 
        m.fecha_fin, 
        u.nombre_completo 
    FROM 
        migraciones m 
    INNER JOIN 
        tb_users u 
    ON 
        m.user_id = u.id 
    WHERE 
        u.id = ?
";

// Preparar y ejecutar la consulta
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Migraciones</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../Assets/css/hist.css">
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Menú</h2>
        <a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a>
        <a href="cargar.php"><i class="fas fa-upload"></i> Cargar Datos</a>
        <a href="migracion_datos.php"><i class="fas fa-exchange-alt"></i> Migración de Datos</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Historial de Migraciones</h1>
        <div class="container table-container">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Archivo Origen</th>
                        <th>Estado</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0) : ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                                <td><?php echo htmlspecialchars($row['archivo_origen']); ?></td>
                                <td>
                                    <?php
                                    $estado = strtolower($row['estado']);
                                    $badgeClass = 'bg-secondary'; // Clase predeterminada

                                    switch ($estado) {
                                        case 'completada':
                                        case 'completado':
                                            $badgeClass = 'bg-success';
                                            break;
                                        case 'error':
                                            $badgeClass = 'bg-danger';
                                            break;
                                        case 'en proceso':
                                            $badgeClass = 'bg-warning';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo htmlspecialchars($row['estado']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($row['fecha_inicio']); ?></td>
                                <td><?php echo htmlspecialchars($row['fecha_fin']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay migraciones registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Botón para ir a reportes -->
            <div class="text-center mt-4">
                <a href="reportes.php" class="btn btn-primary">
                    <i class="fas fa-chart-line"></i> Ir a Reportes de Migraciones
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>