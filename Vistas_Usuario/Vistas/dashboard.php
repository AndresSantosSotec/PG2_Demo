<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php"); // Redirigir al login si no está autenticado
    exit();
}

// Verificar si se hizo clic en "Cerrar Sesión"
if (isset($_GET['logout'])) {
    session_destroy(); // Destruir la sesión
    header("Location: ../login.php"); // Redirigir al login
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard API - Struct Migraciones</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            display: flex;
            height: 100vh;
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 250px;
            background: #2f3640;
            color: #f5f6fa;
            display: flex;
            flex-direction: column;
            padding: 15px;
            transition: all 0.3s ease;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ddd;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed h2 {
            opacity: 0;
        }

        .sidebar a {
            padding: 15px;
            margin-bottom: 10px;
            text-decoration: none;
            color: #f5f6fa;
            font-weight: 500;
            display: flex;
            align-items: center;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar a i {
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .sidebar.collapsed a {
            justify-content: center;
        }

        .sidebar.collapsed a i {
            margin-right: 0;
        }

        .sidebar.collapsed a span {
            display: none;
        }

        .sidebar a:hover {
            background-color: #576574;
            color: #fff;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
            background-color: #e8edf3;
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
            color: #34495e;
        }

        .card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease-in-out;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            font-weight: 600;
            font-size: 1.25rem;
            padding: 20px;
        }

        .card-body {
            padding: 20px;
            background-color: #ffffff;
            color: #7f8c8d;
        }

        .bg-success {
            background-color: #2ecc71 !important;
        }

        .bg-danger {
            background-color: #e74c3c !important;
        }

        .bg-info {
            background-color: #3498db !important;
        }

        .bg-warning {
            background-color: #f1c40f !important;
        }

        .card-title {
            margin: 0;
            color: white;
        }

        .card i {
            margin-right: 10px;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button class="btn btn-sm text-white mb-3" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
        <h2>Menú</h2>
        <a href="cargar.php"><i class="fas fa-upload"></i> <span>Carga de Datos</span></a>
        <a href="migracion_datos.php"><i class="fas fa-exchange-alt"></i> <span>Migración de Datos</span></a>
        <a href="historial_migraciones.php"><i class="fas fa-history"></i> <span>Historial</span></a>
        <a href="reportes.php"><i class="fas fa-chart-line"></i> <span>Reportes</span></a>
        <a href="dashboard.php?logout=true"><i class="fas fa-sign-out-alt"></i> <span>Cerrar Sesión</span></a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Bienvenido al Dashboard de API</h1>

        <div class="row">
            <!-- Carga de Datos -->
            <div class="col-lg-6 mb-4">
                <div class="card" onclick="location.href='./cargar.php'">
                    <div class="card-header bg-success">
                        <i class="fas fa-file-upload"></i>
                        <span class="card-title">Carga de Datos</span>
                    </div>
                    <div class="card-body">
                        <p>Sube los archivos que deseas migrar utilizando la API.</p>
                    </div>
                </div>
            </div>

            <!-- Migración de Datos -->
            <div class="col-lg-6 mb-4">
                <div class="card" onclick="location.href='migracion_datos.php'">
                    <div class="card-header bg-danger">
                        <i class="fas fa-exchange-alt"></i>
                        <span class="card-title">Migración de Datos</span>
                    </div>
                    <div class="card-body">
                        <p>Realiza la migración de los datos cargados a la base de datos destino.</p>
                    </div>
                </div>
            </div>

            <!-- Historial -->
            <div class="col-lg-6 mb-4">
                <div class="card" onclick="location.href='historial_migraciones.php'">
                    <div class="card-header bg-info">
                        <i class="fas fa-history"></i>
                        <span class="card-title">Historial de Migraciones</span>
                    </div>
                    <div class="card-body">
                        <p>Consulta el historial de migraciones realizadas con la API.</p>
                    </div>
                </div>
            </div>

            <!-- Reportes -->
            <div class="col-lg-6 mb-4">
                <div class="card" onclick="location.href='reportes.php'">
                    <div class="card-header bg-warning">
                        <i class="fas fa-chart-line"></i>
                        <span class="card-title">Generar Reportes</span>
                    </div>
                    <div class="card-body">
                        <p>Genera reportes detallados sobre las migraciones realizadas.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');

        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });
    </script>

</body>

</html>
