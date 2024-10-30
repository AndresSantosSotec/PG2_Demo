// Elementos del DOM necesarios
const uploadForm = document.getElementById('uploadForm');
const fileInput = document.getElementById('file');
const jsonDataDiv = document.getElementById('jsonData');

// Evento para detectar si el usuario intenta salir del módulo
window.addEventListener('beforeunload', (e) => {
    // Si hay datos en el área de JSON, mostrar la alerta
    if (jsonDataDiv.textContent.trim()) {
        const confirmationMessage = "¿Quieres guardar los datos antes de salir?";
        e.returnValue = confirmationMessage; // Para algunos navegadores
        return confirmationMessage;
    }
});

// Guardar datos en cache al salir del módulo
function guardarDatosEnCache() {
    const jsonData = jsonDataDiv.textContent.trim();
    if (jsonData) {
        localStorage.setItem('migracionDatos', jsonData);
        Swal.fire('Guardado', 'Los datos han sido guardados en cache.', 'success');
    }
}

// Intentar cargar datos de cache al entrar
function cargarDatosDesdeCache() {
    const cachedData = localStorage.getItem('migracionDatos');
    if (cachedData) {
        displayFormattedJSON(JSON.parse(cachedData));
        Swal.fire('Datos Cargados', 'Se han cargado los datos desde cache.', 'info');
    }
}

// Mostrar JSON formateado en el área correspondiente
function displayFormattedJSON(data) {
    jsonDataDiv.innerHTML = ''; // Limpiar el área
    if (Array.isArray(data)) {
        data.forEach(item => {
            const pre = document.createElement('pre');
            pre.textContent = JSON.stringify(item, null, 4);
            jsonDataDiv.appendChild(pre);
        });
    } else {
        const pre = document.createElement('pre');
        pre.textContent = JSON.stringify(data, null, 4);
        jsonDataDiv.appendChild(pre);
    }
}

// Alertar al usuario si intenta abandonar la página sin guardar datos
window.addEventListener('unload', (e) => {
    if (jsonDataDiv.textContent.trim()) {
        Swal.fire({
            title: 'Salir sin guardar',
            text: '¿Deseas guardar los datos antes de salir?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Salir sin guardar'
        }).then((result) => {
            if (result.isConfirmed) {
                guardarDatosEnCache();
            }
        });
    }
});

// Cargar los datos desde cache automáticamente al cargar la página
document.addEventListener('DOMContentLoaded', cargarDatosDesdeCache);
