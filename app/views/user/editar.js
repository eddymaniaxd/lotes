const formRegistroEdit = document.getElementById("formRegistroEdit");
var id = -1;

function cargarDatosEditar() {
    let url = new URL(window.location);
    let params = new URLSearchParams(url.search);

    id = params.get("id");
    fetch(`../../controllers/user_controller.php?id=${id}`)
    .then( data => data.json() )
    .then( result => {
        if(result.status == 200){
            let user = result.data[0];
            document.getElementById("nombre").value = user.nombre;
            document.getElementById("telefono").value = user.telefono;
            document.getElementById("correo").value = user.correo;
            document.getElementById("password").value = user.password;
        }else if( result.status == 401){
            window.location.href = "../login";
        }else if( result.status == 403){
            window.location.href = "../shared/forbidden.html";
        }
    })
    .catch( error => {
        console.log(error);
    })
}

function requestPUT() {

    formRegistroEdit.addEventListener("submit", (e) => {
        e.preventDefault();

        const usuario = {
            "nombre": document.getElementById("nombre").value,
            "telefono": document.getElementById("telefono").value,
            "correo": document.getElementById("correo").value,
            "password": document.getElementById("password").value,
        }

        if(id == -1) return;

        fetch(`../../controllers/user_controller.php?id=${id}`,{
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(usuario)
        })
        .then( data => data.json() )
        .then( result => {
            if(result.status == 200){
                console.log("Usuario actualizado");
                formRegistroEdit.reset();
                window.location.href = "../user";
            }else if(result.status == 403){
                window.location.href = "../shared/forbidden.html";
            }
        })
        .catch( error => {
            console.log( error );
        });
    });
}

cargarDatosEditar();
if(formRegistroEdit != null) {
    requestPUT();
}