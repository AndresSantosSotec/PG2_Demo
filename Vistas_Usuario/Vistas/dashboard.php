<?php
session_start();

// Verificar si la sesión está activa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Función para cerrar sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard API - Struct Migraciones</title>

    <!-- Enlaces a Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4.5.2/dist/minty/bootstrap.min.css">

    <!-- Enlace a FontAwesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        body {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #3b82f6, #9333ea);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 15px;
        }

        .sidebar a {
            padding: 15px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .sidebar a i {
            margin-right: 10px;
        }

        .sidebar a:hover {
            background-color: #4c51bf;
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
            background-color: #f8fafc;
        }

        .card-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2 class="text-center py-4">Menú</h2>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
    <a href="demo.php"><i class="fas fa-project-diagram"></i>Demo API</a>
    <a href="historial_migraciones.php"><i class="fas fa-history"></i>Historial</a>
    <a href="reportes.php"><i class="fas fa-chart-line"></i>Reportes</a>
    <a href="?logout=true"><i class="fas fa-sign-out-alt"></i>Cerrar Sesión</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <h1 class="text-center mb-4">Bienvenido al Dashboard de API</h1>
    
    <div class="row">
        <!-- Tarjeta para Demo de API -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title">Demo de API</h5>
                </div>
                <div class="card-body">
                    <p>Accede a la demostración interactiva de la API para ver su funcionamiento.</p>
                    <a href="#" class="btn btn-primary">Acceder a Demo</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta para API Key y usos -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title">Tu API Key</h5>
                </div>
                <div class="card-body">
                    <p>Tu API Key: <strong id="api-key"></strong></p>
                    <p>Usos restantes: <strong id="api-usage"></strong></p>
                    <a href="#" class="btn btn-success">Administrar API Key</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta para historial de migraciones -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title">Historial de Migraciones</h5>
                </div>
                <div class="card-body">
                    <p>Revisa el historial de migraciones realizadas con la API.</p>
                    <a href="historial_migraciones.php" class="btn btn-info">Ver Historial</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta para generar reportes -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title">Generar Reportes</h5>
                </div>
                <div class="card-body">
                    <p>Genera reportes detallados sobre las migraciones realizadas.</p>
                    <a href="reportes.php" class="btn btn-warning">Generar Reporte</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Cargar datos de API Key y usos desde sessionStorage -->
<script>
    const apiKey = sessionStorage.getItem('api_key');
    const usosRestantes = sessionStorage.getItem('usos_restantes');

    if (apiKey && usosRestantes) {
        document.getElementById('api-key').innerText = apiKey;
        document.getElementById('api-usage').innerText = usosRestantes;
    } else {
        alert('No se encontraron datos de API Key, por favor inicia sesión de nuevo.');
        window.location.href = 'login.php'; // Redirigir al login si no hay datos
    }
</script>

</body>

</html>
