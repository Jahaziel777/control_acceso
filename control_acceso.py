import json
from datetime import datetime

# Nivel mínimo requerido para permitir acceso al almacén
# Este valor simula la política de seguridad del sistema
NIVEL_REQUERIDO = 2

# Cargar usuarios desde el archivo JSON
def cargar_usuarios():
    try:
        # Se abre el archivo en modo lectura ('r') porque solo se necesita consultar datos
        with open("usuarios.json", "r") as archivo:
            # json.load convierte el contenido del archivo en una estructura de datos de Python (lista/diccionarios)
            datos = json.load(archivo)
            return datos
    except FileNotFoundError:
        # Manejo de error si el archivo no existe
        print("ERROR: No se encontró el archivo usuarios.json")
        return []
    except json.JSONDecodeError:
        # Manejo de error si el JSON está mal estructurado
        print("ERROR: El archivo JSON está mal formado")
        return []

# Buscar usuario por ID dentro de la lista cargada desde el JSON
def buscar_usuario(usuarios, id_ingresado):
    # Se recorre cada usuario para comparar su ID con el ingresado
    for usuario in usuarios:
        if usuario["id_tarjeta"] == id_ingresado:
            # Si se encuentra coincidencia, se devuelve el usuario completo
            return usuario
    # Si no se encuentra, se retorna None (usuario no registrado)
    return None

# Registrar el resultado del acceso en el archivo de auditoría
def registrar_log(mensaje):
    # Se usa modo 'a' (append) para NO borrar registros anteriores
    with open("auditoria.txt", "a") as archivo:
        # Se obtiene fecha y hora actual para llevar trazabilidad
        fecha_hora = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        # Se guarda el evento en formato estructurado
        archivo.write(f"{fecha_hora} - {mensaje}\n")

# Programa principal (simulación del sistema de acceso)
def main():
    # Se cargan los usuarios desde el JSON
    usuarios = cargar_usuarios()

    # Si no se cargaron datos, el sistema no continúa
    if not usuarios:
        return

    # Ciclo infinito que simula un sistema en funcionamiento continuo
    while True:
        print("\n--- CONTROL DE ACCESO ---")
        
        # Simulación de lector de tarjetas (entrada manual)
        id_ingresado = input("Ingresa ID de tarjeta (o escribe 'salir'): ")

        # Permite finalizar el sistema manualmente
        if id_ingresado.lower() == "salir":
            print("Sistema finalizado.")
            break

        # Se busca el usuario en el JSON
        usuario = buscar_usuario(usuarios, id_ingresado)

        # Caso 1: Usuario encontrado
        if usuario:
            # Validación del nivel de seguridad requerido
            if usuario["nivel_seguridad"] >= NIVEL_REQUERIDO:
                # Simulación de acceso permitido (apertura de puerta)
                print("CERRADURA ABIERTA por 5 segundos")
                
                # Mensaje estructurado que se guardará en el log
                mensaje = f"ACCESO PERMITIDO - ID: {id_ingresado} - {usuario['nombre_empleado']} - ACCESO CORRECTO"
            else:
                # Usuario existe pero no tiene permisos suficientes
                print("ACCESO DENEGADO - Nivel de seguridad insuficiente")
                
                mensaje = f"ACCESO DENEGADO - ID: {id_ingresado} - {usuario['nombre_empleado']} - NIVEL INSUFICIENTE"
        
        # Caso 2: Usuario no registrado
        else:
            # Simulación de alarma del sistema
            print("ALARMA ACTIVADA - Intento de acceso no autorizado")
            
            mensaje = f"ACCESO DENEGADO - ID: {id_ingresado} - DESCONOCIDO - USUARIO NO REGISTRADO"

        # Se registra el resultado en el archivo de auditoría
        registrar_log(mensaje)

# Punto de entrada del programa
if __name__ == "__main__":
    main()