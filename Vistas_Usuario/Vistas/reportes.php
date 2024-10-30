<?php
// Iniciar la sesión y verificar autenticación
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php"); // Redirigir al login si no está autenticado
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Reportes</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../Assets/css/repo.css">

</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2 class="text-center text-white">Menú</h2>
        <a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a>
        <a href="cargar.php"><i class="fas fa-upload"></i> Cargar Datos</a>
        <a href="migracion_datos.php"><i class="fas fa-exchange-alt"></i> Migración de Datos</a>
        <a href="reportes.php"><i class="fas fa-chart-line"></i> Reportes</a>
    </div>


    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center mb-4">Dashboard de Reportes</h1>
        <div class="container">
            <div class="row g-4">
                <!-- Reporte General -->
                <div class="col-md-6 col-lg-4">
                    <div class="card text-center bg-primary text-white" onclick="location.href='../../Vistas_Usuario/Vistas/Reportes/reporte_general.php'">
                        <div class="card-body">
                            <i class="fas fa-list-alt card-icon mb-3"></i>
                            <h5 class="card-title">Reporte General de Migraciones</h5>
                            <p class="card-text">Resumen de todas las migraciones realizadas.</p>
                        </div>
                    </div>
                </div>

                <!-- Reporte por Usuario -->
                <div class="col-md-6 col-lg-4">
                    <div class="card text-center bg-success text-white" onclick="location.href='../../Vistas_Usuario/Vistas/Reportes/reporte_usuario.php'">
                        <div class="card-body">
                            <i class="fas fa-user card-icon mb-3"></i>
                            <h5 class="card-title">Reporte de Migraciones por Usuario</h5>
                            <p class="card-text">Migraciones realizadas por cada usuario.</p>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Errores -->
                <div class="col-md-6 col-lg-4">
                    <div class="card text-center bg-danger text-white" onclick="location.href='../../Vistas_Usuario/Vistas/Reportes/reporte_errores.php'">
                        <div class="card-body">
                            <i class="fas fa-exclamation-triangle card-icon mb-3"></i>
                            <h5 class="card-title">Reporte de Errores en Migraciones</h5>
                            <p class="card-text">Identifica los errores ocurridos durante la migración.</p>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Uso de API -->
                <div class="col-md-6 col-lg-4">
                    <div class="card text-center bg-info text-white" onclick="location.href='../../Vistas_Usuario/Vistas/Reportes/reporte_uso_api.php'">
                        <div class="card-body">
                            <i class="fas fa-key card-icon mb-3"></i>
                            <h5 class="card-title">Reporte de Uso de API / Claves</h5>
                            <p class="card-text">Monitorea el uso de las claves API por usuario.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón Volver al Inicio -->
            <div class="text-center">
                <button class="btn btn-volver text-white" onclick="location.href='dashboard.php'">
                    <i class="fas fa-arrow-left"></i> Volver al Inicio
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>