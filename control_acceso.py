import json
from datetime import datetime

# Nivel mínimo requerido para permitir acceso
NIVEL_REQUERIDO = 2

# Cargar usuarios desde JSON
def cargar_usuarios():
    try:
        with open("usuarios.json", "r") as archivo:
            datos = json.load(archivo)
            return datos
    except FileNotFoundError:
        print("ERROR: No se encontró el archivo usuarios.json")
        return []
    except json.JSONDecodeError:
        print("ERROR: El archivo JSON está mal formado")
        return []

# Buscar usuario por ID
def buscar_usuario(usuarios, id_ingresado):
    for usuario in usuarios:
        if usuario["id_tarjeta"] == id_ingresado:
            return usuario
    return None

# Registrar en auditoría
def registrar_log(mensaje):
    with open("auditoria.txt", "a") as archivo:
        fecha_hora = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        archivo.write(f"{fecha_hora} - {mensaje}\n")

# Programa principal
def main():
    usuarios = cargar_usuarios()

    if not usuarios:
        return

    while True:
        print("\n--- CONTROL DE ACCESO ---")
        id_ingresado = input("Ingresa ID de tarjeta (o escribe 'salir'): ")

        if id_ingresado.lower() == "salir":
            print("Sistema finalizado.")
            break

        usuario = buscar_usuario(usuarios, id_ingresado)

        if usuario:
            if usuario["nivel_seguridad"] >= NIVEL_REQUERIDO:
                print("CERRADURA ABIERTA por 5 segundos")
                mensaje = f"ACCESO PERMITIDO - ID: {id_ingresado} - {usuario['nombre_empleado']} - ACCESO CORRECTO"
            else:
                print("ACCESO DENEGADO - Nivel de seguridad insuficiente")
                mensaje = f"ACCESO DENEGADO - ID: {id_ingresado} - {usuario['nombre_empleado']} - NIVEL INSUFICIENTE"
        else:
            print("ALARMA ACTIVADA - Intento de acceso no autorizado")
            mensaje = f"ACCESO DENEGADO - ID: {id_ingresado} - DESCONOCIDO - USUARIO NO REGISTRADO"

        registrar_log(mensaje)

if __name__ == "__main__":
    main()