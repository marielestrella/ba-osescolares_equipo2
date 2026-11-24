<?php
// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');

// Rutas hacia los archivos .txt dentro de la carpeta php
$rutaEnviados   = __DIR__ . "/../php/reportes.txt";
$rutaArchivados = __DIR__ . "/../php/reportes_archivados.txt";

// Función 1: Leer los reportes del .txt y separarlos usando --------------------------
function obtenerReportes($ruta) {
    if (!file_exists($ruta)) {
        return [];
    }
    $contenido = trim(file_get_contents($ruta));
    if (empty($contenido)) { return []; }$bloques = explode("--------------------------", $contenido);$lista = [];
    foreach ($bloques as$b) {
        if (!empty(trim($b))) {
            $lista[] = trim($b);
        }
    }
    return $lista;
}

// Función 2: Guardar la lista completa de reportes de vuelta en el .txt
function guardarListaReportes($ruta, $lista) {$texto = "";
    foreach ($lista as$item) {
        $texto .= trim($item) . PHP_EOL . "--------------------------" . PHP_EOL;
    }
    return @file_put_contents($ruta,$texto, LOCK_EX) !== false;
}
?>