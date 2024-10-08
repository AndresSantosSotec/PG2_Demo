from flask import Flask, request, jsonify
import joblib
import pandas as pd

app = Flask(__name__)

# Cargar el modelo entrenado
modelo = joblib.load('modelo_migracion.pkl')

@app.route('/predecir', methods=['POST'])
def predecir():
    datos = request.json
    df = pd.DataFrame([datos])

    # Convertir las columnas de texto a las mismas que en el entrenamiento
    df = pd.get_dummies(df)

    # Asegurarse de que las columnas tengan el mismo orden que el entrenamiento
    columnas_modelo = joblib.load('columnas.pkl')  # Guardar las columnas durante el entrenamiento
    df = df.reindex(columns=columnas_modelo, fill_value=0)

    # Realizar la predicción
    prediccion = modelo.predict(df)

    return jsonify({'compatibilidad': int(prediccion[0])})

if __name__ == '__main__':
    app.run(debug=True)
