<?php
include 'php/procesar.php'; // Conexión a la base de datos y funciones comunes

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $procesados = 0;
    $insertados = 0;
    $rechazados = 0;
    $errores = [];

    // Validar que se subió un archivo
    if (isset($_FILES['archivo_csv']) && $_FILES['archivo_csv']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['archivo_csv']['tmp_name'];
        $lineas = file($archivo); // Leer las líneas del archivo CSV

        foreach ($lineas as $numeroLinea => $linea) {
            $procesados++;
            $datos = str_getcsv($linea);

            // Validar estructura (Ajustar según las columnas del CSV)
            if (count($datos) < 6) {
                $rechazados++;
                $errores[] = "Línea $numeroLinea: Estructura inválida.";
                continue;
            }

            list($nombre, $tipoDeporte, $fecha, $hora, $ubicacion, $organizador) = $datos;

            // Validar datos (ejemplo de validaciones básicas)
            if (empty($nombre) || empty($tipoDeporte) || empty($fecha) || empty($hora) || empty($ubicacion) || empty($organizador)) {
                $rechazados++;
                $errores[] = "Línea $numeroLinea: Campos vacíos.";
                continue;
            }

            if (!DateTime::createFromFormat('Y-m-d', $fecha)) {
                $rechazados++;
                $errores[] = "Línea $numeroLinea: Fecha inválida ($fecha).";
                continue;
            }

            // Insertar en la base de datos
            $sql = "INSERT INTO eventos (nombre_evento, tipo_deporte, fecha, hora, ubicacion, nombre_organizador) 
                    VALUES (:nombre, :tipo_deporte, :fecha, :hora, :ubicacion, :organizador)";
            $stmt = $pdo->prepare($sql);
            $resultado = $stmt->execute([
                ':nombre' => $nombre,
                ':tipo_deporte' => $tipoDeporte,
                ':fecha' => $fecha,
                ':hora' => $hora,
                ':ubicacion' => $ubicacion,
                ':organizador' => $organizador,
            ]);

            if ($resultado) {
                $insertados++;
            } else {
                $rechazados++;
                $errores[] = "Línea $numeroLinea: Error al insertar en la base de datos.";
            }
        }
    } else {
        $errores[] = "Error al cargar el archivo.";
    }
}
?>
