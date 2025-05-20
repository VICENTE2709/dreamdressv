console.log("✅ login.js cargado correctamente");

// Capturar elementos
const ojoPassword = document.getElementById('ojoPassword');
const iconoPassword = document.getElementById('iconoPassword');
const passwordField = document.getElementById('password');
const emailField = document.getElementById('email');
const formLogin = document.getElementById('sign-In');

// Función para mostrar/ocultar contraseña
ojoPassword.addEventListener('click', function () {
    const isPasswordVisible = passwordField.type === 'text';
    passwordField.type = isPasswordVisible ? 'password' : 'text';
    iconoPassword.src = isPasswordVisible 
        ? 'img/ojocerrado.png' 
        : 'img/ojoabierto.png';
});

// Función para validar campos
function validarCampo(input) {
    if (input.value.trim() === '') {
        input.classList.add('input-error');
        input.classList.remove('input-correcto');
    } else {
        input.classList.remove('input-error');
        input.classList.add('input-correcto');
    }
}

// Función para mostrar mensajes en el HTML
function mostrarMensaje(texto, tipo = 'error') {
    let mensaje = document.getElementById('mensaje');
    if (!mensaje) {
        // Crear contenedor si no existe
        mensaje = document.createElement('div');
        mensaje.id = 'mensaje';
        mensaje.style.padding = '10px';
        mensaje.style.margin = '10px 0';
        mensaje.style.borderRadius = '5px';
        mensaje.style.fontWeight = 'bold';
        mensaje.style.textAlign = 'center';
        mensaje.style.transition = 'all 0.5s ease';
        document.getElementById('forms').prepend(mensaje);
    }

    mensaje.className = '';
    if (tipo === 'success') {
        mensaje.classList.add('mensaje-success');
    } else {
        mensaje.classList.add('mensaje-error');
    }
    mensaje.innerHTML = texto;
    mensaje.style.display = 'block';
    mensaje.style.opacity = '1';

    // Desaparecer después de 4 segundos
    setTimeout(() => {
        mensaje.style.opacity = '0';
        setTimeout(() => {
            mensaje.style.display = 'none';
        }, 500);
    }, 4000);
}

// Validación y envío del formulario
formLogin.addEventListener('submit', async function(event) {
    event.preventDefault();

    validarCampo(emailField);
    validarCampo(passwordField);

    if (emailField.classList.contains('input-error') || passwordField.classList.contains('input-error')) {
        mostrarMensaje('❌ Por favor completa todos los campos correctamente.');
        return; // Bloquear envío si hay campos mal escritos
    }

    const usuarioEntrada = emailField.value.trim();
    const passwordEntrada = passwordField.value.trim();

    try {
        const response = await fetch('http://dreamdressv1.test/dreamdressv1/backend/api/login_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                usuario: usuarioEntrada,
                contrasena: passwordEntrada
            })
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        console.log("📥 Respuesta del servidor:", data);

        if (data.status === 'success') {
            const u = data.usuario;
            localStorage.setItem('usuario_id', u.usuario_id);
            localStorage.setItem('token', u.token);
            localStorage.setItem('rol_id', u.rol_id);
        
            mostrarMensaje('✅ ¡Bienvenido!', 'success');
            setTimeout(() => {
                // Redirigir según el rol
                switch (data.usuario.rol_id) {
                    case 1:
                        window.location.href = 'http://dreamdressv1.test/dreamdressv1/frontend/admin/superadmin_menu.html';
                        break;
                    case 2:
                        window.location.href = 'http://dreamdressv1.test/dreamdressv1/frontend/admin/owner_menu.html';
                        break;
                    case 3:
                        window.location.href = 'http://dreamdressv1.test/dreamdressv1/frontend/inventario/menu_inventario.html';
                        break;
                    case 4:
                        window.location.href = 'http://dreamdressv1.test/dreamdressv1/frontend/ventas/menu_ventas.html';
                        break;
                    case 5:
                        window.location.href = 'http://dreamdressv1.test/dreamdressv1/frontend/main_menu/index.html';
                        break;
                    default:
                        mostrarMensaje('⚠️ Rol no reconocido. Contacta al administrador.');
                        break;
                }
            }, 2000);
        } else {
            mostrarMensaje(`🚫 ${data.message}`);
        }

    } catch (error) {
        console.error("❌ Error en login:", error);
        mostrarMensaje('🚫 Error de conexión con el servidor.');
    }
});



