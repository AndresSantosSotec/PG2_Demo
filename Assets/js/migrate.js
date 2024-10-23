const credentialsForm = document.getElementById('credentialsForm');
const databaseForm = document.getElementById('databaseForm');
const databasesSection = document.getElementById('databasesSection');
const tableSelectionSection = document.getElementById('tableSelectionSection');
const originDbSelect = document.getElementById('originDb');
const originTableSelect = document.getElementById('originTable');
const migrationOptionSelect = document.getElementById('migrationOption');
const progressBar = document.querySelector('.progress-bar');
const progressContainer = document.querySelector('.progress');

// Conectar al servidor para obtener bases de datos
credentialsForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const ip = document.getElementById('ip').value;
    const user = document.getElementById('user').value;
    const password = document.getElementById('password').value;

    Swal.fire({
        title: 'Conectando...',
        text: 'Estableciendo conexión con el servidor.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await fetch('../../Backend/get_databases.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ip, user, password })
        });

        const result = await response.json();
        Swal.close();

        if (result.success) {
            populateDatabases(result.databases);
            databasesSection.style.display = 'block';
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'No se pudo conectar al servidor.', 'error');
    }
});

// Llenar bases de datos
function populateDatabases(databases) {
    originDbSelect.innerHTML = databases.map(db => `<option value="${db}">${db}</option>`).join('');
}

// Mostrar selección de tabla si es necesario
migrationOptionSelect.addEventListener('change', () => {
    if (migrationOptionSelect.value === 'singleTable') {
        loadTables();
        tableSelectionSection.style.display = 'block';
    } else {
        tableSelectionSection.style.display = 'none';
    }
});

// Cargar tablas de
