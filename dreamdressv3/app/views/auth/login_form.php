<?php
// Recuperar datos del formulario si existen en la sesión (para rellenar en caso de error)
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // Limpiar después de usar

// Mensajes de error o éxito
if (isset($_SESSION['error_message'])) {
    echo '<p style="color:red;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['success_message'])) {
    echo '<p style="color:green;">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
    unset($_SESSION['success_message']);
}
?>

<form action="index.php?page=login_action" method="POST">
    <fieldset>
        <legend>Iniciar Sesión</legend>
        
        <div>
            <label for="nombre_usuario">Nombre de Usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required value="<?php echo htmlspecialchars($form_data['nombre_usuario'] ?? ''); ?>">
        </div>
        
        <div>
            <label for="contrasena">Contraseña:</label>
            <div class="password-wrapper">
                <input type="password" id="contrasena" name="contrasena" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('contrasena', this)">&#128065;</span> <!-- Entity for eye icon -->
            </div>
        </div>
        
        <div>
            <button type="submit">Ingresar</button>
        </div>
    </fieldset>
</form>

<style>
    form { max-width: 400px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;}
    fieldset { border: none; padding: 0; }
    legend { font-size: 1.5em; font-weight: bold; margin-bottom: 15px; color: #a76464; }
    form div { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
    input[type="text"], input[type="password"] {
        width: calc(100% - 22px); /* Full width minus padding and border */
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .password-wrapper input[type="password"],
    .password-wrapper input[type="text"] { /* Also style when type is text */
        width: 100%; /* Input takes full width of wrapper */
        padding-right: 40px; /* Space for the icon */
    }
    .toggle-password {
        position: absolute;
        right: 10px;
        cursor: pointer;
        user-select: none; /* Prevent text selection */
        font-size: 1.2em;
        color: #777;
    }
    button[type="submit"] {
        background-color: #b37b7c;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1em;
        transition: background-color 0.3s;
    }
    button[type="submit"]:hover {
        background-color: #a76464;
    }
</style>

<!-- Script for password visibility - Consider moving to a global JS file -->
