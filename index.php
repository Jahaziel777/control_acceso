<?php
$archivo = "auditoria.txt";
$lineas = [];

// Leer archivo
if (file_exists($archivo)) {
    $lineas = file($archivo);
    $lineas = array_reverse($lineas);
}

// Limpiar historial
if (isset($_POST['limpiar'])) {
    file_put_contents($archivo, "");
    header("Refresh:0");
}

// Contadores
$total = count($lineas);
$permitidos = 0;
$denegados = 0;

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

<meta http-equiv="refresh" content="5">

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #0f172a;
    color: white;
    margin: 0;
}

header {
    background: #020617;
    padding: 20px;
    text-align: center;
    font-size: 24px;
}

.alerta {
    animation: parpadeo 1s infinite;
}

@keyframes parpadeo {
    0% { background-color: #7f1d1d; }
    50% { background-color: #b91c1c; }
    100% { background-color: #7f1d1d; }
}

.container {
    padding: 20px;
}

.stats {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 20px;
}

.card {
    padding: 15px;
    border-radius: 10px;
    background: #1e293b;
}

.ok { background: #14532d; }
.bad { background: #7f1d1d; }

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

input {
    padding: 10px;
    border-radius: 8px;
    border: none;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #1e293b;
}

th, td {
    padding: 10px;
    text-align: center;
}

.denegado { background: #7f1d1d; }
.permitido { background: #14532d; }

.ultimo { border: 3px solid yellow; }
</style>

<script>
function filtrar() {
    let input = document.getElementById("buscador").value.toLowerCase();
    let filas = document.querySelectorAll("tbody tr");

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

<div class="stats">
    <div class="card">Total: <?php echo $total; ?></div>
    <div class="card ok">Permitidos: <?php echo $permitidos; ?></div>
    <div class="card bad">Denegados: <?php echo $denegados; ?></div>
</div>

<form method="POST" style="text-align:center;">
    <button name="limpiar">Limpiar historial</button>
</form>

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

$partes = explode(" - ", $linea);

$fecha = $partes[0] ?? "";
$estado = $partes[1] ?? "";
$id = str_replace("ID: ", "", $partes[2] ?? "");
$nombre = $partes[3] ?? "";
$detalle = $partes[4] ?? "";

// Solo por seguridad visual mínima
if (empty(trim($nombre))) {
    $nombre = "Desconocido";
}

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