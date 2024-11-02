<?php
require('../pdf/fpdf186/fpdf.php'); // Ajusta la ruta según corresponda

// Iniciar la sesión y verificar autenticación
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Obtener el ID del usuario logueado
$usuario_id = $_SESSION['usuario_id'];

// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'migraciones_pg2');
if ($conexion->connect_error) {
    die("Conexion fallida: " . $conexion->connect_error);
}

$conexion->set_charset("utf8"); // Configurar UTF-8

// Consulta para obtener los datos de migraciones del usuario logueado
$sql = "
    SELECT 
        m.id AS migracion_id, 
        COALESCE(ac.nombre_archivo, 'Sin archivo') AS archivo_origen, 
        m.estado, 
        m.fecha_inicio, 
        m.fecha_fin, 
        TIMESTAMPDIFF(SECOND, m.fecha_inicio, m.fecha_fin) AS duracion,
        DATE(m.fecha_inicio) AS fecha
    FROM 
        migraciones m
    LEFT JOIN 
        archivos_cargados ac ON ac.id = m.archivo_origen
    WHERE 
        m.user_id = ?
    ORDER BY 
        m.fecha_inicio DESC
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

// Obtener estadísticas de uso
$total_migraciones = $result->num_rows;
$total_duracion = 0;
$migraciones_exitosas = 0;
$migraciones_errores = 0;
$frecuencia_migraciones = []; // Almacenar frecuencia diaria de migraciones

while ($row = $result->fetch_assoc()) {
    $total_duracion += $row['duracion'];
    $fecha = $row['fecha'];
    if (!isset($frecuencia_migraciones[$fecha])) {
        $frecuencia_migraciones[$fecha] = 0;
    }
    $frecuencia_migraciones[$fecha]++;
    
    if ($row['estado'] === 'Completada') {
        $migraciones_exitosas++;
    } else {
        $migraciones_errores++;
    }
}

$duracion_promedio = $total_migraciones > 0 ? round($total_duracion / $total_migraciones, 2) : 0;
$porcentaje_exitosas = $total_migraciones > 0 ? round(($migraciones_exitosas / $total_migraciones) * 100, 2) : 0;
$porcentaje_errores = 100 - $porcentaje_exitosas;

// Resetear el puntero de resultados
$result->data_seek(0);

// Verificar si el usuario solicitó descargar el PDF
if (isset($_POST['descargar_pdf'])) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(190, 10, utf8_decode('Reporte de Uso de Migraciones por Usuario'), 0, 1, 'C');
    $pdf->Ln(10);

    // Agregar estadísticas de uso
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 10, 'Estadisticas Generales:', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(190, 8, 'Total de Migraciones: ' . $total_migraciones, 0, 1, 'L');
    $pdf->Cell(190, 8, 'Duracion Promedio de Migracion (s): ' . $duracion_promedio, 0, 1, 'L');
    $pdf->Cell(190, 8, 'Migraciones Exitosas: ' . $migraciones_exitosas . ' (' . $porcentaje_exitosas . '%)', 0, 1, 'L');
    $pdf->Cell(190, 8, 'Migraciones con Errores: ' . $migraciones_errores . ' (' . $porcentaje_errores . '%)', 0, 1, 'L');
    $pdf->Ln(10);

    // Gráfico de frecuencia de migraciones
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 10, 'Frecuencia de Migraciones (por Dia):', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    foreach ($frecuencia_migraciones as $fecha => $cantidad) {
        $pdf->Cell(190, 8, utf8_decode($fecha) . ': ' . $cantidad . ' migraciones', 0, 1, 'L');
    }
    $pdf->Ln(10);

    // Detalles de las migraciones
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 10, 'Detalle de Migraciones', 0, 1, 'L');
    $pdf->Ln(5);

    // Encabezados de la tabla
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Archivo Origen', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Estado', 1, 0, 'C', true);
    $pdf->Cell(35, 10, 'Fecha Inicio', 1, 0, 'C', true);
    $pdf->Cell(35, 10, 'Fecha Fin', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Duracion (s)', 1, 1, 'C', true);

    // Rellenar los datos de la tabla
    while ($row = $result->fetch_assoc()) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(20, 10, $row['migracion_id'], 1);
        $pdf->Cell(40, 10, utf8_decode($row['archivo_origen']), 1);
        $pdf->Cell(30, 10, utf8_decode($row['estado']), 1);
        $pdf->Cell(35, 10, $row['fecha_inicio'], 1);
        $pdf->Cell(35, 10, $row['fecha_fin'], 1);
        $pdf->Cell(30, 10, $row['duracion'], 1, 1);

        $pdf->Ln(3);
    }

    // Salida del PDF para descarga
    $pdf->Output('D', 'Reporte_Uso_Migraciones.pdf');
    exit();
}
?>
