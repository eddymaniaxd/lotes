const tablaReservas = document.getElementById("tablaReservas");


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

function btnRegistrarDeposito(reserva) {
    const btn = document.createElement("button");
    btn.classList.add("btn", "btn-warning", "me-2");
    btn.style.fontSize = "12px";
    btn.innerHTML = `Registrar deposito`;
    btn.addEventListener("click", () => {
        requestPUT(reserva.reserva_id, "DEPOSITO");
    });
    return btn;
}

function btnLiberarDeposito(reserva) {
    const btn = document.createElement("button");
    btn.classList.add("btn", "btn-secondary", "me-2");
    btn.style.fontSize = "12px";
    btn.innerHTML = `Liberar deposito`;
    btn.addEventListener("click", () => {
        requestPUT(reserva.reserva_id, "RESERVADO");
    });
    return btn;
}

function btnEliminarReserva(reserva) { // Liberar reserva
    const btn = document.createElement("button");
    btn.classList.add("btn", "btn-danger", "me-2");
    btn.style.fontSize = "12px";
    btn.innerHTML = `Liberar`;
    btn.addEventListener("click", () => {
        let question = confirm("¿Seguro de liberar la reserva?");
        if(question) {
            fetch(`../../controllers/reserva_controller.php?id=${reserva.reserva_id}`, {
                method: "DELETE"
            })
            // .then( data => data.json() )
            .then( response => {
                if(response.status == 204){
                    console.log("Usuario eliminado");
                    window.location.reload();
                }else if( response.status == 403){
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

function listarReservas(reservas) {
    const tbody = document.createElement("tbody");

    reservas.forEach(async reserva =>{
        let tr = document.createElement("tr");
        tr.innerHTML += `
            <td>${ reserva.UV }</td>
            <td>${ reserva.Manzado }</td>
            <td>${ reserva.Lote }</td>
            <td>${ reserva.Estado }</td>
            <td>${ reserva.user_nombre }</td>
            <td>${ reserva.cliente_ci }</td>
            <td>${ reserva.cliente_nombre }</td>
            <td>${ reserva.cliente_telefono }</td>
            <td>${ reserva.cliente_email }</td>
            <td>${ reserva.comentario }</td>
            <td class='text-danger fs-6'>
                ${ reserva.fecha_duracion == null ? 'Sin registros' : reserva.fecha_duracion }
            </td>
        `;
        
        let td = document.createElement("td");

        //const user = await getUser();
        // if( user.rol == 1){
        //     if(reserva.Estado == "DEPOSITO"){
        //         td.append( btnLiberarDeposito(reserva) );
        //     }
        //     else if(reserva.Estado == "RESERVADO"){
        //         td.append( btnRegistrarDeposito(reserva), btnEliminarReserva(reserva) );
        //     }
        // }else{
        //     const divNone = document.createElement("div");
        //     divNone.innerHTML = `<span>-</span>`;
        //     divNone.style.display = "flex";
        //     divNone.style.justifyContent = "center";
        //     td.appendChild(divNone);
        // }
        if(reserva.Estado == "DEPOSITO"){
            td.appendChild( btnLiberarDeposito(reserva) );
        }
        else if(reserva.Estado == "RESERVADO"){
            const user = await getUser();
            //console.log(user.id, reserva.user_id);
            if(user.id == reserva.user_id){
                td.appendChild(btnEliminarReserva(reserva));
            }
            td.appendChild( btnRegistrarDeposito(reserva) );
        }
        tr.appendChild(td);

        tbody.append(tr);
        tablaReservas.appendChild( tbody );
    });
}

function requestGET() { //Obtener todos los reservas
    fetch("../../controllers/reserva_controller.php")
    .then( data => data.json() )
    .then( result => {
        if(result.status == 401){
            window.location.href = "../login";
        }else{
            document.body.style.display = "block";
            listarReservas( result.data );
        }
    })
    .catch( error => {
        console.log( error );
    })
}

function requestPUT(id, estado) {
    fetch(`../../controllers/reserva_controller.php?id=${id}`,{
        method: "PUT",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            "estado": estado
        })
    })
    .then( data => data.json() )
    .then( result => {
        if(result.status == 200){
            window.location.reload();
        }else if(result.status == 403){
            window.location.href = "../shared/forbidden.html";
        }
    })
    .catch( error => {
        console.log( error );
    });
}

if( tablaReservas != null){
    requestGET();
}