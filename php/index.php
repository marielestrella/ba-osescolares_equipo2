<?php
// Enlaza la configuración y funciones desde la carpeta config
require_once '../config/conexion.php';

$mensaje = "";
$tipoMensaje = "";

// Variables para cuando se presiona "Actualizar"
$editando        = false;
$editIndex       = -1;
$editOrigen      = "";
$editUbicacion   = "";
$editTipoBano    = "Hombres";
$editTipoFalla   = "";
$editDescripcion = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = trim($_POST["accion"] ?? "enviar");

    // 1. ELIMINAR REPORTE
    if ($accion === "eliminar") {
        $origen  = $_POST["origen"] ?? "";
        $indice  = intval($_POST["indice"] ?? -1);
        $archivo = ($origen === "archivados") ? $rutaArchivados : $rutaEnviados;
        $lista   = obtenerReportes($archivo);

        if (isset($lista[$indice])) {
            unset($lista[$indice]);
            if (guardarListaReportes($archivo, $lista)) {
                $mensaje = "Reporte ELIMINADO correctamente.";
                $tipoMensaje = "exito";
            } else {
                $mensaje = "No se pudo eliminar el reporte. Verifica permisos de la carpeta.";
                $tipoMensaje = "error";
            }
        }
    }
    // 2. CARGAR DATOS AL FORMULARIO PARA ACTUALIZAR
    elseif ($accion === "cargar_edicion") {
        $origen  = $_POST["origen"] ?? "";
        $indice  = intval($_POST["indice"] ?? -1);
        $archivo = ($origen === "archivados") ? $rutaArchivados : $rutaEnviados;
        $lista   = obtenerReportes($archivo);

        if (isset($lista[$indice])) {
            $editando   = true;
            $editIndex  = $indice;
            $editOrigen = $origen;

            $lineas = explode("\n", str_replace("\r", "", $lista[$indice]));
            foreach ($lineas as $linea) {
                if (strpos($linea, "Ubicación: ") === 0)     $editUbicacion   = trim(substr($linea, 12));
                if (strpos($linea, "Tipo de baño: ") === 0)  $editTipoBano    = trim(substr($linea, 15));
                if (strpos($linea, "Tipo de falla: ") === 0) $editTipoFalla   = trim(substr($linea, 15));
                if (strpos($linea, "Descripción: ") === 0)   $editDescripcion = trim(substr($linea, 14));
            }
            $mensaje = "Editando reporte. Modifica los campos arriba y guarda los cambios.";
            $tipoMensaje = "exito";
        }
    }
    // 3. GUARDAR ACTUALIZACIÓN
    elseif ($accion === "actualizar") {
        $origen      = $_POST["origen"] ?? "";
        $indice      = intval($_POST["indice"] ?? -1);
        $ubicacion   = htmlspecialchars(trim($_POST["ubicacion"] ?? ""));
        $tipoBano    = htmlspecialchars(trim($_POST["tipo_bano"] ?? ""));
        $tipoFalla   = htmlspecialchars(trim($_POST["tipo_falla"] ?? ""));
        $descripcion = htmlspecialchars(trim($_POST["descripcion"] ?? ""));

        if (empty($ubicacion) or empty($tipoBano) or empty($tipoFalla) or empty($descripcion)) {
            $mensaje = "Por favor, completa todos los campos.";
            $tipoMensaje = "error";
        } else {
            $archivo = ($origen === "archivados") ? $rutaArchivados : $rutaEnviados;
            $lista   = obtenerReportes($archivo);

            if (isset($lista[$indice])) {
                $reporte  = "Fecha: " . date("d/m/Y H:i:s") . " (Editado)" . PHP_EOL;
                $reporte .= "Ubicación: " . $ubicacion . PHP_EOL;
                $reporte .= "Tipo de baño: " . $tipoBano . PHP_EOL;
                $reporte .= "Tipo de falla: " . $tipoFalla . PHP_EOL;
                $reporte .= "Descripción: " . $descripcion;

                $lista[$indice] = $reporte;

                if (guardarListaReportes($archivo, $lista)) {
                    $mensaje = "Reporte ACTUALIZADO correctamente.";
                    $tipoMensaje = "exito";
                } else {
                    $mensaje = "No se pudo actualizar el reporte. Verifica permisos de la carpeta.";
                    $tipoMensaje = "error";
                }
            }
        }
    }
    // 4. CREAR NUEVO REPORTE (ENVIAR O ARCHIVAR)
    else {
        $ubicacion   = htmlspecialchars(trim($_POST["ubicacion"] ?? ""));
        $tipoBano    = htmlspecialchars(trim($_POST["tipo_bano"] ?? ""));
        $tipoFalla   = htmlspecialchars(trim($_POST["tipo_falla"] ?? ""));
        $descripcion = htmlspecialchars(trim($_POST["descripcion"] ?? ""));

        if (empty($ubicacion) or empty($tipoBano) or empty($tipoFalla) or empty($descripcion)) {
            $mensaje = "Por favor, completa todos los campos.";
            $tipoMensaje = "error";
        } else {
            $reporte  = "Fecha: " . date("d/m/Y H:i:s") . PHP_EOL;
            $reporte .= "Ubicación: " . $ubicacion . PHP_EOL;
            $reporte .= "Tipo de baño: " . $tipoBano . PHP_EOL;
            $reporte .= "Tipo de falla: " . $tipoFalla . PHP_EOL;
            $reporte .= "Descripción: " . $descripcion . PHP_EOL;
            $reporte .= "--------------------------" . PHP_EOL;

            if ($accion === "archivar") {
                $archivo = $rutaArchivados;
                $textoExito = "Reporte ARCHIVADO correctamente en reportes_archivados.txt";
            } else {
                $archivo = $rutaEnviados;
                $textoExito = "Reporte ENVIADO correctamente en reportes.txt";
            }

            if (@file_put_contents($archivo, $reporte, FILE_APPEND + LOCK_EX) !== false) {
                $mensaje = $textoExito;
                $tipoMensaje = "exito";
            } else {
                $mensaje = "No se pudo guardar el reporte. Verifica permisos de la carpeta.";
                $tipoMensaje = "error";
            }
        }
    }
}

