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
//genrar tablas BTN
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

        preprocessJSON(jsonData); // Preprocesar JSON

        const createTableSQL = generateCreateTableSQL(tableName, jsonData);
        console.log('SQL generado:', createTableSQL);

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
                    tableName,
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
// Generar SQL para la creación de tablas
function generateCreateTableSQL(tableName, jsonData) {
    if (!tableName) {
        throw new Error('El nombre de la tabla es obligatorio y no puede estar vacío.');
    }

    let sql = `CREATE TABLE \`${tableName}\` (\n`;

    // Agregar el campo 'id' como autoincrementable y clave primaria
    sql += '  `id` INT AUTO_INCREMENT PRIMARY KEY,\n';

    const sample = jsonData[0];

    for (const [key, value] of Object.entries(sample)) {
        let columnName = sanitizeColumnName(key);

        // Evitar duplicar la columna 'id' si ya existe en los datos
        if (columnName.toLowerCase() === 'id') {
            console.warn('El campo "id" ya está presente en los datos y será omitido.');
            continue;
        }

        if (!columnName) {
            console.warn(`Advertencia: El campo "${key}" tiene un nombre inválido. Se asignará un nombre por defecto.`);
            columnName = `col_${Math.random().toString(36).substring(7)}`; // Nombre único por defecto
        }

        const dataType = inferDataType(key, value);
        sql += `  \`${columnName}\` ${dataType},\n`;
    }

    // Eliminar la coma final y cerrar la instrucción SQL
    sql = sql.slice(0, -2) + '\n);';
    return sql;
}
//Logica para caprurar los logs de la migracion de la base de datos 
async function insertarDatosConLogs(jsonData, tableName, migracionId) {
    const ip = document.getElementById('ip').value.trim();
    const user = document.getElementById('user').value.trim();
    const password = document.getElementById('password').value || '';
    const database = originDbSelect.value;

    try {
        const response = await fetch('../../Backend/create_table.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                sql: '', 
                jsonData,
                tableName,
                ip,
                user,
                password,
                database,
                migracionId
            })
        });

        const result = await response.json();

        if (result.success) {
            console.log('Inserciones completadas. Logs:');
            result.logs.forEach(log => console.log(`Registro #${log.index}: ${log.message}`));
        } else {
            console.error('Error en la migración:', result.message);
        }
    } catch (error) {
        console.error('Error al procesar la migración:', error);
    }
}
//logia para la realizacion de los logs de las migraciones en bd
async function registrarLog(migracionId, mensaje) {
    try {
        const response = await fetch('../../Backend/log_migracion.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ migracionId, mensaje })
        });

        const result = await response.json();
        if (!result.success) {
            console.error('Error al registrar log:', result.message);
        }
    } catch (error) {
        console.error('Error en la solicitud del log:', error);
    }
}
// Función para sanitizar nombres de columnas
function sanitizeColumnName(name) {
    const sanitized = name.replace(/\s+/g, '_').replace(/[^\w]/g, '');
    return sanitized || null; // Retorna null si el nombre es vacío
}
function formatDate(value) {
    const dateTimePattern = /^\d{2}[\/-]\d{2}[\/-]\d{4} \d{1,2}:\d{2}(:\d{2})?$/;
    const datePattern1 = /^\d{4}[\/-]\d{2}[\/-]\d{2}$/; // YYYY-MM-DD o YYYY/MM/DD
    const datePattern2 = /^\d{2}[\/-]\d{2}[\/-]\d{4}$/; // DD/MM/YYYY o MM/DD/YYYY

    if (dateTimePattern.test(value)) {
        const [datePart, timePart] = value.split(' ');
        const date = parseDate(datePart);
        const time = timePart.length === 5 ? `${timePart}:00` : timePart;
        return `${date} ${time}`.trim();
    } else if (datePattern1.test(value) || datePattern2.test(value)) {
        return parseDate(value);
    } else {
        console.error(`Fecha no válida: "${value}". Almacenando como null.`);
        return null;
    }
}

// Función para parsear la fecha al formato YYYY-MM-DD
function parseDate(value) {
    const parts = value.split(/[\/-]/);
    if (parts[0].length === 4) {
        return `${parts[0]}-${parts[1].padStart(2, '0')}-${parts[2].padStart(2, '0')}`;
    } else if (parseInt(parts[1], 10) <= 12) {
        return `${parts[2]}-${parts[0].padStart(2, '0')}-${parts[1].padStart(2, '0')}`;
    } else {
        return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
    }
}
// Inferir tipo de dato con validaciones
function inferDataType(key, value) {
    const dateTimePattern = /^\d{2}[\/-]\d{2}[\/-]\d{4} \d{1,2}:\d{2}(:\d{2})?$/;
    const datePattern = /^\d{4}[\/-]\d{2}[\/-]\d{2}$/;
    const decimalPattern = /^-?\d+\.\d+$/;
    const intPattern = /^-?\d+$/;

    if (/telefono|phone/i.test(key)) {
        return 'VARCHAR(20)'; // Asegurar siempre VARCHAR(20) para teléfonos
    }

    if (/nombre|name/i.test(key)) {
        return 'VARCHAR(100)'; // Asegurar siempre VARCHAR(100) para nombres
    }

    if (/fecha|date|alta|nacimiento/i.test(key)) {
        return dateTimePattern.test(value) ? 'DATETIME' : 'DATE';
    }

    if (decimalPattern.test(value)) {
        return 'DECIMAL(10,2)';
    }

    if (intPattern.test(value)) {
        return 'INT';
    }

    return 'VARCHAR(255)'; // Valor por defecto
}


// Preprocesado del JSON con manejo avanzado de errores
function preprocessJSON(jsonData) {
    jsonData.forEach(row => {
        for (let key in row) {
            let value = row[key];

            if (typeof value === 'string') {
                value = value.trim();
            }

            if (typeof value === 'string' && /[\/-]/.test(value)) {
                const formattedDate = formatDate(value);
                if (formattedDate === null) {
                    console.error(`Fecha inválida en campo "${key}": ${value}`);
                }
                row[key] = formattedDate;
            }

            if (/nombre|name/i.test(key)) {
                row[key] = value.replace(/[^a-zA-Z\s\-@#_]/g, '');
                console.log(`Nombre procesado: "${row[key]}"`);
            }

            if (/telefono|phone/i.test(key)) {
                row[key] = value.replace(/[^0-9\(\)\-\s]/g, '');
                if (!/^\(\d{3}\)\s\d{3}-\d{4}$/.test(row[key])) {
                    console.warn(`Teléfono inválido en campo "${key}": ${value}`);
                }
            }
        }
    });
}
// Mostrar JSON formateado en la interfaz
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

// Configurar el enlace de descarga del JSON
function setupDownloadLink(jsonData, fileName) {
    downloadLinkDiv.style.display = 'block';
    const blob = new Blob([jsonData], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    convertedFileLink.href = url;
    convertedFileLink.download = fileName;
}
// Llenar el dropdown de bases de datos
function populateDatabases(databases) {
    originDbSelect.innerHTML = databases.map(db => `<option value="${db}">${db}</option>`).join('');
}
// Llenar el dropdown de tablas existentes
function populateTables(tables) {
    tableOption.innerHTML = `
        <option value="select">Seleccionar Tabla Existente</option>
        <option value="create">Crear Nueva Tabla</option>
        ${tables.map(table => `<option value="${table}">${table}</option>`).join('')}
    `;
}
