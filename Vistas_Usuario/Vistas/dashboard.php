<?php
// Iniciar la sesión
session_start();

// Asegurar la persistencia correcta de la sesión
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

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
    <link rel="stylesheet" href="../../Assets/css/ds.css">
    <style>

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
                        <hr>
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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            
        </script>
        

        <!-- JavaScript para Mostrar la API Key y Usos Restantes -->
        <script>
            // Cargar datos de la sesión PHP en los elementos HTML
            document.addEventListener('DOMContentLoaded', function() {
                const apiKey = "<?php echo $_SESSION['api_key'] ?? 'No disponible'; ?>";
                const usosRestantes = "<?php echo $_SESSION['usos_restantes'] ?? 'No disponible'; ?>";

                // Asignar los valores a los elementos del DOM
                document.getElementById('api-key').innerText = apiKey;
                document.getElementById('usos-restantes').innerText = usosRestantes;
            });

            // Lógica para colapsar la sidebar
            const toggleSidebar = document.getElementById('toggleSidebar');
            const sidebar = document.getElementById('sidebar');

            toggleSidebar.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
            });
        </script>
</body>

</html>