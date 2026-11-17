<?php
$host = "localhost";
$basedatos = "bd_banos_escolares"; // Cambia esto por el nombre real de tu BD en phpMyAdmin
$usuario = "root";                 // O el usuario que te hayan indicado (ej. labuser)
$pass = "";                        // O la contraseña de tu servidor (ej. passwd123)

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$basedatos;charset=utf8mb4",
        $usuario,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $error) {
    echo "Error de conexion: " . $error->getMessage() . "<br>";
    die("No fue posible conectar a la base de datos.");
}