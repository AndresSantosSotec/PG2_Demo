import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score
import joblib

# Datos de ejemplo para entrenar la IA
# Puedes agregar más columnas y tipos de datos para mejorar el entrenamiento.
data = {
    'columna_origen': [
        'nombre', 'edad', 'direccion', 'fecha_nacimiento', 'email',
        'id_tip_nego', 'TipoNegocio', 'Contribullente',
        'id_queja', 'Ctip_queja', 'ccodQueja',
        'id_usuario', 'Nombre', 'Correo', 'Usuario', 'Contraseña', 'Seccion', 'inicio',
        'id', 'matricula', 'nombre', 'fecha_nacimiento', 'telefono', 'email', 'nivel_id', 'created_at', 'updated_at',
        'id_cliente', 'Nombre', 'Celular', 'Direccion'
    ],
    'tipo_origen': [
        'varchar', 'int', 'varchar', 'date', 'varchar',
        'int', 'varchar(50)', 'varchar(30)',
        'int', 'varchar(50)', 'varchar(5)',
        'int', 'varchar(50)', 'varchar(50)', 'varchar(50)', 'varchar(50)', 'tinyint', 'datetime',
        'bigint', 'varchar(10)', 'varchar(120)', 'date', 'varchar(20)', 'varchar(50)', 'bigint', 'timestamp', 'timestamp',
        'int', 'varchar(35)', 'varchar(15)', 'varchar(50)'
    ],
    'columna_destino': [
        'nombre', 'edad', 'direccion', 'nacimiento', 'correo',
        'id_tip_nego', 'nombre_tipo_negocio', 'tipo_contribuyente',
        'id_queja', 'descripcion_queja', 'codigo_queja',
        'id_usuario', 'nombre_completo', 'correo_electronico', 'nombre_usuario', 'clave', 'seccion_usuario', 'inicio_sesion',
        'id_alumno', 'num_matricula', 'nombre_alumno', 'nacimiento_alumno', 'num_telefono', 'correo_alumno', 'id_nivel', 'creacion_alumno', 'actualizacion_alumno',
        'id_cliente', 'nombre_cliente', 'telefono_cliente', 'direccion_cliente'
    ],
    'tipo_destino': [
        'varchar', 'int', 'varchar', 'date', 'varchar',
        'int', 'varchar(50)', 'varchar(30)',
        'int', 'varchar(50)', 'varchar(5)',
        'int', 'varchar(50)', 'varchar(50)', 'varchar(50)', 'varchar(50)', 'tinyint', 'datetime',
        'bigint', 'varchar(10)', 'varchar(120)', 'date', 'varchar(20)', 'varchar(50)', 'bigint', 'timestamp', 'timestamp',
        'int', 'varchar(35)', 'varchar(15)', 'varchar(50)'
    ],
    'compatibilidad': [
        1, 1, 1, 0, 1,  # Datos de ejemplo originales
        1, 1, 1,  # tb_tip_negocio
        1, 1, 1,  # tb_tip_queja
        1, 1, 1, 1, 1, 1, 1,  # tb_usuarios
        1, 1, 1, 1, 1, 1, 1, 1, 1,  # alumnos
        1, 1, 1, 1  # tb_clientes
    ]
}

# Convertimos los datos en un DataFrame de pandas
df = pd.DataFrame(data)

# Variables independientes (X) y dependientes (y)
X = df[['columna_origen', 'tipo_origen', 'columna_destino', 'tipo_destino']]
y = df['compatibilidad']

# Convertir columnas de texto a numéricas mediante get_dummies
X = pd.get_dummies(X)

# Dividir datos en entrenamiento (80%) y prueba (20%)
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Crear y entrenar el modelo
clf = RandomForestClassifier()
clf.fit(X_train, y_train)

# Evaluar el modelo
y_pred = clf.predict(X_test)
print(f'Precisión del modelo: {accuracy_score(y_test, y_pred)}')

# Guardar el modelo entrenado para usarlo más adelante
joblib.dump(clf, 'modelo_migracion.pkl')

# Guardar las columnas del entrenamiento para garantizar que los datos de prueba sigan el mismo formato
joblib.dump(X.columns, 'columnas.pkl')

print("Modelo entrenado y guardado como 'modelo_migracion.pkl'")
