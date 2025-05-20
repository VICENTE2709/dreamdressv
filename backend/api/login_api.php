<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Permitir CORS (permisos localhost)
require_once __DIR__ . '/cors.php';
habilitarCORS();

// Cabecera JSON
header("Content-Type: application/json");

// Incluir conexión PDO
require_once __DIR__ . '/../conexion.php';
$conexion = obtenerConexion();

// Leer datos JSON enviados
$data = json_decode(file_get_contents("php://input"), true);

$usuarioEntrada = $data['usuario'] ?? null;
$contrasenaEntrada = $data['contrasena'] ?? null;

if (!$usuarioEntrada || !$contrasenaEntrada) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Faltan datos: usuario o contraseña.'
    ]);
    exit;
}

try {
    // Buscar usuario (por correo o nombre de usuario, según tu modelo)
    $stmt = $conexion->prepare("SELECT u.UsuarioID, u.NombreUsuario, u.Contrasena, u.RolID, u.Habilitado
                                FROM usuario u
                                WHERE u.NombreUsuario = ?");
    $stmt->execute([$usuarioEntrada]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || !$usuario['Habilitado']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Usuario no encontrado o deshabilitado.'
        ]);
        exit;
    }

    // Verificar contraseña (usa hashing si tienes implementado)
    if (!password_verify($contrasenaEntrada, $usuario['Contrasena'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Contraseña incorrecta.'
        ]);
        exit;
    }

    // ✅ Generar token y registrar sesión
    $token = bin2hex(random_bytes(16));
    $ip = $_SERVER['REMOTE_ADDR'];

    $stmt = $conexion->prepare("INSERT INTO sesion_usuario (UsuarioID, Token, IP_acceso, Activa)
                                VALUES (?, ?, ?, 1)");
    $stmt->execute([$usuario['UsuarioID'], $token, $ip]);

    // ✅ Devolver éxito y token
    echo json_encode([
        'status' => 'success',
        'usuario' => [
            'usuario_id' => $usuario['UsuarioID'],
            'nombre_usuario' => $usuario['NombreUsuario'],
            'rol_id' => $usuario['RolID'],
            'token' => $token
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en el servidor: ' . $e->getMessage()
    ]);
}







