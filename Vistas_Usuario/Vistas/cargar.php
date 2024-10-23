<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Excel/JSON - API de Migración</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../Assets/css/estilos_carga.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #e9f7ef;
            font-family: 'Poppins', sans-serif;
            display: flex;
            height: 100vh;
        }

        .wrapper {
            display: flex;
            width: 100%;
        }

        .sidebar {
            width: 250px;
            background-color: #004d66;
            color: white;
            transition: all 0.3s ease;
            padding-top: 20px;
            overflow: hidden;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar h4 {
            text-align: center;
            margin-bottom: 20px;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed h4 {
            opacity: 0;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 15px;
            color: #a1c4c9;
            text-decoration: none;
            font-size: 18px;
            transition: background-color 0.3s, color 0.3s;
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
            background-color: #009688;
            color: white;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
        }

        .upload-area {
            border: 2px dashed #17a2b8;
            padding: 40px;
            text-align: center;
            background-color: #ffffff;
            border-radius: 10px;
            transition: background-color 0.3s;
        }

        .upload-area:hover {
            background-color: #d0f0f7;
        }

        .btn-primary {
            background-color: #009688;
            border-color: #00796b;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #00796b;
        }

        .btn-migrate {
            display: none;
            margin-top: 20px;
        }

        #jsonData {
            background-color: #f1f8f9;
            padding: 15px;
            border: 1px solid #a1c4c9;
            border-radius: 5px;
            height: 300px;
            overflow-y: scroll;
            font-family: monospace;
            margin-top: 20px;
            color: #004d66;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <div class="sidebar" id="sidebar">
            <button class="btn btn-sm text-white mb-3" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Menú</h4>
            <a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
            <a href="#"><i class="fas fa-exchange-alt"></i> <span>Migración de Datos</span></a>
            <a href="#"><i class="fas fa-history"></i> <span>Historial</span></a>
        </div>

        <div class="content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <h2 class="text-center">Cargar Excel/JSON a API de Migración</h2>
                            <div class="upload-area mt-4">
                                <form id="uploadForm" method="POST" enctype="multipart/form-data">
                                    <label for="file">Arrastra tu archivo aquí o haz clic para subir</label>
                                    <input type="file" name="file" id="file" accept=".xlsx, .xls, .json, .csv">
                                    <button type="submit" class="btn btn-primary mt-3">Subir y Convertir</button>
                                </form>
                                <div id="downloadLink" style="display: none;" class="mt-3">
                                    <a href="#" id="convertedFileLink" class="btn btn-success">Descargar archivo convertido</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h4>Datos Convertidos (Formato JSON)</h4>
                        <div id="jsonData"></div>
                    </div>
                </div>

                <button id="migrateBtn" class="btn btn-info btn-migrate">Migrar Datos</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const form = document.getElementById('uploadForm');
        const fileInput = document.getElementById('file');
        const downloadLinkDiv = document.getElementById('downloadLink');
        const convertedFileLink = document.getElementById('convertedFileLink');
        const jsonDataDiv = document.getElementById('jsonData');
        const migrateButton = document.getElementById('migrateBtn');

        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!fileInput.value) {
                Swal.fire('Error', 'Por favor, selecciona un archivo.', 'error');
                return;
            }

            Swal.fire({
                title: 'Cargando...',
                text: 'Por favor espera mientras procesamos tu archivo.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData(form);
            const response = await fetch('../../Backend/uploads/upload.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            Swal.close();

            if (result.success) {
                Swal.fire('¡Éxito!', 'El archivo se ha cargado y convertido con éxito.', 'success');
                downloadLinkDiv.style.display = 'block';

                const blob = new Blob([result.jsonData], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                convertedFileLink.href = url;
                convertedFileLink.download = result.fileName;
                jsonDataDiv.textContent = JSON.stringify(JSON.parse(result.jsonData), null, 2);

                migrateButton.style.display = 'block';
            } else {
                Swal.fire('Error', result.message, 'error');
            }
        });

        migrateButton.addEventListener('click', () => {
            Swal.fire('Migración Iniciada', 'La migración de datos ha comenzado.', 'info');
        });
    </script>

</body>

</html>
