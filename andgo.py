import pandas as pd

def exportar_a_csv(datos, nombre_archivo):
    # Convertir los datos a un DataFrame de pandas
    df = pd.DataFrame(datos)

    # Guardar el DataFrame como un archivo CSV
    df.to_csv(nombre_archivo, index=False)

# Ejemplo de uso
datos = [
    {"Timestamp": "2024-04-25 10:00:00", "Señal": "Compra", "Par": "BTC/USD"},
    {"Timestamp": "2024-04-25 10:05:00", "Señal": "Venta", "Par": "ETH/BTC"},
    # Más datos aquí...
]

exportar_a_csv(datos, "señales_crypto_idx.csv")
