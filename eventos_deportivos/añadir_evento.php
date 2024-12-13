<?php include 'php/procesar.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Formulario</title>
</head>
<body>
    <?php 
        if (!isset($_GET['id'])){

        

        ?>
    <div class="m-0 row justify-content-center">
        <div class="col-auto p-5 text-center">
            <div class="card" style="width: 30rem">
                <div class="card-body ">
                    <h1>Registro</h1>
                    <form action="php/procesar.php" method="post">
                        <!--Valor hidden para diferenciar formularios-->
                        <input type="hidden" name="hidden-evento" value="hidden-evento"><br>

                        <label for="nombre_evento" class="control-label col-sm-4 text-right">Nombre del evento:</label><br>
                        <input type="text" id="nombre_evento" name="nombre_evento" required><br><br>

                        <label for="tipo_deporte" class="control-label col-sm-4 text-right">Tipo de deporte:</label><br>
                        <input type="text" id="tipo_deporte" name="tipo_deporte" required><br><br>

                        <label for="ubicacion" class="control-label col-sm-4 text-right">Ubicación:</label><br>
                        <input type="text" id="ubicacion" name="ubicacion" required><br><br>

                        <label for="fecha" class="control-label col-sm-4 text-right">Fecha:</label><br>
                        <input type="date" id="fecha" name="fecha" required><br><br>

                        <label for="hora" class="control-label col-sm-4 text-right">Hora:</label><br>
                        <input type="time" id="hora" name="hora" required><br><br>

                        <label for="organizador" class="control-label col-sm-4 text-right">organizador</label><br>
                        <select name="organizador" id="organizador">
                            <?php 
                                
                                if (!empty($organizadores)){ 
                                    foreach ($organizadores as $organizador){ 
                                        echo "<option value='{$organizador['nombre']}'>{$organizador['nombre']}</option>";
                                    }
                                }
                            ?>
                        </select><br><br>
                        
                        <input type="submit" value="Crear evento">
                    </form>
                    <?php
                        }else{
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php 
        if (!empty($resultsEventos)){ 
            foreach ($resultsEventos as $row){ 
                
    ?>
    <div class="m-0 row justify-content-center">
        <div class="col-auto p-5 text-center">
            <div class="card" style="width: 30rem">
                <div class="card-body "></div>
                    <h1 class="p-1">Editar Evento</h1>
                    <form method="POST" action="php/procesar.php?id=<?php echo $row['id']?>"> 
                        
                        <label for="nombre_evento" class="control-label col-sm-4 text-right">Nombre del evento:</label><br>
                        <input type="text" id="nombre_evento" name="nombre_evento" value="<?php echo $row['nombre_evento']; ?>" required><br><br>

                        <label for="apellido" class="control-label col-sm-4 text-right">Tipo de deporte:</label><br>
                        <input type="text" id="tipo_deporte" name="tipo_deporte" value="<?php echo $row['tipo_deporte']; ?>" required><br><br>

                        <label for="ubicacion" class="control-label col-sm-4 text-right">Ubicación:</label><br>
                        <input type="text" id="ubicacion" name="ubicacion" value="<?php echo $row['ubicacion']; ?>" required><br><br>

                        <label for="edad" class="control-label col-sm-4 text-right">Fecha:</label><br>
                        <input type="date" id="fecha" name="fecha" value="<?php echo $row['fecha']; ?>" required><br><br>

                        <label for="hora" class="control-label col-sm-4 text-right">Hora:</label><br>
                        <input type="time" id="hora" name="hora" value="<?php echo $row['hora']; ?>" required><br><br>

                            
                    <?php 
                                $id = $_GET['id'];
                                $sql = "SELECT eventos.*, organizadores.nombre AS nombre_organizador 
                                        FROM eventos 
                                        JOIN organizadores ON eventos.id_organizador = organizadores.id 
                                        WHERE eventos.id = $id";

                                $result = $conn->query($sql);
                                $nombre_organizador = '';
                                if ($result && $result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $nombre_organizador = $row['nombre_organizador'];
                                }
                            
                            ?>
                            <label for="organizador" class="control-label col-sm-4 text-right">organizador</label><br>
                            <select name="organizador" id="organizador" required>
                                <?php 
                                    if (!empty($organizadores)){ 
                                        foreach ($organizadores as $organizador){ 
                                            // Valor ternario que encuentra el organizador del evento a editar y lo coloca como seleccionado
                                            $selected = ($organizador['nombre'] == $nombre_organizador) ? 'selected="selected"' : '';
                                            echo "<option value='{$organizador['nombre']}' $selected>{$organizador['nombre']}</option>";
                                        }
                                    }
                                }
                                ?>
                            </select><br><br>
                        
                        <input type="submit" value="Editar evento"><br>
                        <a href="listado.php">Ir al listado</a>
                        <?php
                            }
                        }
                        ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>