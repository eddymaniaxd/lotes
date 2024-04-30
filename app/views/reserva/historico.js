const tablaHistorico = document.getElementById("tablaHistorico");

function listarHistoricoReservas(reservas) {
    if(tablaHistorico.querySelector("tbody")){
        tablaHistorico.removeChild(tablaHistorico.querySelector("tbody"));
    }
    const tbody = document.createElement("tbody");

    reservas.forEach(reserva => {
        let tr = document.createElement("tr");
        tr.innerHTML += `
            <td>${ reserva.UV }</td>
            <td>${ reserva.Manzado }</td>
            <td>${ reserva.Lote }</td>
            <td>${ reserva.Estado }</td>
            <td>${ reserva.user_nombre }</td>
            <td>${ reserva.cliente_nombre }</td>
            <td>${ reserva.cliente_telefono }</td>
            <td>${ reserva.cliente_email }</td>
            <td>${ reserva.comentario }</td>
            <td>${ reserva.fecha_creacion == null ? '' : reserva.fecha_creacion }</td>
            <td>${ reserva.fecha_baja == null ? '' : reserva.fecha_baja }</td>
            <td>${ reserva.fecha_deposito == null ? '' : reserva.fecha_deposito }</td>
            <td>${ reserva.fecha_liberacion == null ? '' : reserva.fecha_liberacion }</td>
        `;
        tbody.append(tr);
        tablaHistorico.appendChild( tbody );
    });
}

function requestGET() { //Obtener todos los reservas
    fetch("../../controllers/reserva_historico_controller.php")
    .then( data => data.json() )
    .then( result => {
        if(result.status == 401){
            window.location.href = "../login";
        }else{
            document.body.style.display = "block";
            listarHistoricoReservas( result.data );
        }
    })
    .catch( error => {
        console.log( error );
    })
}

if( tablaHistorico != null){
    requestGET();
}

document.getElementById("btnBuscarHistorico").addEventListener("click", () => {
    const fechaIniInput = document.getElementById("fechaIniInput");
    const fechaFinInput = document.getElementById("fechaFinInput");

    const fechaIni = fechaIniInput.value;
    const fechaFin = fechaFinInput.value;

    if(fechaIni && fechaFin){
        fetch(`../../controllers/reserva_controller.php?fechaIni=${fechaIni}&fechaFin=${fechaFin}`)
        .then( data => data.json() )
        .then( result => {
            if(result.status == 401){
                window.location.href = "../login";
            }else{
                listarHistoricoReservas( result.data );
            }
        })
        .catch( error => {
            console.log( error );
        });
    }else{
        alert("Seleccione las fechas");
    }

});

document.getElementById("btnReporte").addEventListener("click", () => {
    fetch("../../controllers/reserva_controller.php?report=xls")
    .then( response => response.blob() )
    .then( blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "historico.xls";
        document.body.appendChild( a );
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild( a );
    })
    .catch( error => {
        console.log( error );
    });
});