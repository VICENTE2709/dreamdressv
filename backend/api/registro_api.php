<?php
// Activar errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/cors.php'; // Permitir CORS (permisos localhost)
habilitarCORS();

// Cabecera JSON
header("Content-Type: application/json");

// Incluir conexión
require_once __DIR__ . '/../conexion.php';
$conexion = obtenerConexion();

// Leer datos JSON
$datos = json_decode(file_get_contents("php://input"), true);

// Verificar que el JSON sea válido
if (!$datos || !is_array($datos)) {
    echo json_encode(['status' => 'error', 'message' => 'Error al recibir los datos del formulario.']);
    exit;
}

// Validar que todas las claves estén presentes
$campos = ['nombre', 'ap_paterno', 'ap_materno', 'ci', 'fecha_nacimiento', 'email', 'telefono', 'ciudad', 'zona', 'calle', 'puerta', 'nombre_usuario', 'contrasena'];

foreach ($campos as $campo) {
    if (!array_key_exists($campo, $datos)) {
        echo json_encode(['status' => 'error', 'message' => "Falta el campo: $campo"]);
        exit;
    }
}

// Capturar valores
$nombre = trim($datos['nombre']);
$ap_paterno = trim($datos['ap_paterno']);
$ap_materno = trim($datos['ap_materno']);
$ci = trim($datos['ci']);
$fecha_nacimiento = trim($datos['fecha_nacimiento']);
$email = trim($datos['email']);
$telefono = trim($datos['telefono']);
$ciudad = strtoupper(trim($datos['ciudad']));
$zona = trim($datos['zona']);
$calle = trim($datos['calle']);
$nro_puerta = trim($datos['puerta']);
$nombre_usuario = trim($datos['nombre_usuario']);
$contrasena = trim($datos['contrasena']);

// Función para detectar secuencias inválidas
function tienePatronesInvalidos($cadena) {
    return preg_match('/[aeiou]{3,}/i', $cadena) || preg_match('/[^aeiou\s\d]{4,}/i', $cadena);
}

// Validar nombre y apellidos
foreach ([$nombre, $ap_paterno, $ap_materno] as $campo) {
    if (tienePatronesInvalidos($campo)) {
        echo json_encode(['status' => 'error', 'message' => 'Nombre o apellido contiene secuencia inválida.']);
        exit;
    }
}

try {
    // Validar CI duplicado
    $stmt_ci = $conexion->prepare("SELECT 1 FROM usuario WHERE CI = :ci");
    $stmt_ci->execute(['ci' => $ci]);
    if ($stmt_ci->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El CI ya está registrado.']);
        exit;
    }

    // Validar nombre de usuario duplicado
    $stmt_usuario = $conexion->prepare("SELECT 1 FROM usuario WHERE NombreUsuario = :usuario");
    $stmt_usuario->execute(['usuario' => $nombre_usuario]);
    if ($stmt_usuario->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El nombre de usuario ya está en uso.']);
        exit;
    }

    // Validar fecha de nacimiento
    $fecha_nac = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
    $hoy = new DateTime();
    if (!$fecha_nac || $fecha_nac > $hoy || $fecha_nac->diff($hoy)->y > 90) {
        echo json_encode(['status' => 'error', 'message' => 'Fecha de nacimiento inválida o supera los 90 años.']);
        exit;
    }

    // Validar correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'El correo electrónico no es válido.']);
        exit;
    }

    // Validar correo duplicado
    $stmt_email = $conexion->prepare("SELECT 1 FROM usuario WHERE Correo = :email");
    $stmt_email->execute(['email' => $email]);
    if ($stmt_email->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El correo ya está registrado.']);
        exit;
    }

    // Validar teléfono
    if (!preg_match('/^[67][0-9]{7}$/', $telefono)) {
        echo json_encode(['status' => 'error', 'message' => 'El teléfono debe tener 8 dígitos y comenzar con 6 o 7.']);
        exit;
    }

    // Validar nombre de usuario (longitud)
    if (strlen($nombre_usuario) < 6 || strlen($nombre_usuario) > 15) {
        echo json_encode(['status' => 'error', 'message' => 'El nombre de usuario debe tener entre 6 y 15 caracteres.']);
        exit;
    }

    // Validar contraseña fuerte
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $contrasena)) {
        echo json_encode(['status' => 'error', 'message' => 'La contraseña debe tener al menos 8 caracteres, incluyendo una mayúscula, una minúscula y un número.']);
        exit;
    }

    // Validar zona y calle
    foreach ([$zona, $calle] as $campo) {
        if (tienePatronesInvalidos($campo)) {
            echo json_encode(['status' => 'error', 'message' => 'Zona o calle contiene secuencia inválida.']);
            exit;
        }
    }

    // Validar número de puerta
    if (!preg_match('/^\d{1,4}$/', $nro_puerta)) {
        echo json_encode(['status' => 'error', 'message' => 'El número de puerta debe tener hasta 4 dígitos.']);
        exit;
    }

    // Validar ciudad
    $ciudades_validas = ['LA PAZ', 'EL ALTO', 'COCHABAMBA', 'SANTA CRUZ', 'TARIJA', 'BENI', 'PANDO', 'ORURO', 'POTOSI', 'CHUQUISACA'];
    if (!in_array($ciudad, $ciudades_validas)) {
        echo json_encode(['status' => 'error', 'message' => 'Ciudad inválida.']);
        exit;
    }

    // Cifrar contraseña
    $contrasena_segura = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar usuario
    $stmt = $conexion->prepare("
        INSERT INTO usuario 
        (Nombre, Ap_paterno, Ap_materno, CI, Fecha_nacimiento, Correo, Telefono, 
         NombreUsuario, Contrasena, Ciudad, Zona, Calle, Nro_puerta, Habilitado, RolID)
        VALUES 
        (:nombre, :ap_paterno, :ap_materno, :ci, :fecha_nacimiento, :correo, :telefono, 
         :nombre_usuario, :contrasena, :ciudad, :zona, :calle, :nro_puerta, 1, 5)
    ");

    $stmt->execute([
        'nombre' => $nombre,
        'ap_paterno' => $ap_paterno,
        'ap_materno' => $ap_materno,
        'ci' => $ci,
        'fecha_nacimiento' => $fecha_nacimiento,
        'correo' => $email,
        'telefono' => $telefono,
        'nombre_usuario' => $nombre_usuario,
        'contrasena' => $contrasena_segura,
        'ciudad' => $ciudad,
        'zona' => $zona,
        'calle' => $calle,
        'nro_puerta' => $nro_puerta
    ]);

    // Obtener el ID del nuevo usuario
    $usuario_id = $conexion->lastInsertId();

    // Insertar en cliente
    $stmtCliente = $conexion->prepare("INSERT INTO cliente (UsuarioID) VALUES (?)");
    $stmtCliente->execute([$usuario_id]);

    // Obtener ClienteID recién generado
    $clienteID = $conexion->lastInsertId();

    // Crear carrito único para el cliente nuevo
    $stmt = $conexion->prepare("INSERT INTO carrito (ClienteID, Estado, Fecha_creacion) VALUES (?, 'Activo', NOW())");
    $stmt->execute([$clienteID]);

    echo json_encode(['status' => 'success', 'message' => 'Usuario registrado exitosamente.']);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error del servidor al registrar el usuario.',
        'debug' => $e->getMessage() // Desactiva en producción
    ]);
}


