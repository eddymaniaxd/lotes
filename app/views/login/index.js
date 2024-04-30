import { alertErrorMessage } from "../common/utils.js";

const zona = document.getElementById("zonaSelect");
const correo = document.getElementById("correo");
const password = document.getElementById("password");
const formLogin = document.getElementById("formLogin");

formLogin.addEventListener("submit", (e) => {

    e.preventDefault();
    let credenciales = {
        "zona": zona.value,
        "correo": correo.value,
        "password": password.value
    }
    fetch("../../controllers/login_controller.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(credenciales),
    })
    .then( data => data.json() )
    .then( result => {
        console.log( result );
        clearSpanMessage();
        if(result.status == 200){
            window.location.href = "../home";
        }
        else if(result.status == 400 && result.field ){
            if(result.field == "correo"){
                document.getElementById("correoSpan").textContent = `*${result.message}`;
            }
            else if(result.field == "password"){
                document.getElementById("passwordSpan").textContent = `*${result.message}`;
            }
        }else{
            formLogin.reset();
            document.getElementById("content-container").appendChild(
                alertErrorMessage("Credenciales inválidas")
            );
        }
    })
    .catch( error => {
        console.log( error );
    });
});

function clearSpanMessage() {
    document.getElementById("correoSpan").textContent = "";
    document.getElementById("passwordSpan").textContent = "";
}