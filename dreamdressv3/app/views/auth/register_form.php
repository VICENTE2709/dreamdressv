<?php
// Recuperar datos del formulario si existen en la sesión (para rellenar en caso de error)
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // Limpiar después de usar

// Mensajes de error o éxito
if (isset($_SESSION['error_message'])) {
    echo '<p style="color:red;">' . $_SESSION['error_message'] . '</p>'; // No necesita htmlspecialchars si ya lo hicimos al guardar y usamos <br>
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['success_message'])) {
    echo '<p style="color:green;">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
    unset($_SESSION['success_message']);
}
?>

<form action="index.php?page=register_action" method="POST">
    <fieldset>
        <legend>Crear Nueva Cuenta</legend>
        
        <div>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($form_data['nombre'] ?? ''); ?>">
        </div>
        
        <div>
            <label for="ap_paterno">Apellido Paterno:</label>
            <input type="text" id="ap_paterno" name="ap_paterno" required value="<?php echo htmlspecialchars($form_data['ap_paterno'] ?? ''); ?>">
        </div>

        <div>
            <label for="ap_materno">Apellido Materno:</label>
            <input type="text" id="ap_materno" name="ap_materno" value="<?php echo htmlspecialchars($form_data['ap_materno'] ?? ''); ?>">
        </div>

        <div>
            <label for="ci">Cédula de Identidad (CI):</label>
            <input type="text" id="ci" name="ci" required value="<?php echo htmlspecialchars($form_data['ci'] ?? ''); ?>">
        </div>

        <div>
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($form_data['fecha_nacimiento'] ?? ''); ?>">
        </div>

        <div>
            <label for="ciudad">Ciudad:</label>
            <select id="ciudad" name="ciudad" required>
                <option value="" <?php echo empty($form_data['ciudad']) ? 'selected' : ''; ?> disabled>Seleccione una ciudad</option>
                <?php 
                $ciudades = ['LA PAZ','EL ALTO','SANTA CRUZ','COCHABAMBA','TARIJA','PANDO','CHUQUISACA','BENI','ORURO','POTOSI'];
                foreach ($ciudades as $ciudad_opcion) {
                    $selected = (isset($form_data['ciudad']) && $form_data['ciudad'] == $ciudad_opcion) ? 'selected' : '';
                    echo "<option value='{$ciudad_opcion}' {$selected}>{$ciudad_opcion}</option>";
                }
                ?>
            </select>
        </div>

        <div>
            <label for="zona">Zona:</label>
            <input type="text" id="zona" name="zona" required value="<?php echo htmlspecialchars($form_data['zona'] ?? ''); ?>">
        </div>

        <div>
            <label for="calle">Calle:</label>
            <input type="text" id="calle" name="calle" required value="<?php echo htmlspecialchars($form_data['calle'] ?? ''); ?>">
        </div>

        <div>
            <label for="nro_puerta">Número de Puerta/Casa:</label>
            <input type="text" id="nro_puerta" name="nro_puerta" value="<?php echo htmlspecialchars($form_data['nro_puerta'] ?? ''); ?>">
        </div>
        
        <div>
            <label for="telefono">Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($form_data['telefono'] ?? ''); ?>">
        </div>

        <div>
            <label for="nombre_usuario">Nombre de Usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required value="<?php echo htmlspecialchars($form_data['nombre_usuario'] ?? ''); ?>">
        </div>
        
        <div>
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
        </div>
        
        <div>
            <label for="contrasena">Contraseña:</label>
            <div class="password-wrapper">
                <input type="password" id="contrasena" name="contrasena" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('contrasena', this)">&#128065;</span>
            </div>
        </div>

        <div>
            <label for="confirmar_contrasena">Confirmar Contraseña:</label>
            <div class="password-wrapper">
                <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('confirmar_contrasena', this)">&#128065;</span>
            </div>
        </div>
        
        <div>
            <button type="submit">Registrarse</button>
        </div>
    </fieldset>
</form>

<style>
    form { max-width: 500px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;}
    fieldset { border: none; padding: 0; }
    legend { font-size: 1.5em; font-weight: bold; margin-bottom: 15px; color: #a76464; }
    form div { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
    input[type="text"], input[type="email"], input[type="password"], input[type="date"], input[type="tel"], select {
        width: calc(100% - 22px); /* Full width minus padding and border */
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        background-color: #fff; /* Ensure consistent background */
    }
    /* Specific fix for select to align with inputs */
    select {
        width: 100%; /* Make select take full width of its container */
        height: auto; /* Adjust height to match padding */
        padding: 10px; /* Match padding of other inputs */
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
