<?php

// Incluir la configuración de la base de datos una sola vez
require_once __DIR__ . '/../../config/database.php';

/**
 * Establece y devuelve una conexión PDO a la base de datos.
 * 
 * @return PDO|null Una instancia de PDO si la conexión es exitosa, null en caso de error.
 */
function get_pdo_connection() {
    static $pdo = null; // Variable estática para mantener la conexión (Singleton pattern simple)
    global $pdo_options; // Hacer $pdo_options accesible globalmente

    if ($pdo === null) {
        try {
            // Utiliza las constantes DSN y $pdo_options definidas en config/database.php
            $pdo = new PDO(DSN, DB_USER, DB_PASS, $pdo_options);
        } catch (PDOException $e) {
            // En un entorno de producción, loguearías este error en lugar de mostrarlo.
            // Por ahora, para desarrollo, podemos mostrarlo.
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            // Podrías optar por lanzar la excepción o manejarla de otra forma:
            // throw new PDOException($e->getMessage(), (int)$e->getCode());
            // O simplemente mostrar un mensaje amigable y terminar:
            // die("Error de conexión. Por favor, inténtelo más tarde.");
            return null; // O manejar el error como prefieras
        }
    }
    return $pdo;
}

?>
