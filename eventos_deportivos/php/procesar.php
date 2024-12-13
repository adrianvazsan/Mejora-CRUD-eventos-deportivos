<?php

$servername = "localhost";
$database = "eventos_deportivos";
$username = "root";
$password = "";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Procesar filtro por nombre
$filtroNombre = isset($_GET['filtro_nombre']) ? trim($_GET['filtro_nombre']) : '';
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha';
$direccion = isset($_GET['direccion']) && $_GET['direccion'] === 'desc' ? 'DESC' : 'ASC';
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$registrosPorPagina = 10;
$offset = ($pagina - 1) * $registrosPorPagina;

// Construcción de consulta con filtros y orden
$query = "SELECT eventos.*, organizadores.nombre AS nombre_organizador 
          FROM eventos 
          JOIN organizadores ON eventos.id_organizador = organizadores.id";

if (!empty($filtroNombre)) {
    $query .= " WHERE eventos.nombre_evento LIKE ?";
}

$query .= " ORDER BY $orden $direccion LIMIT ?, ?";
$stmt = $conn->prepare($query);

if (!empty($filtroNombre)) {
    $paramFiltro = "%" . $filtroNombre . "%";
    $stmt->bind_param("sii", $paramFiltro, $offset, $registrosPorPagina);
} else {
    $stmt->bind_param("ii", $offset, $registrosPorPagina);
}

$stmt->execute();
$result = $stmt->get_result();
$resultsEventos = $result->fetch_all(MYSQLI_ASSOC);

// Contar total de registros para la paginación
$totalQuery = "SELECT COUNT(*) as total FROM eventos";
if (!empty($filtroNombre)) {
    $totalQuery .= " WHERE nombre_evento LIKE ?";
    $totalStmt = $conn->prepare($totalQuery);
    $totalStmt->bind_param("s", $paramFiltro);
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
} else {
    $totalResult = $conn->query($totalQuery);
}

$totalRegistros = $totalResult->fetch_assoc()["total"] ?? 0;
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty( $_POST["hidden-evento"] ) && !isset($_GET['id'])) {
    añadirEvento();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty( $_POST["hidden-organizador"] ) && !isset($_GET['id'])) {
    añadirOrganizador();
}

if (isset($_GET['action']) && $_GET['action'] == 'borrar_evento') {
    borrarEvento();
}

if (isset($_GET['action']) && $_GET['action'] == 'borrar_organizador') {
    borrarOrganizador();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['id'])) {
    actualizarEvento();
}

// Función para añadir un organizador
function añadirOrganizador() {
    global $conn;

    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    $sql = "INSERT INTO organizadores (nombre, email, telefono) VALUES ('$nombre', '$email', '$telefono')";
    if ($conn->query($sql) === TRUE) {
        header('Location: ../listado.php');
    } else {
        echo "Error en el registro: " . $conn->error;
    }
}

// Función para añadir un evento
function añadirEvento() {
    global $conn;

    $nombre_evento = $_POST['nombre_evento'];
    $tipo_deporte = $_POST['tipo_deporte'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $ubicacion = $_POST['ubicacion'];
    $organizador = $_POST['organizador'];

    // Obtener id de organizador
    $query = "SELECT id FROM organizadores WHERE nombre = '$organizador'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();
        $id_organizador = $row['id'];

        // Insertar evento
        $sql = "INSERT INTO eventos (nombre_evento, tipo_deporte, fecha, hora, ubicacion, id_organizador) VALUES ('$nombre_evento', '$tipo_deporte', '$fecha', '$hora', '$ubicacion', '$id_organizador')";
        
        if ($conn->query($sql) === TRUE) {
            header('Location: ../listado.php');
        } else {
            echo "Error en el registro: " . $conn->error;
        }
    } else {
        echo "Error: organizador no encontrado.";
    }
}

// Función para actualizar un evento
function actualizarEvento() {
    global $conn;

    $id = $_GET['id'];
    $nombre_evento = $_POST['nombre_evento'];
    $tipo_deporte = $_POST['tipo_deporte'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $ubicacion = $_POST['ubicacion'];
    $organizador = $_POST['organizador'];

    // Obtener id de organizador
    $query = "SELECT id FROM organizadores WHERE nombre = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $organizador);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $id_organizador = $result->fetch_assoc()['id'];

        // Actualizar evento
        $sql = "UPDATE eventos SET nombre_evento = ?, tipo_deporte = ?, fecha = ?, hora = ?, ubicacion = ?, id_organizador = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssii", $nombre_evento, $tipo_deporte, $fecha, $hora, $ubicacion, $id_organizador, $id);

        if ($stmt->execute()) {
            header('Location: ../listado.php');
        } else {
            echo "Error en la actualización: " . $stmt->error;
        }
    } else {
        echo "Error: organizador no encontrado.";
    }
}

function obtenerOrganizadores() {
    global $conn;
    $sql = "SELECT * FROM organizadores"; 
    $result = $conn->query($sql);
    if (!$result) {
        echo "Error en la consulta: " . $conn->error;
        return []; 
    }
    $organizadores = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $organizadores[] = $row;
        }
    }
    return $organizadores;
}

$organizadores = obtenerOrganizadores();

function obtenerListadoEventos($filtroNombre = "", $idOrganizador = null) {
    global $conn;
    $sql = "SELECT eventos.*, organizadores.nombre AS nombre_organizador 
            FROM eventos 
            JOIN organizadores ON eventos.id_organizador = organizadores.id";

    // Construir las condiciones dinámicamente
    $condiciones = [];
    $parametros = [];
    $tipos = "";

    if (!empty($filtroNombre)) {
        $condiciones[] = "eventos.nombre_evento LIKE ?";
        $parametros[] = "%" . $filtroNombre . "%";
        $tipos .= "s";
    }

    if (!empty($idOrganizador)) {
        $condiciones[] = "organizadores.id = ?";
        $parametros[] = $idOrganizador;
        $tipos .= "i";
    }

    if (!empty($condiciones)) {
        $sql .= " WHERE " . implode(" AND ", $condiciones);
    }

    $stmt = $conn->prepare($sql);

    if (!empty($parametros)) {
        $stmt->bind_param($tipos, ...$parametros);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $listadoEventos = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $listadoEventos[] = $row;
        }
    }

    return $listadoEventos;
}



// Función para borrar un evento
function borrarEvento(){
    global $conn;
    $id = $_GET['id'];

    $sql = "DELETE FROM eventos WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header('Location: ../listado.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

function borrarOrganizador(){
    global $conn;
    $eventos = obtenerListadoEventos(true);
    $id = $_GET['id'];
    if (sinEventos($id, $eventos)==0){
        echo"<script>alert('No se puede eliminar a un organizador con eventos a su nombre'); window.location.href='../listado.php';</script>";
    }else{
        $sql = "DELETE FROM organizadores WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            header('Location: ../listado.php');
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}


function sinEventos($id, $resultsEventos) {
    if (!empty($resultsEventos)){  
        
        foreach ($resultsEventos as $row){ 
            if ($id === $row['id_organizador']){  
                return 0;  
            }
        } 
    } 
    return 1; 
}

$direccionInvertida = $direccion === 'ASC' ? 'desc' : 'asc';

?>
