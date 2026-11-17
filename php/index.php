<?php
// Enlaza la conexión desde la carpeta config
require_once '../config/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Fallas - Baños Escolares</title>
    <style>
        * { box-sizing: border-box; font-family: system-ui, -apple-system, sans-serif; }
        body { background-color: #f4f6f9; margin: 0; padding: 2rem; color: #333; }
        .contenedor { max-width: 600px; margin: 0 auto; background: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        h1 { margin-top: 0; color: #1e3a8a; font-size: 1.5rem; text-align: center; }
        .campo { margin-bottom: 1.2rem; }
        label { display: block; font-weight: 600; margin-bottom: 0.4rem; }
        input, select, textarea { width: 100%; padding: 0.7rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; }
        button { width: 100%; padding: 0.8rem; background-color: #2563eb; color: white; border: none; border-radius: 6px; font-size: 1rem; font-weight: 600; cursor: pointer; }
        button:hover { background-color: #1d4ed8; }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Reporte de Fallas en Baños Escolares</h1>
        <form action="" method="POST">
            <div class="campo">
                <label for="ubicacion">Ubicación / Edificio:</label>
                <input type="text" id="ubicacion" name="ubicacion" placeholder="Ej. Edificio B, Planta Baja" required>
            </div>
            <div class="campo">
                <label for="tipo_bano">Tipo de baño:</label>
                <select id="tipo_bano" name="tipo_bano" required>
                    <option value="">Selecciona una opción...</option>
                    <option value="Hombres">Hombres</option>
                    <option value="Mujeres">Mujeres</option>
                    <option value="Docentes">Docentes / Mixto</option>
                </select>
            </div>
            <div class="campo">
                <label for="falla">Tipo de falla:</label>
                <select id="falla" name="falla" required>
                    <option value="">Selecciona el problema...</option>
                    <option value="Fuga de agua">Fuga de agua</option>
                    <option value="WC tapado o dañado">WC tapado o dañado</option>
                    <option value="Lavabo sin agua">Lavabo sin agua</option>
                    <option value="Falta de insumos">Falta de papel / jabón</option>
                    <option value="Puerta o cerradura">Puerta o cerradura dañada</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div class="campo">
                <label for="descripcion">Descripción detallada:</label>
                <textarea id="descripcion" name="descripcion" rows="3" placeholder="Describe brevemente la falla..." required></textarea>
            </div>
            <button type="submit">Enviar Reporte</button>
        </form>
    </div>
    <!-- Enlaza el archivo de validaciones de la carpeta js -->
    <script src="../js/validaciones.js"></script>
</body>
</html>
