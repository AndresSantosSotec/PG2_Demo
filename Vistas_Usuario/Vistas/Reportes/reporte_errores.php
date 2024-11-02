<?php
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'migraciones_pg2');
if ($conexion->connect_error) {
    die("Conexion fallida: " . $conexion->connect_error);
}
$conexion->set_charset("utf8");

// Consulta para obtener estadísticas de errores
$sql_errores = "
    SELECT 
        estado,
        COUNT(*) AS cantidad
    FROM 
        migraciones
    GROUP BY estado
";
$result_errores = $conexion->query($sql_errores);

$error_data = [];
while ($row = $result_errores->fetch_assoc()) {
    $error_data[$row['estado']] = $row['cantidad'];
}

// Consulta para obtener frecuencia de uso diario
$sql_uso = "
    SELECT 
        DATE(fecha_inicio) AS fecha,
        COUNT(*) AS cantidad
    FROM 
        migraciones
    GROUP BY DATE(fecha_inicio)
    ORDER BY fecha
";
$result_uso = $conexion->query($sql_uso);

$uso_data = [];
while ($row = $result_uso->fetch_assoc()) {
    $uso_data[] = [
        'fecha' => $row['fecha'],
        'cantidad' => $row['cantidad']
    ];
}

// Convertir datos a JSON para ser utilizados en el frontend
$error_data_json = json_encode($error_data);
$uso_data_json = json_encode($uso_data);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Errores y Uso de la API</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }
        .chart-container {
            margin-top: 50px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Dashboard de Errores y Uso de la API</h1>

    <div class="chart-container">
        <h2>Distribución de Errores</h2>
        <canvas id="errorChart"></canvas>
    </div>

    <div class="chart-container">
        <h2>Frecuencia de Uso Diario</h2>
        <canvas id="usoChart"></canvas>
    </div>
</div>

<script>
    // Datos de errores y uso proporcionados desde el backend en JSON
    const errorData = <?php echo $error_data_json; ?>;
    const usoData = <?php echo $uso_data_json; ?>;

    // Gráfico de errores
    const errorLabels = Object.keys(errorData);
    const errorValues = Object.values(errorData);
    new Chart(document.getElementById('errorChart'), {
        type: 'bar',
        data: {
            labels: errorLabels,
            datasets: [{
                label: 'Cantidad de Errores',
                data: errorValues,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de uso diario
    const usoLabels = usoData.map(item => item.fecha);
    const usoValues = usoData.map(item => item.cantidad);
    new Chart(document.getElementById('usoChart'), {
        type: 'line',
        data: {
            labels: usoLabels,
            datasets: [{
                label: 'Uso Diario de la API',
                data: usoValues,
                fill: false,
                borderColor: 'rgba(54, 162, 235, 1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
