import { alertErrorMessage, alertSuccessMessage } from "../common/utils.js";

const formReserva = document.getElementById("formReserva");
var loteId = -1;

function cargarDatosLote() {
    let url = new URL(window.location);
    let params = new URLSearchParams(url.search);

    loteId = params.get("loteId");
    fetch(`../../controllers/lote_controller.php?id=${loteId}`)
    .then( data => data.json() )
    .then( result => {
        if(result.status == 200){
            let lote = result.data[0];
            document.getElementById("infoUV").textContent = lote.UV;
            document.getElementById("infoManzano").textContent = lote.Manzado;
            document.getElementById("infoLote").textContent = lote.Lote;
            document.getElementById("infoEstado").textContent = lote.Estado;
        }else if( result.status == 401){
            window.location.href = "../login";
        }
    })
    .catch( error => {
        console.log(error);
    })
}

async function getUser() {
    try{
        const response = await fetch("../../controllers/login_controller.php")
        if(!response.ok) {
            throw new Error('Ocurrió un error al obtener los datos.');
        }
        const result = await response.json();
        return result.data;
    }catch(error){
        console.log(error);
        return null;
    }
}

function crearReserva() {
    formReserva.addEventListener("submit", async (event) => {
        event.preventDefault();
        
        if(loteId == -1) return;
        const cliente = {
            "nombre":     document.getElementById("name").value,
            "telefono":   document.getElementById("telefono").value,
            "comentario": document.getElementById("comentario").value,
            "ci":         document.getElementById("ci").value,
            "email":      document.getElementById("email").value,
        };
        const user = await getUser();
        const reserva ={
            "cliente": cliente,
            "user": user,
            "loteId": loteId
        }
        
        fetch("../../controllers/reserva_controller.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(reserva)
        })
        .then( data => data.json() )
        .then( result => {
            clearSpanMessage();
            if(result.status == 201){
                //formRegistro.reset();
                document.getElementById("content-container").appendChild(
                    alertSuccessMessage("Registro satisfactorio")
                );
                const containerButtons = document.getElementById("containerButtons");

                containerButtons.removeChild(document.getElementById("btnReserva"));
                
                const buttonReturn = document.createElement("a");
                buttonReturn.classList.add("btn-return");
                buttonReturn.textContent = "Ver reservas";
                buttonReturn.href = "../reserva";

                containerButtons.appendChild(buttonReturn);
            } else if( result.status == 400 && result.field ){
                if(result.field == "nombre"){
                    document.getElementById("nombreSpan").textContent = `*${result.message}`;
                }else if(result.field == "telefono"){
                    document.getElementById("telefonoSpan").textContent = `*${result.message}`;
                }
            }
            else{
                document.getElementById("content-container").appendChild(
                    alertErrorMessage(result.message)
                )
            }
        })
        .catch( error => {
            console.log( error );
        });
    })
}

function clearSpanMessage() {
    document.getElementById("nombreSpan").textContent = "";
    document.getElementById("telefonoSpan").textContent = "";
    document.getElementById("ciSpan").textContent = "";
}


cargarDatosLote();
if(formReserva != null){
    crearReserva();
}
//obtenerUsuarioAutenticado();