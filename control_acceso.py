import json  # Permite trabajar con archivos en formato JSON
from datetime import datetime  # Permite obtener fecha y hora actual

# Nivel mínimo requerido para permitir el acceso
NIVEL_REQUERIDO = 1

# Función para cargar los usuarios desde el archivo JSON
def cargar_usuarios():
    try:
        # Abre el archivo en modo lectura
        with open("usuarios.json", "r") as archivo:
            datos = json.load(archivo)  # Convierte el contenido JSON a estructuras de Python
            return datos  # Retorna la lista de usuarios
    except FileNotFoundError:
        # Se ejecuta si el archivo no existe
        print("ERROR: No se encontró el archivo usuarios.json")
        return []
    except json.JSONDecodeError:
        # Se ejecuta si el JSON está mal formado
        print("ERROR: El archivo JSON está mal formado")
        return []

# Función para buscar un usuario por su ID de tarjeta
def buscar_usuario(usuarios, id_ingresado):
    for usuario in usuarios:  # Recorre la lista de usuarios
        if usuario["id_tarjeta"] == id_ingresado:
            return usuario  # Retorna el usuario si encuentra coincidencia
    return None  # Retorna None si no se encuentra

# Función para registrar cada intento en el archivo de auditoría
def registrar_log(mensaje):
    # Abre el archivo en modo append para agregar sin borrar contenido previo
    with open("auditoria.txt", "a") as archivo:
        # Obtiene la fecha y hora actual en formato legible
        fecha_hora = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        # Escribe el registro en el archivo
        archivo.write(f"{fecha_hora} - {mensaje}\n")

# Función principal que controla el flujo del programa
def main():
    # Carga los usuarios desde el JSON
    usuarios = cargar_usuarios()

    # Si no hay usuarios, se termina el programa
    if not usuarios:
        return

    # Bucle infinito para permitir múltiples intentos de acceso
    while True:
        print("\n--- CONTROL DE ACCESO ---")
        id_ingresado = input("Ingresa ID de tarjeta (o escribe 'salir'): ")

        # Permite salir del sistema
        if id_ingresado.lower() == "salir":
            print("Sistema finalizado.")
            break

        # Busca el usuario en la lista
        usuario = buscar_usuario(usuarios, id_ingresado)

        if usuario:
            # Validación del nivel de seguridad
            if usuario["nivel_seguridad"] >= NIVEL_REQUERIDO:
                print("CERRADURA ABIERTA por 5 segundos")
                # Se genera mensaje de acceso permitido
                mensaje = f"ACCESO PERMITIDO - ID: {id_ingresado} - {usuario['nombre_empleado']}"
            else:
                print("ACCESO DENEGADO - Nivel de seguridad insuficiente")
                # Se genera mensaje indicando nivel insuficiente
                mensaje = f"ACCESO DENEGADO - ID: {id_ingresado} - {usuario['nombre_empleado']} - NIVEL INSUFICIENTE"
        else:
            # Usuario no encontrado
            print("ALARMA ACTIVADA - Intento de acceso no autorizado")
            mensaje = f"ACCESO DENEGADO - ID: {id_ingresado}"

        # Registra el resultado en el archivo de auditoría
        registrar_log(mensaje)

# Punto de entrada del programa
if __name__ == "__main__":
    main()