<?php
// Ruta del archivo de auditoría generado por el sistema Python
$archivo = "auditoria.txt";

// Arreglo donde se almacenarán las líneas del archivo
$lineas = [];

// Verifica si el archivo existe antes de leerlo
if (file_exists($archivo)) {
    // Lee el archivo línea por línea y lo guarda en un arreglo
    $lineas = file($archivo);

    // Invierte el orden para mostrar primero los registros más recientes
    $lineas = array_reverse($lineas);
}

// Verifica si se presionó el botón de limpiar historial
if (isset($_POST['limpiar'])) {
    // Vacía el contenido del archivo
    file_put_contents($archivo, "");

    // Recarga la página
    header("Refresh:0");
}

// Inicializa contadores
$total = count($lineas);
$permitidos = 0;
$denegados = 0;

// Recorre cada línea para contar accesos
foreach ($lineas as $linea) {
    if (strpos($linea, "DENEGADO") !== false) {
        $denegados++;
    } else {
        $permitidos++;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard Control de Acceso</title>

<!-- Recarga automática cada 5 segundos -->
<meta http-equiv="refresh" content="5">

<style>
/* Estilo general del sistema */
body {
    font-family: 'Segoe UI', sans-serif;
    background: #0f172a;
    color: white;
    margin: 0;
}

/* Encabezado */
header {
    background: #020617;
    padding: 20px;
    text-align: center;
    font-size: 24px;
}

/* Animación para eventos críticos */
.alerta {
    animation: parpadeo 1s infinite;
}

@keyframes parpadeo {
    0% { background-color: #7f1d1d; }
    50% { background-color: #b91c1c; }
    100% { background-color: #7f1d1d; }
}

/* Contenedor principal */
.container {
    padding: 20px;
}

/* Sección de estadísticas */
.stats {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 20px;
}

/* Tarjetas de estadísticas */
.card {
    padding: 15px;
    border-radius: 10px;
    background: #1e293b;
}

.ok { background: #14532d; }
.bad { background: #7f1d1d; }

/* Botón */
button {
    padding: 10px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    background: #334155;
    color: white;
}

button:hover {
    background: #475569;
}

/* Campo de búsqueda */
input {
    padding: 10px;
    border-radius: 8px;
    border: none;
}

/* Tabla */
table {
    width: 100%;
    border-collapse: collapse;
    background: #1e293b;
}

th, td {
    padding: 10px;
    text-align: center;
}

/* Estilos según tipo de acceso */
.denegado { background: #7f1d1d; }
.permitido { background: #14532d; }

/* Resalta el evento más reciente */
.ultimo { border: 3px solid yellow; }
</style>

<script>
// Función para filtrar resultados en tiempo real
function filtrar() {
    let input = document.getElementById("buscador").value.toLowerCase();
    let filas = document.querySelectorAll("tbody tr");

    // Recorre cada fila y compara el texto con el filtro
    filas.forEach(fila => {
        let texto = fila.innerText.toLowerCase();
        fila.style.display = texto.includes(input) ? "" : "none";
    });
}
</script>
</head>

<body>

<header>CONTROL DE ACCESOS</header>

<div class="container">

<!-- Muestra estadísticas generales -->
<div class="stats">
    <div class="card">Total: <?php echo $total; ?></div>
    <div class="card ok">Permitidos: <?php echo $permitidos; ?></div>
    <div class="card bad">Denegados: <?php echo $denegados; ?></div>
</div>

<!-- Botón para limpiar historial -->
<form method="POST" style="text-align:center;">
    <button name="limpiar">Limpiar historial</button>
</form>

<!-- Campo de búsqueda -->
<div style="text-align:center; margin:10px;">
    <input type="text" id="buscador" onkeyup="filtrar()" placeholder="Buscar por ID o nombre...">
</div>

<table>
<thead>
<tr>
<th>Fecha</th>
<th>Estado</th>
<th>ID</th>
<th>Usuario</th>
<th>Detalle</th>
</tr>
</thead>

<tbody>
<?php foreach ($lineas as $index => $linea): 

// Divide la línea en partes usando " - " como separador
$partes = explode(" - ", $linea);

// Asigna valores con validación para evitar errores
$fecha = $partes[0] ?? "";
$estado = $partes[1] ?? "";
$id = str_replace("ID: ", "", $partes[2] ?? "");
$nombre = $partes[3] ?? "";
$detalle = $partes[4] ?? "";

// Si no hay nombre válido, se muestra "Desconocido"
if (empty(trim($nombre)) || (strpos($linea, "DENEGADO") !== false && count($partes) < 4)) {
    $nombre = "Desconocido";
}

// Determina la clase visual según el tipo de acceso
$clase = (strpos($linea, "DENEGADO") !== false) ? "denegado" : "permitido";
?>

<tr class="<?php echo $clase; ?> <?php echo $index == 0 ? 'ultimo' : ''; ?> <?php echo ($index == 0 && $clase == 'denegado') ? 'alerta' : ''; ?>">
<td><?php echo $fecha; ?></td>
<td><?php echo $estado; ?></td>
<td><?php echo $id; ?></td>
<td><?php echo $nombre; ?></td>
<td><?php echo $detalle; ?></td>
</tr>

<?php endforeach; ?>
</tbody>
</table>

</div>

</body>
</html>