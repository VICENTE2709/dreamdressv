<?php

// Configuración de la base de datos
define('DB_HOST', 'localhost'); // O la IP de tu servidor MySQL si es diferente
define('DB_USER', 'Josue');      // Tu usuario de MySQL (por defecto en Laragon es root)
define('DB_PASS', '');          // Tu contraseña de MySQL (por defecto en Laragon es vacía)
define('DB_NAME', 'dreamdressv54'); // El nombre de tu base de datos
define('DB_CHARSET', 'utf8mb4');

// Data Source Name (DSN) para PDO
define('DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET);

// Opciones de PDO (opcional, pero recomendado)
$pdo_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Activa los errores en modo excepción
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Establece el modo de fetch por defecto a asociativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Desactiva la emulación de preparaciones para mayor seguridad
];

?>