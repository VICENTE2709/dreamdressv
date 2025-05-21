// JavaScript principal para Bella Arte Boutique

// 
// Ejemplo de cómo podrías añadir funcionalidades en el futuro:
/*
document.addEventListener('DOMContentLoaded', function() {
    // Código a ejecutar cuando el DOM esté completamente cargado
    console.log('DOM completamente cargado y parseado. main.js está listo.');

    // Ejemplo: Añadir un listener a un botón con id 'miBoton'
    // const miBoton = document.getElementById('miBoton');
    // if (miBoton) {
    //     miBoton.addEventListener('click', function() {
    //         alert('¡Botón clickeado!');
    //     });
    // }
});
*/

// Función para mostrar/ocultar contraseña
function togglePasswordVisibility(id, toggleElement) {
    const passwordInput = document.getElementById(id);
    if (!passwordInput) {
        console.error('Elemento de contraseña no encontrado:', id);
        return;
    }
    // El toggleElement es el <span> que se clickea.
    // Si no se pasa, podríamos intentar encontrarlo basado en el id del input,
    // pero el HTML actual lo pasa directamente.

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        // Usar innerHTML porque estamos insertando entidades HTML (&#128064;)
        if(toggleElement) toggleElement.innerHTML = '&#128064;'; // Entidad para ojo tachado
    } else {
        passwordInput.type = 'password';
        if(toggleElement) toggleElement.innerHTML = '&#128065;'; // Entidad para ojo normal
    }
}
