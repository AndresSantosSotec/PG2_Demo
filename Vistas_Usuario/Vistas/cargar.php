<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Excel/JSON - API de Migración</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../Assets/css/estilos_carga.css" rel="stylesheet">
</head>
<body>

<div class="wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <h2>Cargar Excel/JSON a API de Migración</h2>
                    <div class="upload-area">
                        <form id="uploadForm" action="../../Backend/uploads/upload.php" method="POST" enctype="multipart/form-data">
                            <label for="file">Arrastra tu archivo Excel o JSON aquí o haz clic para subir</label>
                            <input type="file" name="file" id="file" accept=".xlsx, .xls, .json">
                            <br><br>
                            <button type="submit" class="btn btn-primary">Subir y Convertir</button>
                        </form>
                        <br>
                        <div id="downloadLink" style="display: none;">
                            <a href="#" id="convertedFileLink" class="btn btn-success">Descargar archivo convertido</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recuadro inferior para mostrar el JSON convertido -->
        <div class="row json-container">
            <div class="col-md-12">
                <h4>Datos Convertidos (Formato JSON)</h4>
                <div id="jsonData"></div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    const form = document.getElementById('uploadForm');
    const fileInput = document.getElementById('file');
    const downloadLinkDiv = document.getElementById('downloadLink');
    const convertedFileLink = document.getElementById('convertedFileLink');
    const jsonDataDiv = document.getElementById('jsonData');
    const submitButton = document.querySelector('button[type="submit"]');

    // Validar si hay un archivo seleccionado antes de enviar el formulario
    form.addEventListener('submit', async (e) => {
        if (!fileInput.value) {
            alert('Por favor, selecciona un archivo antes de subir.');
            e.preventDefault();
            return;
        }

        e.preventDefault();

        submitButton.disabled = true;
        submitButton.textContent = 'Cargando...';

        const formData = new FormData(form);

        const response = await fetch('../../Backend/uploads/upload.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        submitButton.disabled = false;
        submitButton.textContent = 'Subir y Convertir';

        if (result.success) {
            downloadLinkDiv.style.display = 'block';
            convertedFileLink.href = result.fileUrl;
            convertedFileLink.textContent = "Descargar " + result.fileName;

            // Mostrar los datos JSON en pantalla
            jsonDataDiv.style.display = 'block';
            jsonDataDiv.textContent = JSON.stringify(JSON.parse(result.jsonData), null, 2);
        } else {
            alert("Error: " + result.message);
        }
    });
</script>

<!-- Bootstrap JS y dependencias -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
