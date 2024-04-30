
const tablaUsuarios = document.getElementById("tablaUsuarios");
const formRegistro = document.getElementById("formRegistro");
const btnReporte = document.getElementById("btnReporte");

function btnEditar(usuario) {
    const btn = document.createElement("button");
    btn.classList.add("btn", "btn-warning", "me-2");
    btn.innerHTML = `<i class="fa fa-edit"></i>`;
    btn.addEventListener("click", () => {
        window.location.href = `../user/editar.html?id=${usuario.id}`;
    });
    return btn;
}

function btnEliminar(usuario) {
    const btn = document.createElement("button");
    btn.classList.add("btn", "btn-danger");
    btn.innerHTML = `<i class="fa fa-trash"></i>`;
    btn.addEventListener("click", () => {
        let question = confirm("¿Seguro de eliminar este registro?");
        if(question) {
            fetch(`../../controllers/user_controller.php?id=${usuario.id}`, {
                method: "DELETE"
            })
            // .then( data => data.json() )
            .then( response => {
                if(response.status == 204){
                    console.log("Usuario eliminado");
                    window.location.reload();
                }else if(response.status == 401){
                    window.location.href = "../login";
                }else if(response.status == 403){
                    window.location.href = "../shared/forbidden.html";
                }
            })
            .catch( error => {
                console.log( error );
            });
        }
    });
    return btn;
}

function listarUsuarios(usuarios) {
    const tbody = document.createElement("tbody");

    usuarios.forEach(usuario => {
        let tr = document.createElement("tr");
        tr.innerHTML += `
            <td>${ usuario.nombre }</td>
            <td>${ usuario.telefono }</td>
            <td>${ usuario.correo }</td>
            <td>${ usuario.password }</td>
            <td>${ usuario.fecha }</td>
            <td>${ usuario.rol == 1 ? "Admin" : "User" }</td>
        `;
        let td = document.createElement("td");
        td.append( btnEditar(usuario), btnEliminar(usuario) );
        tr.appendChild(td);

        tbody.append(tr);
        tablaUsuarios.appendChild( tbody );
    });
}

function requestGET() { //Obtener todos los usuarios
    fetch("../../controllers/user_controller.php")
    .then( data => data.json() )
    .then( result => {
        if(result.status == 401){
            window.location.href = "../login";
        }
        else if(result.status == 403){
            window.location.href = "../shared/forbidden.html";
        }
        else{
            document.body.style.display = "block";
            listarUsuarios( result.data );
        }
    })
    .catch( error => {
        console.log( error );
    })
}

function requestPOST() { //Agregar un usuario
    // const btnGuardar = document.getElementById("btnGuardar");

    formRegistro.addEventListener("submit", (e) => {
        e.preventDefault();

        const usuario = {
            "nombre": document.getElementById("nombre").value,
            "telefono": document.getElementById("telefono").value,
            "correo": document.getElementById("correo").value,
            "password": document.getElementById("password").value,
            "rol": document.getElementById("rol").value,
        }
    
        fetch("../../controllers/user_controller.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(usuario)
        })
        .then( data => data.json() )
        .then( result => {
            clearSpanMessage();
            if(result.status == 201){
                formRegistro.reset();
                window.location.href = "../user";
            }
            else if(result.status == 400 && result.field){
                switch(result.field){
                    case "nombre":
                        document.getElementById("nombreSpan").textContent = `*${result.message}`;
                        break;
                    case "telefono":
                        document.getElementById("telefonoSpan").textContent = `*${result.message}`;
                        break;
                    case "correo":
                        document.getElementById("correoSpan").textContent = `*${result.message}`;
                        break;
                    case "password":
                        document.getElementById("passwordSpan").textContent = `*${result.message}`;
                        break;
                    default: "no-field";
                }
            }
            else if(result.status == 401) {
                window.location.href = "../login";
            }
        })
        .catch( error => {
            console.log( error );
        });
    });
}

function reporteUsuarios() {
    btnReporte.addEventListener("click", () => {
        fetch("../../controllers/user_controller.php?report=xls")
        .then( response => response.blob() )
        .then( blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = "usuarios.xls";
            document.body.appendChild( a );
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild( a );
        })
        .catch( error => {
            console.log( error );
        });
    });
}

function clearSpanMessage() {
    document.getElementById("nombreSpan").textContent = "";
    document.getElementById("telefonoSpan").textContent = "";
    document.getElementById("correoSpan").textContent = "";
    document.getElementById("passwordSpan").textContent = "";
}

if( tablaUsuarios != null){
    requestGET();
}

if(formRegistro != null){
    requestPOST();
}

if( btnReporte != null){
    reporteUsuarios();
}
