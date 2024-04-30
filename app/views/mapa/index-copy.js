const tablaLotes = document.getElementById("tablaLotes");

function btnReservar(lote) {
    const btn = document.createElement("button");
    btn.classList.add("btn", "btn-primary", "me-2");
    btn.innerHTML = `<i class="fa-regular fa-bookmark"></i>`;
    btn.addEventListener("click", () => {
        // window.location.href = `../reserva/crear.html?id=${ lote.id }`;
        window.open(`../reserva/crear.html?loteId=${ lote.id }`);
    });
    return btn;
}

function listarLotes(lotes) {
    const tbody = document.createElement("tbody");

    lotes.forEach(lote => {
        let tr = document.createElement("tr");
        tr.innerHTML += `
            <td>${ lote.ItemCode   }</td>
            <td>${ lote.UV         }</td>
            <td>${ lote.Manzado    }</td>
            <td>${ lote.Lote       }</td>
            <td>${ lote.Estado     }</td>
            <td>${ lote.Superficie }</td>
            <td>${ lote.Modelo     }</td>
            <td>${ lote.Precio     }</td>
            <td>${ lote.Cuota      }</td>
        `;
        let td = document.createElement("td");
        td.append( btnReservar(lote) );
        tr.appendChild(td);

        tbody.append(tr);
        tablaLotes.appendChild( tbody );
    });
}

function requestGET() {
    fetch("../../controllers/lote_controller.php")
    .then( data => data.json() )
    .then( result => {
        // console.log(result.data)
        cargarSelectInput( result.data );
    })
    .catch( error => {
        console.log( error );
    })
}

// if(tablaLotes != null) {
//     requestGET();
// }

function cargarSelectInput(lotes) {
    // const uvSelect = document.getElementById("uvSelect");
    // const manzanoSelect = document.getElementById("manzanoSelect");
    const loteSelect = document.getElementById("loteSelect");

    lotes.forEach( lote => {
        const optionUv = document.createElement("option");
        const optionManzano = document.createElement("option");
        const optionLote = document.createElement("option");

        // optionUv.value = lote.UV;
        // optionUv.textContent = `UV-${lote.UV}`;

        // optionManzano.value = lote.Manzado;
        // optionManzano.textContent = `Mzn-${lote.Manzado}`;

        optionLote.value = lote.Lote;
        optionLote.textContent = `Lote-${lote.Lote}`;

        uvSelect.appendChild(optionUv);
        manzanoSelect.appendChild(optionManzano);
        loteSelect.appendChild(optionLote);
    });
}

function changeLocation(lote) {
    var marker
    if (marker) {
        map.removeLayer(marker); // Elimina el marcador anterior si existe
    }
    const coordinates = `${lote.Latitud}, ${lote.Longitud}`;
    var latLng = coordinates.split(',').map(Number)
    // Utiliza flyTo para moverse suavemente a las nuevas coordenadas con un nivel de zoom de 13
    map.flyTo(latLng)
    // Agrega un nuevo marcador en la ubicación seleccionada
    marker = L.marker(latLng).addTo(map)
        .bindPopup(`Estas en lote: ${lote.Lote}`)
        .openPopup();
}

document.getElementById("btnBuscar").addEventListener("click", () => {

    const uvInput = document.getElementById("uvInput");
    const mznInput = document.getElementById("mznInput");
    const loteInput = document.getElementById("loteInput");
    
    fetch(`../../controllers/lote_controller.php?uv=${uvInput.value}&manzano=${mznInput.value}&lote=${loteInput.value}`)
    .then( data => data.json() )
    .then( result => {
        if(result.status == 200){
            if(result.data.length > 0){
                const lote = result.data[0];
                if(lote.Latitud != null && lote.Longitud != null){
                    if(map != null){
                        changeLocation(lote);
                    }
                }else{
                    alert("Coordenadas no encontrado");
                }
            }else{
                alert("Resultados no encontrado");
            }
        }
    })
    .catch( error => {
        console.log( error );
    })
})