// Leer el contenido guardado y el texto de consulta (GET)
$busqueda        = trim($_GET["buscar"] ?? "");
$listaEnviados   = obtenerReportes($rutaEnviados);
$listaArchivados = obtenerReportes($rutaArchivados);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Fallas - Baños Escolares</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Courier New', Courier, monospace;
        }

        body {
            background-color: #070709;
            background-image:
                radial-gradient(circle at 15% 20%, rgba(153, 0, 0, 0.25), transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(120, 0, 0, 0.3), transparent 50%);
            margin: 0;
            padding: 2rem 1rem;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            overflow-y: auto;
        }

        .contenedor {
            width: 100%;
            max-width: 650px;
            margin: 0 auto 2rem auto;
            padding: 2.5rem 2rem;
            background: linear-gradient(135deg, rgba(22, 22, 26, 0.78), rgba(45, 5, 10, 0.65));
            backdrop-filter: blur(12px);
            border: 1px solid rgba(220, 38, 38, 0.45);
            border-radius: 6px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.9), inset 0 0 25px rgba(185, 28, 28, 0.25);
        }

        .muestra-vidrio {
            width: 36px;
            height: 36px;
            margin: 0 auto 1rem;
            background: radial-gradient(circle at 35% 35%, #ef4444, #991b1b 60%, #450a0a);
            border-radius: 50%;
            box-shadow: 0 0 15px rgba(220, 38, 38, 0.8);
        }

        h1, h2 {
            color: #f8fafc;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 1px solid rgba(220, 38, 38, 0.5);
            padding-bottom: 0.8rem;
        }

        h1 { font-size: 1.4rem; }
        h2 { font-size: 1.1rem; margin-top: 0; }

        .subtitulo-forense {
            text-align: center;
            font-size: 0.75rem;
            color: #ef4444;
            letter-spacing: 3px;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
        }

        .campo {
            margin-bottom: 1.2rem;
        }

        label {
            display: block;
            font-weight: 700;
            margin-bottom: 0.4rem;
            color: #fca5a5;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.75rem;
            background-color: rgba(10, 10, 12, 0.85);
            color: #f1f5f9;
            border: 1px solid rgba(153, 27, 27, 0.6);
            border-radius: 4px;
            font-size: 0.95rem;
        }

        textarea {
            resize: vertical;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #ef4444;
            box-shadow: 0 0 12px rgba(220, 38, 38, 0.45);
        }

        .grupo-botones {
            margin-top: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        button {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(180deg, #b91c1c, #7f1d1d);
            color: white;
            border: 1px solid #ef4444;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
        }

        button:hover {
            background: linear-gradient(180deg, #dc2626, #991b1b);
        }

        .mensaje {
            padding: 12px;
            margin-bottom: 15px;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
        }

        .exito {
            background: #14532d;
            color: #bbf7d0;
            border: 1px solid #22c55e;
        }

        .error {
            background: #7f1d1d;
            color: #fecaca;
            border: 1px solid #ef4444;
        }

        .caja-registros {
            background-color: rgba(10, 10, 12, 0.9);
            border: 1px solid rgba(153, 27, 27, 0.6);
            border-radius: 4px;
            padding: 1rem;
            max-height: 250px;
            overflow-y: auto;
            white-space: pre-wrap;
            font-size: 0.85rem;
            color: #cbd5e1;
            line-height: 1.5;
        }

        .item-reporte {
            border-bottom: 1px dashed rgba(220, 38, 38, 0.4);
            padding-bottom: 0.8rem;
            margin-bottom: 0.8rem;
        }

        .acciones-fila {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .acciones-fila form {
            flex: 1;
        }

        .btn-chico {
            padding: 0.45rem;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }

        .vacio {
            color: #64748b;
            font-style: italic;
            text-align: center;
        }

        @media (max-width: 500px) {
            body { padding: 1rem; }
            .contenedor { padding: 1.5rem 1rem; }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="muestra-vidrio"></div>
    <h1>Reporte de Fallas</h1>
    <div class="subtitulo-forense">Baños Escolares // Bay Harbor Lab</div>

    <?php if (!empty($mensaje)): ?>
        <div class="mensaje <?php echo $tipoMensaje; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <form id="formReporte" method="POST">
        <input type="hidden" id="accion" name="accion" value="<?php echo $editando ? 'actualizar' : 'enviar'; ?>">
        <?php if ($editando): ?>
            <input type="hidden" name="indice" value="<?php echo $editIndex; ?>">
            <input type="hidden" name="origen" value="<?php echo htmlspecialchars($editOrigen); ?>">
        <?php endif; ?>

        <div class="campo">
            <label for="ubicacion">Ubicación / Edificio:</label>
            <input
                type="text"
                id="ubicacion"
                name="ubicacion"
                placeholder="Ej. Edificio F planta baja"
                value="<?php echo htmlspecialchars($editUbicacion); ?>"
                required
            >
        </div>

        <div class="campo">
            <label for="tipo_bano">Tipo de baño:</label>
            <select id="tipo_bano" name="tipo_bano" required>
                <option value="Hombres" <?php echo ($editTipoBano === "Hombres") ? "selected" : ""; ?>>Hombres</option>
                <option value="Mujeres" <?php echo ($editTipoBano === "Mujeres") ? "selected" : ""; ?>>Mujeres</option>
                <option value="Mixto / Docentes" <?php echo ($editTipoBano === "Mixto / Docentes") ? "selected" : ""; ?>>Mixto / Docentes</option>
            </select>
        </div>

        <div class="campo">
            <label for="tipo_falla">Tipo de falla:</label>
            <select id="tipo_falla" name="tipo_falla" required>
                <option value="" disabled <?php echo empty($editTipoFalla) ? "selected" : ""; ?>>Selecciona el problema...</option>
                <option value="Fuga de agua" <?php echo ($editTipoFalla === "Fuga de agua") ? "selected" : ""; ?>>Fuga de agua</option>
                <option value="Inodoro obstruido" <?php echo ($editTipoFalla === "Inodoro obstruido") ? "selected" : ""; ?>>Inodoro obstruido</option>
                <option value="Lavabo dañado" <?php echo ($editTipoFalla === "Lavabo dañado") ? "selected" : ""; ?>>Lavabo dañado</option>
                <option value="Falta de insumos" <?php echo ($editTipoFalla === "Falta de insumos") ? "selected" : ""; ?>>Falta de insumos</option>
                <option value="Iluminación o puerta dañada" <?php echo ($editTipoFalla === "Iluminación o puerta dañada") ? "selected" : ""; ?>>Iluminación o puerta dañada</option>
                <option value="Otro" <?php echo ($editTipoFalla === "Otro") ? "selected" : ""; ?>>Otro</option>
            </select>
        </div>

        <div class="campo">
            <label for="descripcion">Descripción detallada:</label>
            <textarea
                id="descripcion"
                name="descripcion"
                rows="4"
                placeholder="Describe brevemente la evidencia de la falla..."
                required
            ><?php echo htmlspecialchars($editDescripcion); ?></textarea>
        </div>

        <div class="grupo-botones">
            <?php if ($editando): ?>
                <button type="submit">Guardar Cambios</button>
                <button type="button" onclick="window.location.href=window.location.pathname">Cancelar</button>
            <?php else: ?>
                <button type="submit" onclick="seleccionarAccion('enviar')">
                    Enviar Reporte
                </button>

                <button type="submit" onclick="seleccionarAccion('archivar')">
                    Archivar Reporte
                </button>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- SECCIÓN PARA CONSULTAR, ACTUALIZAR Y ELIMINAR LOS REPORTES -->
<div class="contenedor">
    <h2>Bitácora de Evidencias Guardadas</h2>

    <form method="GET" class="campo">
        <label for="buscar">Consultar reporte:</label>
        <div class="acciones-fila">
            <input
                type="text"
                id="buscar"
                name="buscar"
                placeholder="Buscar edificio, falla o fecha..."
                value="<?php echo htmlspecialchars($busqueda); ?>"
            >
            <button type="submit" class="btn-chico" style="max-width: 140px;">Consultar</button>
            <?php if (!empty($busqueda)): ?>
                <button type="button" class="btn-chico" style="max-width: 110px;" onclick="window.location.href=window.location.pathname">Limpiar</button>
            <?php endif; ?>
        </div>
    </form>

    <div class="campo">
        <label>Reportes Enviados (reportes.txt):</label>
        <div class="caja-registros"><?php
            $hayEnv = false;
            foreach ($listaEnviados as $indice => $rep):
                if (!empty($busqueda) && stripos($rep, $busqueda) === false) continue;
                $hayEnv = true;
            ?><div class="item-reporte"><?php echo htmlspecialchars($rep); ?><div class="acciones-fila">
                    <form method="POST">
                        <input type="hidden" name="accion" value="cargar_edicion">
                        <input type="hidden" name="origen" value="enviados">
                        <input type="hidden" name="indice" value="<?php echo $indice; ?>">
                        <button type="submit" class="btn-chico">Actualizar</button>
                    </form>
                    <form method="POST" onsubmit="return confirmarEliminacion('enviado');">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="origen" value="enviados">
                        <input type="hidden" name="indice" value="<?php echo $indice; ?>">
                        <button type="submit" class="btn-chico">Eliminar</button>
                    </form>
                </div></div><?php
            endforeach;
            if (!$hayEnv) echo '<div class="vacio">Aún no hay reportes enviados.</div>';
        ?></div>
    </div>

    <div class="campo">
        <label>Reportes Archivados (reportes_archivados.txt):</label>
        <div class="caja-registros"><?php
            $hayArc = false;
            foreach ($listaArchivados as $indice => $rep):
                if (!empty($busqueda) && stripos($rep, $busqueda) === false) continue;
                $hayArc = true;
            ?><div class="item-reporte"><?php echo htmlspecialchars($rep); ?><div class="acciones-fila">
                    <form method="POST">
                        <input type="hidden" name="accion" value="cargar_edicion">
                        <input type="hidden" name="origen" value="archivados">
                        <input type="hidden" name="indice" value="<?php echo $indice; ?>">
                        <button type="submit" class="btn-chico">Actualizar</button>
                    </form>
                    <form method="POST" onsubmit="return confirmarEliminacion('archivado');">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="origen" value="archivados">
                        <input type="hidden" name="indice" value="<?php echo $indice; ?>">
                        <button type="submit" class="btn-chico">Eliminar</button>
                    </form>
                </div></div><?php
            endforeach;
            if (!$hayArc) echo '<div class="vacio">Aún no hay reportes archivados.</div>';
        ?></div>
    </div>
</div>

<!-- Enlaza las validaciones desde la carpeta js -->
<script src="../js/validaciones.js"></script>
</body>
</html>