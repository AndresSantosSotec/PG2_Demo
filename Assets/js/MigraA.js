// Elementos del DOM
const form = document.getElementById('uploadForm');
const fileInput = document.getElementById('file');
const downloadLinkDiv = document.getElementById('downloadLink');
const convertedFileLink = document.getElementById('convertedFileLink');
const jsonDataDiv = document.getElementById('jsonData');
const credentialsForm = document.getElementById('credentialsForm');
const databasesSection = document.getElementById('databasesSection');
const originDbSelect = document.getElementById('originDb');
const tableSection = document.getElementById('tableSection');
const tableOption = document.getElementById('tableOption');
const newTableNameSection = document.getElementById('newTableNameSection');
const generateSQLBtn = document.getElementById('generateSQLBtn');

// Lógica para subir archivo y convertir a JSON
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const file = fileInput.files[0];
    if (!file) {
        Swal.fire('Error', 'Por favor, selecciona un archivo.', 'error');
        return;
    }

    Swal.fire({
        title: 'Cargando...',
        text: 'Procesando archivo...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    const formData = new FormData(form);
    try {
        const response = await fetch('../../Backend/uploads/upload.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        Swal.close();

        if (result.success) {
            const parsedData = JSON.parse(result.jsonData);
            displayFormattedJSON(parsedData);
            setupDownloadLink(result.jsonData, result.fileName);
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'Error al procesar el archivo.', 'error');
    }
});

// Lógica para conectar al servidor y cargar bases de datos
credentialsForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const ip = document.getElementById('ip').value.trim();
    const user = document.getElementById('user').value.trim();
    const password = document.getElementById('password').value || '';

    if (!ip || !user) {
        Swal.fire('Error', 'Por favor, completa los campos de IP y Usuario.', 'error');
        return;
    }

    Swal.fire({
        title: 'Conectando...',
        text: 'Estableciendo conexión con el servidor...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const response = await fetch('../../Backend/get_databases.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ip, user, password })
        });

        if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();
        Swal.close();

        if (result.success) {
            populateDatabases(result.databases);
            databasesSection.style.display = 'block';
        } else {
            Swal.fire('Error', result.message || 'Error desconocido.', 'error');
        }
    } catch (error) {
        console.error('Error en la conexión:', error);
        Swal.fire('Error', 'No se pudo conectar al servidor. Verifica las credenciales o la conexión.', 'error');
    }
});

// Mostrar opciones de tabla después de seleccionar base de datos
originDbSelect.addEventListener('change', async () => {
    const selectedDb = originDbSelect.value;

    try {
        const response = await fetch('../../Backend/get_tables.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ database: selectedDb })
        });

        const result = await response.json();
        if (result.success) {
            populateTables(result.tables);
            tableSection.style.display = 'block';
        } else {
            Swal.fire('Error', 'No se pudieron cargar las tablas.', 'error');
        }
    } catch (error) {
        console.error('Error al obtener las tablas:', error);
        Swal.fire('Error', 'Hubo un problema al obtener las tablas.', 'error');
    }
});

// Mostrar campo para nombre de nueva tabla si se selecciona "Crear Nueva Tabla"
tableOption.addEventListener('change', () => {
    const selectedValue = tableOption.value;
    newTableNameSection.style.display = selectedValue === 'create' ? 'block' : 'none';
    generateSQLBtn.style.display = selectedValue === 'create' ? 'block' : 'none';
});

