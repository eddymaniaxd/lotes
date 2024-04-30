import { alertErrorMessage, alertSuccessMessage } from "../common/utils.js";

const tablaHistorialMigracion = document.getElementById("tablaHistorialMigracion");

fetch("../../controllers/login_controller.php") //Verificar auth
.then( response => response.json() )
.then( result => {
    if(result.status == 401){
        window.location.href = "../login";
    }
    else if(result.data.rol == 2){
        window.location.href = "../shared/forbidden.html";
    }
})
.catch( error => {
    console.error(error);
});

document.getElementById("btnVolver").addEventListener("click", () => {
    window.location.href = "../home";
})


function mostrarDatosEnModal(lote){
    document.getElementById("modalTitle").textContent = lote.Proyectos;
    const containerModalCardBody = document.getElementById("containerModalCardBody");
    containerModalCardBody.innerHTML = `
        <div>
            <span class="fw-bold">ItemCode: </span><span>${ lote.ItemCode }</span>
        </div>
        <div>
            <span class="fw-bold">Modelo: </span><span>${ lote.Modelo }</span>
        </div>
        <div>
            <span class="fw-bold">UV: </span><span>${ lote.UV }</span>
        </div>
        <div>
            <span class="fw-bold">Manzano: </span><span>${ lote.Manzado }</span>
        </div>
        <div>
            <span class="fw-bold">Lote: </span><span>${ lote.Lote }</span>
        </div>
        <div>
            <span class="fw-bold">Superficie: </span><span>${ lote.Superficie }</span>
        </div>
        <div>
            <span class="fw-bold">Precio: </span><span>${ lote.Precio }</span>
        </div>
        <div>
            <span class="fw-bold">Estado: </span><span>${ lote.Estado }</span>
        </div>
    `;
}

function buttonMostrar(itemCode) {
    const btnMostrar = document.createElement("button");
    btnMostrar.classList.add("btn", "btn-success");
    btnMostrar.setAttribute("data-bs-toggle", "modal");
    btnMostrar.setAttribute("data-bs-target", "#modalLote")
    btnMostrar.textContent = "Mostrar";
    btnMostrar.addEventListener("click", () => {
        fetch(`../../controllers/lote_controller.php?itemcode=${itemCode}`)
        .then( response => response.json() )
        .then( result => {
            if(result.status == 401) {
                window.location.href = "../login";
            }
            else if(result.status == 403) {
                window.location.href = "../shared/forbidden.html";
            }
            else if(result.status == 200) {
                if(result.data.length > 0){
                    const lote = result.data[0];
                    mostrarDatosEnModal(lote);
                }
            }
        })
        .catch(error => {
            console.error(error);
        })
    });
    return btnMostrar;
}
function listarHistoriales(historiales) {

    if(tablaHistorialMigracion.querySelector("tbody")){
        tablaHistorialMigracion.removeChild(tablaHistorialMigracion.querySelector("tbody"));
    }

    const tbody = document.createElement("tbody");

    historiales.forEach(historial => {
        let tr = document.createElement("tr");
        tr.innerHTML += `
            <td>${ historial.item_code_lote }</td>
            <td>${ historial.estado_anterior }</td>
            <td>${ historial.estado_entrante }</td>
            <td>${ historial.fecha_creacion }</td>
        `;

        const td = document.createElement("td");
        td.appendChild( buttonMostrar(historial.item_code_lote));
        tr.appendChild( td );

        tbody.append(tr);
        tablaHistorialMigracion.appendChild( tbody );
    });
}

function requestGET() { //Obtener todos los historiales
    fetch("../../controllers/historial_migracion_controller.php")
    .then( response => response.json() )
    .then( result => {
        if(result.status == 401) {
            window.location.href = "../login";
        }
        else if(result.status == 403) {
            window.location.href = "../shared/forbidden.html";
        }
        else if(result.status == 200) {
            document.body.style.display = "block";
            listarHistoriales( result.data );
        }
    })
    .catch( error => {
        console.log( error );
    })
}

function mostrarResultadosActualizados(historiales){

    document.getElementById("btnActualizarEstados").style.display = "none";
    document.getElementById("btnVolver").style.display = "none";

    const spanMessageResult = document.getElementById("spanMessage");
    spanMessageResult.textContent = `Resultados: ${ historiales.length }`;
    spanMessageResult.style.display = "block";

    const btnRegresar = document.getElementById("btnRegresar");
    btnRegresar.style.display = "block";
    btnRegresar.addEventListener("click", () => {
        window.location.reload();
    });
}

//Actualizar estados
document.getElementById("btnActualizarEstados").addEventListener("click", () => {
    
    document.getElementById("btnVolver").disabled = true;
    document.getElementById("btnActualizarEstados").disabled = true;
    document.getElementById("loading").style.display = "block";

    fetch("../../controllers/lote_controller.php",{
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
    })
    .then( response => response.json() )
    .then( result => {
        if(result.status == 401) {
            window.location.href = "../login";
        }
        else if(result.status == 403) {
            window.location.href = "../shared/forbidden.html";
        }
        else if(result.status == 200) {
            mostrarResultadosActualizados( result.data );
            listarHistoriales( result.data );
        }else{
            document.body.appendChild( alertErrorMessage(result.message) );
        }
        document.getElementById("btnVolver").disabled = false;
        document.getElementById("btnActualizarEstados").disabled = false;
        document.getElementById("loading").style.display = "none";
    })
    .catch(error => {
        console.error(error);
        document.getElementById("loading").style.display = "none";
    });
});

//Actualizar toodos los lotes, sin importar los estados
document.getElementById("btnActualizarLotes").addEventListener("click", () => {

    document.getElementById("loading").style.display = "block";

    fetch("../../controllers/lote_controller.php",{
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                "type": "update"
            }),
    })
    .then( response => response.json() )
    .then( result => {
        if(result.status == 401) {
            window.location.href = "../login";
        }
        else if(result.status == 403) {
            window.location.href = "../shared/forbidden.html";
        }
        else if(result.status == 200) {
            document.body.appendChild( alertSuccessMessage(result.message) );
        }else{
            document.body.appendChild( alertErrorMessage(result.message) );
        }
        document.getElementById("loading").style.display = "none";
    })
    .catch(error => {
        console.error(error);
        document.getElementById("loading").style.display = "none";
    });
});

if(tablaHistorialMigracion != null){
    requestGET();
}