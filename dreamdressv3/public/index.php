<?php
// Iniciar la sesión (importante para login, carritos, etc.)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definir una constante para la ruta base de la aplicación
define('BASE_PATH', dirname(__DIR__));

// Cargar el archivo de conexión a la base de datos (los controladores lo necesitarán)
require_once BASE_PATH . '/app/core/Connection.php';

// --- Simple Router ---
// Determina la página actual a partir del parámetro GET 'page', o 'home' por defecto.
$page = $_GET['page'] ?? 'home';
$active_page = $page; // Para marcar el enlace activo en la navbar

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bella Arte Boutique - <?php echo htmlspecialchars(ucfirst($page)); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="index.php?page=home" class="<?php echo ($active_page === 'home') ? 'active' : ''; ?>">Home</a>
    <a href="index.php?page=catalogo" class="<?php echo ($active_page === 'catalogo') ? 'active' : ''; ?>">Catálogo</a>
    <a href="index.php?page=editor" class="<?php echo ($active_page === 'editor') ? 'active' : ''; ?>">Editor 3D</a>
    <a href="index.php?page=reservas" class="<?php echo ($active_page === 'reservas') ? 'active' : ''; ?>">Reservas</a>
    <a href="index.php?page=carrito" class="<?php echo ($active_page === 'carrito') ? 'active' : ''; ?>">Carrito</a>
    
    <div class="auth-links">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <span class="welcome-user">Bienvenido/a, <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>!</span>
            <a href="index.php?page=logout_action">Cerrar Sesión</a>
            <?php if ($_SESSION['rol_id'] == 1 || $_SESSION['rol_id'] == 2): // Asumiendo RolID 1=Admin, 2=Empleado ?>
                 <a href="index.php?page=admin_panel" class="<?php echo ($active_page === 'admin_panel') ? 'active' : ''; ?>">Panel Admin</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="index.php?page=login" class="<?php echo ($active_page === 'login') ? 'active' : ''; ?>">Iniciar Sesión</a>
            <a href="index.php?page=registro" class="<?php echo ($active_page === 'registro') ? 'active' : ''; ?>">Registrarse</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">
    <?php
    // --- Carga de Contenido Dinámico (Placeholder) ---
    // Esto se refinará con controladores y vistas dedicadas.
    
    // Por ahora, un switch simple para mostrar contenido diferente.
    switch ($page) {
        case 'home':
            echo "<h1>Página Principal</h1>";
            echo "<p>Bienvenido a la página principal de Bella Arte Boutique. Explora nuestros servicios y diseños.</p>";
            // Prueba de conexión a la BD (podemos quitarla después)
            $pdo_test = get_pdo_connection();
            if ($pdo_test) {
                echo "<p style='color:green; font-weight:bold;'>✓ Conexión a la base de datos exitosa.</p>";
            } else {
                echo "<p style='color:red; font-weight:bold;'>✗ Error al conectar con la base de datos.</p>";
            }
            break;
        
        case 'usuario':
            echo "<h1>Área de Usuario</h1>";
            // Lógica simple para mostrar opciones de login/registro o perfil
            if (isset($_SESSION['usuario_id'])) { // Suponiendo que 'usuario_id' se establece en sesión al loguearse
                $userName = isset($_SESSION['usuario_nombre']) ? htmlspecialchars($_SESSION['usuario_nombre']) : 'Usuario';
                echo "<p>Bienvenido/a, " . $userName . ".</p>";
                echo '<p><a href="index.php?page=perfil">Ver mi Perfil</a></p>'; // Placeholder
                echo '<p><a href="index.php?page=logout_action">Cerrar Sesión</a></p>'; // Placeholder
            } else {
                echo "<p>Gestiona tu cuenta, revisa tus pedidos y más.</p>";
                echo '<p><a href="index.php?page=login">Iniciar Sesión</a> &nbsp;|&nbsp; <a href="index.php?page=registro">Registrarse</a></p>';
            }
            break;

        case 'catalogo':
            echo "<h1>Catálogo de Vestidos</h1>";
            echo "<p>Aquí se mostrará nuestro catálogo completo de vestidos de novia y fiesta.</p>";
            break;

        case 'editor':
            echo "<h1>Editor 3D de Vestidos</h1>";
            echo "<p>Diseña el vestido de tus sueños con nuestro editor 3D interactivo.</p>";
            break;

        case 'reservas':
            echo "<h1>Reservas de Citas</h1>";
            echo "<p>Agenda una cita con nuestros diseñadores para una atención personalizada.</p>";
            break;

        case 'carrito':
            echo "<h1>Carrito de Compras</h1>";
            echo "<p>Revisa los artículos en tu carrito y procede al pago.</p>";
            break;
        
        // Casos para formularios de login y registro (los implementaremos pronto)
        case 'login':
            include BASE_PATH . '/app/views/auth/login_form.php';
            break;
        
        case 'login_action':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombre_usuario_login = trim($_POST['nombre_usuario'] ?? '');
                $contrasena_login = $_POST['contrasena'] ?? '';
                $errors = [];

                if (empty($nombre_usuario_login)) {
                    $errors[] = "El nombre de usuario es obligatorio.";
                }
                if (empty($contrasena_login)) {
                    $errors[] = "La contraseña es obligatoria.";
                }

                if (empty($errors)) {
                    $pdo = get_pdo_connection();
                    if ($pdo) {
                        try {
                            // Modificar la consulta para buscar por NombreUsuario O correo con placeholders distintos
                            $stmt = $pdo->prepare("SELECT UsuarioID, NombreUsuario, Contrasena, RolID, Habilitado FROM usuario WHERE NombreUsuario = :login_param_un OR correo = :login_param_dos");
                            $stmt->execute([
                                'login_param_un' => $nombre_usuario_login,
                                'login_param_dos' => $nombre_usuario_login
                            ]);
                            $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($usuario_db) {
                                if (password_verify($contrasena_login, $usuario_db['Contrasena'])) {
                                    if ($usuario_db['Habilitado'] == 1) {
                                        session_regenerate_id(true); // Prevenir fijación de sesión
                                        $_SESSION['usuario_id'] = $usuario_db['UsuarioID'];
                                        $_SESSION['nombre_usuario'] = $usuario_db['NombreUsuario'];
                                        $_SESSION['rol_id'] = $usuario_db['RolID'];
                                        // Podrías guardar más datos si los necesitas globalmente

                                        // Redirigir según el rol, o a una página por defecto
                                        // Por ahora, a la página de inicio
                                        header('Location: index.php?page=home&login=success');
                                        exit;
                                    } else {
                                        $errors[] = "Su cuenta no está habilitada. Por favor, contacte al administrador.";
                                    }
                                } else {
                                    $errors[] = "Nombre de usuario o contraseña incorrectos.";
                                }
                            } else {
                                $errors[] = "Nombre de usuario o contraseña incorrectos.";
                            }
                        } catch (PDOException $e) {
                            error_log("Error en login_action (PDOException): " . $e->getMessage());
                            $errors[] = "Error al procesar el login. Detalles: " . $e->getMessage();
                        }
                    } else {
                        $errors[] = "Error de conexión con la base de datos.";
                    }
                }

                // Si hay errores, regresar al formulario de login
                if (!empty($errors)) {
                    $_SESSION['error_message'] = implode("<br>", $errors);
                    $_SESSION['form_data'] = $_POST; // Para rellenar el nombre de usuario
                    header('Location: index.php?page=login');
                    exit;
                }
            } else {
                // Si no es POST, redirigir al login
                header('Location: index.php?page=login');
                exit;
            }
            break;

        case 'logout_action':
            // Destruir todas las variables de sesión.
            $_SESSION = array();

            // Si se desea destruir la sesión completamente, borre también la cookie de sesión.
            // Nota: ¡Esto destruirá la sesión, y no solo los datos de la sesión!
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }

            // Finalmente, destruir la sesión.
            session_destroy();

            // Redirigir a la página de inicio (o a la de login)
            header('Location: index.php?page=home&logout=success');
            exit;
            break;

        case 'registro':
            // echo "<h1>Registro de Nuevo Usuario</h1>"; // El título ya está en el form
            // echo "<p>Formulario de registro irá aquí.</p>";
            include BASE_PATH . '/app/views/auth/register_form.php';
            break;

        case 'register_action': 
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // 1. Recoger datos del POST
                $nombre_form = trim($_POST['nombre'] ?? ''); // Será Nombres
                $ap_paterno_form = trim($_POST['ap_paterno'] ?? ''); 
                $ap_materno_form = trim($_POST['ap_materno'] ?? ''); 
                $ci_form = trim($_POST['ci'] ?? '');
                $fecha_nacimiento_form = trim($_POST['fecha_nacimiento'] ?? '');
                $ciudad_form = trim($_POST['ciudad'] ?? ''); // Nuevo
                $zona_form = trim($_POST['zona'] ?? ''); // Nuevo
                $calle_form = trim($_POST['calle'] ?? ''); // Nuevo
                $nro_puerta_form = trim($_POST['nro_puerta'] ?? ''); // Nuevo, opcional
                $telefono_form = trim($_POST['telefono'] ?? '');
                $nombre_usuario_form = trim($_POST['nombre_usuario'] ?? '');
                $email_form = trim($_POST['email'] ?? '');
                $contrasena_form = $_POST['contrasena'] ?? '';
                $confirmar_contrasena_form = $_POST['confirmar_contrasena'] ?? '';

                // 2. Validaciones
                $errors = [];
                if (empty($nombre_form)) $errors[] = "El nombre es obligatorio.";
                if (empty($ap_paterno_form)) $errors[] = "El Apellido Paterno es obligatorio.";
                if (empty($ci_form)) $errors[] = "La Cédula de Identidad (CI) es obligatoria.";
                // Validaciones para fecha_nacimiento_form si tiene formatos específicos.
                if (empty($ciudad_form)) $errors[] = "La Ciudad es obligatoria.";
                if (empty($zona_form)) $errors[] = "La Zona es obligatoria.";
                if (empty($calle_form)) $errors[] = "La Calle es obligatoria.";
                // Nro_puerta_form es opcional, no necesita validación de vacío aquí a menos que tenga otros requisitos.
                
                if (empty($nombre_usuario_form)) $errors[] = "El nombre de usuario es obligatorio.";
                if (empty($email_form)) {
                    $errors[] = "El correo electrónico es obligatorio.";
                } elseif (!filter_var($email_form, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "El formato del correo electrónico no es válido.";
                }
                if (empty($contrasena_form)) {
                    $errors[] = "La contraseña es obligatoria.";
                } elseif (strlen($contrasena_form) < 6) {
                    $errors[] = "La contraseña debe tener al menos 6 caracteres.";
                }
                if ($contrasena_form !== $confirmar_contrasena_form) {
                    $errors[] = "Las contraseñas no coinciden.";
                }

                // 3. Si no hay errores de validación inicial, proceder
                if (empty($errors)) {
                    $pdo = get_pdo_connection();
                    if ($pdo) {
                        try {
                            $pdo->beginTransaction();

                            // 3.1. Verificar duplicados
                            $stmt_check_ci = $pdo->prepare("SELECT RegistroID FROM registro WHERE CI = :ci");
                            $stmt_check_ci->execute(['ci' => $ci_form]);
                            if ($stmt_check_ci->fetch()) {
                                $errors[] = "La Cédula de Identidad ya está registrada.";
                            }

                            // Usar 'correo' como en tu tabla usuario
                            $stmt_check_user = $pdo->prepare("SELECT UsuarioID FROM usuario WHERE NombreUsuario = :nombre_usuario OR correo = :email");
                            $stmt_check_user->execute(['nombre_usuario' => $nombre_usuario_form, 'email' => $email_form]);
                            if ($stmt_check_user->fetch()) {
                                $errors[] = "El nombre de usuario o correo electrónico ya están registrados.";
                            }

                            if (!empty($errors)) {
                                if ($pdo->inTransaction()) $pdo->rollBack();
                                $_SESSION['error_message'] = implode("<br>", $errors);
                                $_SESSION['form_data'] = $_POST;
                                header('Location: index.php?page=registro');
                                exit;
                            }

                            // 3.2. Insertar en tabla 'registro'
                            // Ajustar nombres de columnas y campos opcionales
                            $sql_registro = "INSERT INTO registro (Nombres, Ap_paterno, Ap_materno, CI, Fecha_nacimiento, Ciudad, Zona, Calle, Nro_puerta, Telefono) 
                                             VALUES (:nombres, :ap_paterno, :ap_materno, :ci, :fecha_nacimiento, :ciudad, :zona, :calle, :nro_puerta, :telefono)";
                            $stmt_registro = $pdo->prepare($sql_registro);
                            $stmt_registro->execute([
                                'nombres' => $nombre_form,
                                'ap_paterno' => $ap_paterno_form,
                                'ap_materno' => !empty($ap_materno_form) ? $ap_materno_form : null,
                                'ci' => $ci_form,
                                'fecha_nacimiento' => !empty($fecha_nacimiento_form) ? $fecha_nacimiento_form : null,
                                'ciudad' => $ciudad_form,
                                'zona' => $zona_form,
                                'calle' => $calle_form,
                                'nro_puerta' => !empty($nro_puerta_form) ? $nro_puerta_form : null,
                                'telefono' => !empty($telefono_form) ? $telefono_form : null
                            ]);
                            $registro_id = $pdo->lastInsertId();

                            // 3.3. Hashear contraseña
                            $contrasena_hash = password_hash($contrasena_form, PASSWORD_DEFAULT);
                            
                            // 3.4. Insertar en tabla 'usuario'
                            $rol_id_cliente = 5; 
                            $habilitado_usuario = 1; // 0 para 'Pendiente' (no habilitado), 1 para 'Activo' (habilitado)

                            // Quitar Nombre y Apellido, usar 'correo' y 'Habilitado'
                            $sql_usuario = "INSERT INTO usuario (RegistroID, RolID, NombreUsuario, Contrasena, correo, Habilitado) 
                                            VALUES (:registro_id, :rol_id, :nombre_usuario, :contrasena_hash, :email, :habilitado)";
                            $stmt_usuario = $pdo->prepare($sql_usuario);
                            $stmt_usuario->execute([
                                'registro_id' => $registro_id,
                                'rol_id' => $rol_id_cliente,
                                'nombre_usuario' => $nombre_usuario_form,
                                'contrasena_hash' => $contrasena_hash,
                                'email' => $email_form,
                                'habilitado' => $habilitado_usuario
                            ]);
                            $nuevo_usuario_id = $pdo->lastInsertId();

                            // 3.5. Insertar en tabla 'cliente' (si aún es relevante)
                            $sql_cliente = "INSERT INTO cliente (UsuarioID) VALUES (:usuario_id)";
                            $stmt_cliente = $pdo->prepare($sql_cliente);
                            $stmt_cliente->execute(['usuario_id' => $nuevo_usuario_id]);
                            

                            $pdo->commit();

                            $_SESSION['success_message'] = "¡Registro exitoso! Ahora puedes iniciar sesión.";
                            unset($_SESSION['form_data']);
                            header('Location: index.php?page=login');
                            exit;

                        } catch (PDOException $e) {
                            if ($pdo->inTransaction()) {
                                $pdo->rollBack();
                            }
                            error_log("Error en register_action (PDOException): " . $e->getMessage());
                            $_SESSION['error_message'] = "Error al procesar el registro. Hubo un problema con la base de datos. Detalles: " . $e->getMessage(); 
                            $_SESSION['form_data'] = $_POST;
                            header('Location: index.php?page=registro');
                            exit;
                        }
                    } else {
                        $_SESSION['error_message'] = "Error de conexión con la base de datos.";
                        $_SESSION['form_data'] = $_POST;
                        header('Location: index.php?page=registro');
                        exit;
                    }
                } else { // Si hay errores de validación inicial
                    $_SESSION['error_message'] = implode("<br>", $errors);
                    $_SESSION['form_data'] = $_POST;
                    header('Location: index.php?page=registro');
                    exit;
                }
            } else {
                header('Location: index.php?page=registro');
                exit;
            }
            break;
            
        default:
            echo "<h1>Página no Encontrada</h1>";
            echo "<p>Lo sentimos, la página que buscas no existe.</p>";
            // http_response_code(404); // Para enviar un estado 404 real
            break;
    }
    ?>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Bella Arte Boutique. Todos los derechos reservados.</p>
    <!-- Puedes añadir más elementos al footer aquí -->
</footer>

<script src="js/main.js"></script> 
</body>
</html>