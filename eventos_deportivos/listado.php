<?php include 'php/procesar.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<h1 class="text-center">Listado de eventos</h1>
<div class="container mt-3">
    <!-- Contenedor de búsqueda y carga masiva -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Formulario de búsqueda -->
        <form method="GET" class="d-flex flex-grow-1 me-3">
            <input type="text" name="filtro_nombre" class="form-control" placeholder="Buscar por nombre" 
                   value="<?php echo isset($_GET['filtro_nombre']) ? htmlspecialchars($_GET['filtro_nombre']) : ''; ?>">
            <button type="submit" class="btn btn-primary ms-2">Buscar</button>
        </form>

        <!-- Formulario de carga masiva -->
        <form method="POST" enctype="multipart/form-data" class="d-flex">
            <div class="input-group" style="max-width: 300px;">
                <input type="file" class="form-control" id="archivo_csv" name="archivo_csv" accept=".csv" required>
                <button type="submit" class="btn btn-success">Cargar</button>
            </div>
        </form>
    </div>

    <!-- Tabla de lista de eventos -->
    <table class="table table-hover">
        <thead class="table-info">
            <tr>
                <th><a href="?orden=nombre_evento&direccion=<?php echo $direccionInvertida; ?>">Nombre del evento</a></th>
                <th><a href="?orden=tipo_deporte&direccion=<?php echo $direccionInvertida; ?>">Tipo de deporte</a></th>
                <th><a href="?orden=fecha&direccion=<?php echo $direccionInvertida; ?>">Fecha</a></th>
                <th>Hora</th>
                <th>Ubicación</th>
                <th>Organizador</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            
            <?php 
                if (!empty($resultsEventos)){ 
                    foreach ($resultsEventos as $row){ 
            echo "<tr>
                <td> {$row['nombre_evento']} </td>
                <td> {$row['tipo_deporte']} </td>
                <td> {$row['fecha']} </td>
                <td> {$row['hora']} </td>
                <td> {$row['ubicacion']} </td>
                <td> {$row['nombre_organizador']} </td>
                <td>
                    <a href='añadir_evento.php?id={$row['id']}' class='btn btn-primary'>Editar</a>
                    <a href='php/procesar.php?id={$row['id']}&action=borrar_evento' class='btn btn-danger'>Eliminar</a>
                </td>
            </tr>";
                    }
                }
            ?>
        </tbody>
    </table>
</div>

<nav>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                <a class="page-link" href="?pagina=<?php echo $i; ?>&orden=<?php echo $orden; ?>&direccion=<?php echo $direccion; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<br>
<h1 class="text-center">Listado de organizadores</h1><br>
<div class="text-center">
    <a href="añadir_organizador.html" class="btn btn-lg btn-primary">Añadir nuevo organizador</a><br><br>
</div>
<table class="table table-hover table-bordered">
    <thead class="table-info">
        <tr>

            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            
        </tr>
    </thead>
    <tbody>
    <?php 
        if (!empty($organizadores)){ 
            foreach ($organizadores as $row){ 
    echo "<tr>

        <td> {$row['nombre']} </td>
        <td> {$row['email']} </td>
        <td> {$row['telefono']} </td>
        <td>
                                <a href=\"php/procesar.php?id=" . $row['id'] . "&action=borrar_organizador\" class=\"btn btn-danger\" onclick=\"return confirmar()\">Eliminar</a>
        </td>
    </tr>";
            }
        }
    ?>
    </tbody>
    
</table>
<!--Advertencias de eliminación de eventos y organizadores-->
<script>
        function confirmar() {
               return confirm("¿Estas seguro de que quieres hacer eso?");
        }

         function confirmarEvento() {
             return confirm("¿Estas seguro de que quieres eliminar este evento?");
        }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
