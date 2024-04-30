import requests
import threading
import time

def obtenerTodasReservas(header):
    url = 'https://urbanor.com.bo/Barrera/Barrera3/app/controllers/reserva_controller.php'
    response = requests.get(url, headers=header)
    return response

def liberarReservaPorFechaDuracion(header, id):
    url = "https://urbanor.com.bo/Barrera/Barrera3/app/controllers/reserva_controller.php?id={}".format(id)
    response = requests.delete(url, headers=header)
    if(response.status_code == 204):
        print("Reserva con id: {} eliminado".format(id))
    else:
        print("La solicitud DELETE no fue exitosa. Código de estado:", response.status_code)

def login():
    url = 'https://urbanor.com.bo/Barrera/Barrera3/app/controllers/login_controller.php'
    credenciales = {
        "zona": "las-barreras-1",
        "correo": "henryeddy97@gmail.com",
        "password": "12887442"
    }
    authResponse = requests.post(url, json=credenciales)
    if(authResponse.status_code == 200):
        cookie_session = authResponse.headers['Set-Cookie'].split(';')[0]
        headers = {'Cookie': cookie_session}
        return headers
    else:
        return None
    
def verificarReservas():
    while True:
        header = login()
        reservas = obtenerTodasReservas(header)
        reservasFormatter = reservas.json()["data"]
        fechaActual = time.time()
        for reserva in reservasFormatter:
            if reserva["fecha_duracion"] != None:
                fechaReserva = time.mktime(time.strptime(reserva["fecha_duracion"], "%Y-%m-%d %H:%M:%S"))
                if fechaActual >= fechaReserva:
                    liberarReservaPorFechaDuracion(header, reserva["reserva_id"])
                time.sleep(10)
            
threadTareas = threading.Thread(target=verificarReservas)
threadTareas.start()

while True:
    time.sleep(1)
    