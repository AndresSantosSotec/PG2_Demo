<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard API - Struct Migraciones</title>

    <!-- Enlaces a Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.1.3/dist/minty/bootstrap.min.css">

    <!-- Enlace a Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Enlace a FontAwesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            display: flex;
            height: 100vh;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
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
            padding: 20px;
            background-color: #e5e9f0;
        }

        .collapse-btn {
            margin-bottom: 20px;
            text-align: right;
        }

        .collapse-btn i {
            font-size: 1.5rem;
            color: #fff;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header i {
            margin-right: 10px;
            font-size: 1.5rem;
        }

        .card-header {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .bg-success {
            background-color: #82e0aa !important;
        }

        .bg-danger {
            background-color: #f5b7b1 !important;
        }

        .bg-info {
            background-color: #85c1e9 !important;
        }

        .bg-warning {
            background-color: #f9e79f !important;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button class="btn btn-sm collapse-btn" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
        <h2>Menú</h2>
        <a href="carga_datos.php"><i class="fas fa-upload"></i> <span>Carga de Datos</span></a>
        <a href="migracion_datos.php"><i class="fas fa-exchange-alt"></i> <span>Migración de Datos</span></a>
        <a href="historial_migraciones.php"><i class="fas fa-history"></i> <span>Historial</span></a>
        <a href="reportes.php"><i class="fas fa-chart-line"></i> <span>Reportes</span></a>
        <a href="?logout=true"><i class="fas fa-sign-out-alt"></i> <span>Cerrar Sesión</span></a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-center mb-4">Bienvenido al Dashboard de API</h1>

        <div class="row">
            <!-- Tarjeta para Carga de Datos -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-file-upload"></i>
                        <h5 class="card-title d-inline">Carga de Datos</h5>
                    </div>
                    <div class="card-body">
                        <p>Sube los archivos que deseas migrar utilizando la API.</p>
                        <a href="./cargar.php" class="btn btn-success">Ir a Carga de Datos</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta para Migración de Datos -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-exchange-alt"></i>
                        <h5 class="card-title d-inline">Migración de Datos</h5>
                    </div>
                    <div class="card-body">
                        <p>Realiza la migración de los datos cargados a la base de datos destino.</p>
                        <a href="migracion_datos.php" class="btn btn-danger">Ir a Migración de Datos</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta para Historial -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-history"></i>
                        <h5 class="card-title d-inline">Historial de Migraciones</h5>
                    </div>
                    <div class="card-body">
                        <p>Consulta el historial de migraciones realizadas con la API.</p>
                        <a href="historial_migraciones.php" class="btn btn-info">Ver Historial</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta para generar reportes -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-chart-line"></i>
                        <h5 class="card-title d-inline">Generar Reportes</h5>
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

    <!-- Script para colapsar el sidebar -->
    <script>
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');

        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });
    </script>

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
