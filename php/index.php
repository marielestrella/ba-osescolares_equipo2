<?php
// Enlaza la conexión desde la carpeta config
require_once '../config/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente de Fallas - Baños Escolares</title>
    <style>
        * { 
            box-sizing: border-box; 
            font-family: 'Courier New', Courier, monospace, system-ui; 
        }

        body { 
            background-color: #070709;
            background-image: 
                radial-gradient(circle at 15% 20%, rgba(153, 0, 0, 0.25) 0%, transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(120, 0, 0, 0.3) 0%, transparent 50%);
            margin: 0; 
            padding: 2rem; 
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Salpicaduras de sangre en el fondo */
        .sangre-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            opacity: 0.55;
        }

        /* Efecto Portaobjetos de Vidrio (Blood Slide) */
        .contenedor { 
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 600px; 
            margin: 0 auto; 
            /* Vidrio translúcido oscuro con tinte carmesí */
            background: linear-gradient(135deg, rgba(22, 22, 26, 0.78) 0%, rgba(45, 5, 10, 0.65) 100%);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 2.5rem 2rem; 
            border-radius: 6px; 
            /* Borde biselado tipo cristal de laboratorio */
            border: 1px solid rgba(220, 38, 38, 0.45);
            border-top: 2px solid rgba(255, 255, 255, 0.25);
            border-left: 2px solid rgba(255, 255, 255, 0.15);
            box-shadow: 
                0 20px 50px rgba(0, 0, 0, 0.9),
                inset 0 0 25px rgba(185, 28, 28, 0.25);
        }

        /* Gota de sangre central decorativa estilo muestra de laboratorio */
        .muestra-vidrio {
            width: 36px;
            height: 36px;
            margin: 0 auto 1rem auto;
            background: radial-gradient(circle at 35% 35%, #ef4444, #991b1b 60%, #450a0a 100%);
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            box-shadow: 0 0 15px rgba(220, 38, 38, 0.8), inset 0 2px 4px rgba(255,255,255,0.4);
            transform: rotate(-15deg);
        }

        h1 { 
            margin-top: 0; 
            color: #f8fafc; 
            font-size: 1.4rem; 
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 1px solid rgba(220, 38, 38, 0.5);
            padding-bottom: 0.8rem;
            text-shadow: 0 0 10px rgba(220, 38, 38, 0.7);
        }

        .subtitulo-forense {
            text-align: center;
            font-size: 0.75rem;
            color: #ef4444;
            letter-spacing: 3px;
            margin-top: -0.4rem;
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
            letter-spacing: 1px;
        }

        input, select, textarea { 
            width: 100%; 
            padding: 0.75rem; 
            background-color: rgba(10, 10, 12, 0.85);
            color: #f1f5f9;
            border: 1px solid rgba(153, 27, 27, 0.6); 
            border-radius: 4px; 
            font-size: 0.95rem;
            transition: all 0.25s ease;
        }

        input::placeholder, textarea::placeholder {
            color: #64748b;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #ef4444;
            box-shadow: 0 0 12px rgba(220, 38, 38, 0.45);
            background-color: rgba(18, 12, 14, 0.95);
        }

        select option {
            background-color: #111115;
            color: #f1f5f9;
        }

        button { 
            width: 100%; 
            padding: 0.9rem; 
            margin-top: 0.5rem;
            background: linear-gradient(180deg, #b91c1c 0%, #7f1d1d 100%);
            color: #ffffff; 
            border: 1px solid #ef4444; 
            border-radius: 4px; 
            font-size: 1rem; 
            font-weight: 700; 
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer; 
            box-shadow: 0 4px 15px rgba(185, 28, 28, 0.4);
            transition: all 0.25s ease;
        }

        button:hover { 
            background: linear-gradient(180deg, #dc2626 0%, #991b1b 100%);
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.75);
            transform: translateY(-1px);
        }

        button:active {
            transform: translateY(1px);
        }
    </style>
</head>
<body>
    <!-- Salpicaduras SVG estilo análisis forense -->
    <svg class="sangre-bg" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
        <g fill="#880808">
            <circle cx="180" cy="140" r="28" />
            <circle cx="220" cy="110" r="9" />
            <circle cx="150" cy="185" r="12" />
            <circle cx="245" cy="165" r="5" />
            <path d="M180 140 Q185 230 178 290 Q175 305 182 305 Q188 305 185 285 Q182 220 192 145 Z" />
            
            <circle cx="1220" cy="680" r="45" />
            <circle cx="1160" cy="630" r="14" />
            <circle cx="1280" cy="620" r="10" />
            <circle cx="1250" cy="750" r="18" />
            <circle cx="1130" cy="710" r="7" />
            <path d="M1220 680 Q1225 790 1218 880 L1226 880 Q1228 780 1232 680 Z" />

            <circle cx="1100" cy="160" r="16" />
            <circle cx="1135" cy="185" r="6" />
            <circle cx="310" cy="740" r="22" />
            <circle cx="345" cy="770" r="8" />
        </g>
    </svg>

    <div class="contenedor">
        <div class="muestra-vidrio" title="Muestra #001"></div>
        <h1>Reporte de Fallas</h1>
        <div class="subtitulo-forense">Baños Escolares // Bay Harbor Lab</div>
        
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
                <textarea id="descripcion" name="descripcion" rows="3" placeholder="Describe brevemente la evidencia de la falla..." required></textarea>
            </div>
            <button type="submit">Archivar Reporte</button>
        </form>
    </div>

    <!-- Enlaza el archivo de validaciones de la carpeta js -->
    <script src="../js/validaciones.js"></script>
</body>
</html>