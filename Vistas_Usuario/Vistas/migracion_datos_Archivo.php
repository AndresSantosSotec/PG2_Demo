<?php
// Iniciar la sesión
session_start();

// Verificar si la sesión tiene los datos necesarios, si no redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Función para cerrar sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Obtener el usuario_id de la sesión
$usuario_id = $_SESSION['usuario_id'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migración de Datos - API de Migración</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="../../Assets/css/migrate.css" rel="stylesheet">
    <link href="../../Assets/css/migrateA.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <?php include '../Vistas/sides/side_migration_archive.php'; ?>

        <div class="content">
            <div class="container">
                <h2 class="text-center mb-4">Cargar Archivo y Conectar al Servidor</h2>

                <!-- Área de carga del archivo -->
                <div class="upload-area mt-4">
                    <form id="uploadForm" method="POST" enctype="multipart/form-data">
                        <label for="file">Sube tu archivo (Excel/JSON)</label>
                        <input type="file" name="file" id="file" accept=".xlsx, .xls, .json, .csv" required>
                        <button type="submit" class="btn btn-primary mt-3">Subir y Convertir</button>
                    </form>
                    <div id="downloadLink" style="display: none;" class="mt-3">
                        <a href="#" id="convertedFileLink" class="btn btn-success">Descargar archivo convertido</a>
                    </div>
                </div>

                <!-- Datos Convertidos (JSON) -->
                <div class="mt-4">
                    <h4>Datos Convertidos (JSON)</h4>
                    <div id="jsonData" class="p-3 border bg-light" style="height: 250px; overflow-y: auto;"></div>
                </div>

                <!-- Formulario para Credenciales del Servidor -->
                <div class="mt-4">
                    <h4>Credenciales del Servidor</h4>
                    <form id="credentialsForm">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="IP del Servidor" id="ip" required>
                        </div>
                        <div class="form-group mt-2">
                            <input type="text" class="form-control" placeholder="Usuario" id="user" required>
                        </div>
                        <div class="form-group mt-2">
                            <input type="password" class="form-control" placeholder="Contraseña" id="password">
                        </div>
                        <button type="submit" class="btn btn-info mt-3">Conectar</button>
                    </form>
                </div>

                <!-- Sección de Bases de Datos -->
                <div class="mt-4" id="databasesSection" style="display: none;">
                    <h4>Seleccionar Base de Datos</h4>
                    <select class="form-control" id="originDb"></select>
                </div>

                <!-- Sección de Base de Datos y Tabla -->
                <div class="mt-4" id="tableSection" style="display: none;">
                    <h4>Tabla de Destino</h4>
                    <div class="form-group">
                        <select class="form-control" id="tableOption">
                            <option value="select">Seleccionar Tabla Existente</option>
                            <option value="create">Crear Nueva Tabla</option>
                        </select>
                    </div>
                    <div class="form-group mt-3" id="newTableNameSection" style="display: none;">
                        <input type="text" class="form-control" placeholder="Nombre de la Nueva Tabla" id="newTableName">
                    </div>
                    <button class="btn btn-success mt-3" id="generateSQLBtn" style="display: none;">Generar SQL para Crear Tabla</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!--<script src="../../Assets/js/MigraA.js"></script>-->
    <script src="../../Assets/js/MigraATest.js"></script>
    <script src="../../Assets/js/cache.js"></script>

    <script>
        // Pasar el ID del usuario de PHP a JavaScript
        const usuarioId = <?php echo json_encode($usuario_id); ?>;
        console.log("ID de Usuario:", usuarioId);
    </script>
</body>

</html>
