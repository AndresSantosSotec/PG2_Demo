<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migración de Datos - API de Migración</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../../Assets/css/migrate.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
    <?php include '../Vistas/sides/side_migrations.php';?>

        <div class="content">
            <div class="container">
                <h2 class="text-center mb-4">Migración de Datos</h2>

                <!-- Credenciales del servidor -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="credentialsForm">
                            <div class="form-group">
                                <label for="ip">IP del Servidor de origen</label>
                                <input type="text" id="ip" class="form-control" placeholder="Ingrese la IP del servidor" required>
                            </div>
                            <div class="form-group">
                                <label for="user">Usuario</label>
                                <input type="text" id="user" class="form-control" placeholder="Ingrese el usuario" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Contraseña</label>
                                <input type="password" id="password" class="form-control" placeholder="Ingrese la contraseña">
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Conectar</button>
                        </form>
                    </div>
                </div>

                <!-- Selección de bases de datos y tablas -->
                <div id="databasesSection" style="display: none;">
                    <h4 class="text-center mb-3">Seleccione la Base de Datos</h4>
                    <form id="databaseForm">
                        <div class="form-group">
                            <label for="originDb">Base de Datos de Origen</label>
                            <select id="originDb" class="form-control" required></select>
                        </div>
                        <div class="form-group">
                            <label>¿Desea migrar toda la base de datos o una tabla específica?</label>
                            <select id="migrationOption" class="form-control" required>
                                <option value="wholeDb">Toda la base de datos</option>
                                <option value="singleTable">Migrar una tabla</option>
                            </select>
                        </div>
                        <div id="tableSelectionSection" style="display: none;">
                            <h4 class="text-center mb-3">Seleccione la Tabla</h4>
                            <select id="originTable" class="form-control"></select>
                        </div>
                        <button type="submit" class="btn btn-success mt-3">Credeniales de destino </button>
                    </form>
                </div>

                <!-- Barra de progreso -->
                <div class="progress mt-4">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">0%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const credentialsForm = document.getElementById('credentialsForm');
        const databaseForm = document.getElementById('databaseForm');
        const databasesSection = document.getElementById('databasesSection');
        const tableSelectionSection = document.getElementById('tableSelectionSection');
        const originDbSelect = document.getElementById('originDb');
        const originTableSelect = document.getElementById('originTable');
        const migrationOptionSelect = document.getElementById('migrationOption');
        const progressBar = document.querySelector('.progress-bar');
        const progressContainer = document.querySelector('.progress');

        // Conectar al servidor para obtener bases de datos
        credentialsForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const ip = document.getElementById('ip').value;
            const user = document.getElementById('user').value;
            const password = document.getElementById('password').value;

            Swal.fire({
                title: 'Conectando...',
                text: 'Estableciendo conexión con el servidor.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch('../../Backend/get_databases.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ip, user, password })
                });

                const result = await response.json();
                Swal.close();

                if (result.success) {
                    populateDatabases(result.databases);
                    databasesSection.style.display = 'block';
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'No se pudo conectar al servidor.', 'error');
            }
        });

        // Llenar bases de datos
        function populateDatabases(databases) {
            originDbSelect.innerHTML = databases.map(db => `<option value="${db}">${db}</option>`).join('');
        }

        // Mostrar selección de tabla si es necesario
        migrationOptionSelect.addEventListener('change', () => {
            if (migrationOptionSelect.value === 'singleTable') {
                loadTables();
                tableSelectionSection.style.display = 'block';
            } else {
                tableSelectionSection.style.display = 'none';
            }
        });

        // Cargar tablas de la base de datos seleccionada
        async function loadTables() {
            const database = originDbSelect.value;

            try {
                const response = await fetch('../../Backend/get_tables.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ database })
                });

                const result = await response.json();
                if (result.success) {
                    originTableSelect.innerHTML = result.tables.map(table => `<option value="${table}">${table}</option>`).join('');
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'No se pudieron cargar las tablas.', 'error');
            }
        }

    </script>

</body>

</html>