// Limpieza de JSON y generación de tabla
generateSQLBtn.addEventListener('click', async () => {
    const tableName = document.getElementById('newTableName').value.trim();
    if (!tableName) {
        Swal.fire('Error', 'Por favor, ingresa un nombre para la nueva tabla.', 'error');
        return;
    }

    try {
        let rawText = jsonDataDiv.textContent.trim();
        if (!rawText.startsWith('[')) {
            rawText = `[${rawText.replace(/}{/g, '},{')}]`;
        }
        const cleanText = rawText.replace(/[\u200B-\u200D\uFEFF]/g, '').replace(/(\r\n|\n|\r)/gm, '');
        const jsonData = JSON.parse(cleanText || '[]');

        if (jsonData.length === 0) {
            Swal.fire('Error', 'No hay datos disponibles para generar la tabla.', 'error');
            return;
        }

        const createTableSQL = generateCreateTableSQL(tableName, jsonData);

        // Debug para verificar el SQL y el nombre de la tabla
        console.log('SQL generado:', createTableSQL);
        console.log('Nombre de la tabla:', tableName);

        const confirmation = await Swal.fire({
            title: '¿Deseas ejecutar este SQL y migrar los datos?',
            html: `<pre>${createTableSQL}</pre>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ejecutar y Migrar',
            cancelButtonText: 'Cancelar'
        });

        if (confirmation.isConfirmed) {
            const ip = document.getElementById('ip').value.trim();
            const user = document.getElementById('user').value.trim();
            const password = document.getElementById('password').value || '';
            const database = originDbSelect.value;

            const response = await fetch('../../Backend/create_table.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    sql: createTableSQL,
                    jsonData,
                    tableName, // Aseguramos que se envíe el nombre de la tabla
                    ip,
                    user,
                    password,
                    database
                })
            });

            const result = await response.json();
            if (result.success) {
                Swal.fire('Éxito', 'Tabla creada e inserción de datos exitosa.', 'success');
            } else {
                Swal.fire('Error', result.message || 'Hubo un error en la operación.', 'error');
            }
        }
    } catch (error) {
        console.error('Error al interpretar JSON:', error);
        Swal.fire('Error', 'Error al interpretar el JSON para la tabla.', 'error');
    }
});

function generateCreateTableSQL(tableName, jsonData) {
    if (!tableName) {
        throw new Error('El nombre de la tabla es obligatorio y no puede estar vacío.');
    }

    let sql = `CREATE TABLE \`${tableName}\` (\n`;
    const columns = Object.keys(jsonData[0]);

    columns.forEach((col, index) => {
        sql += `  \`${col.replace(/\s+/g, '_')}\` VARCHAR(255)`;
        if (index < columns.length - 1) sql += ',\n';
    });

    sql += `\n);`;
    return sql;
}
// Función para mostrar el JSON formateado en la interfaz
function displayFormattedJSON(data) {
    jsonDataDiv.innerHTML = '';

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

// Función para configurar el enlace de descarga del JSON convertido
function setupDownloadLink(jsonData, fileName) {
    downloadLinkDiv.style.display = 'block';
    const blob = new Blob([jsonData], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    convertedFileLink.href = url;
    convertedFileLink.download = fileName;
}

// Función para llenar el dropdown con bases de datos
function populateDatabases(databases) {
    originDbSelect.innerHTML = databases.map(db => `<option value="${db}">${db}</option>`).join('');
}

// Función para llenar el dropdown de tablas existentes
function populateTables(tables) {
    tableOption.innerHTML = `
        <option value="select">Seleccionar Tabla Existente</option>
        <option value="create">Crear Nueva Tabla</option>
        ${tables.map(table => `<option value="${table}">${table}</option>`).join('')}
    `;
}

// Función para generar sentencia SQL basada en los datos JSON
function generateCreateTableSQL(tableName, jsonData) {
    let sql = `CREATE TABLE ${tableName} (\n`;
    const sample = jsonData[0];

    for (const [key, value] of Object.entries(sample)) {
        const columnName = key.replace(/\s+/g, '_').replace(/[^\w]/g, '');
        const dataType = inferDataType(value);
        sql += `  ${columnName} ${dataType},\n`;
    }

    sql = sql.slice(0, -2) + '\n);';
    return sql;
}

// Función para inferir el tipo de dato
function inferDataType(value) {
    if (!isNaN(Date.parse(value))) return 'DATETIME';
    if (!isNaN(value)) return 'INT';
    if (typeof value === 'string' && value.length <= 50) return 'VARCHAR(50)';
    return 'TEXT';
}
