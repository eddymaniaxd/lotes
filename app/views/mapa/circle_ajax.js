function dibujarCirculo(id, ubicacion, estado) {
    let circle = L.circle(
                    ubicacion, 
                    {
                        id: id, // Aquí asignamos el ID basado en el ItemCode del lote
                        className: 'circle-transition',
                        radius: 4,
                        fillColor: '',
                        color: '',
                        weight: 1,
                        opacity: 1,
                        fillOpacity: 0.8
                    }
                ).addTo(map);
    
    switch(estado) {
        case 'VENDIDO':
            circle.setStyle({ fillColor: 'red', color: 'red' });
            break;
        case 'LIBRE':
            circle.setStyle({ fillColor: 'green', color: 'green' });
            break;
        case 'RESERVADO':
            circle.setStyle({ fillColor: 'yellow', color: 'yellow' });
            break;
        case 'DEPOSITO':
            circle.setStyle({ fillColor: 'purple', color: 'purple' });
            break;
        case 'TRANSFERIDO':
            circle.setStyle({ fillColor: 'blue', color: 'blue' });
            break;
        default:
            circle.setStyle({ fillColor: 'yellow', color: 'yellow' });
    }

    return circle;
}

function dibujarPopup(lote) {
    return `
    <div class="card" style="padding: 5px;">
        <div class="card__header">
            <img src="../../../public/assets/images/barrera.png" width="265" height="200" style="border-radius:3%;">
            <div style="margin-bottom: 5px; display: flex; justify-content: space-around;">
                <div> <b>UV: </b> <span>${ lote.UV }</span> </div>
                <div> <b>MZN: </b> <span>${ lote.Manzado }</span> </div>
                <div> <b>Lote: </b> <span>${ lote.Lote }</span> </div>
            </div>
        </div>
        <hr>
        <div class="card__body">
            <div style="margin: 0; padding: 0;"> <b>Urb: ${ lote.Proyectos }  </b> </div>
            <div style="margin: 0; padding: 0;"> <b>Superficie: </b> <span>${ lote.Superficie }. </span> </div>
            <div style="margin: 0; padding: 0;"> <b>Precio M2: </b> <span>35.00 </span> </div>
            <div style="margin: 0; padding: 0;"> <b>Precio Contado: </b> <span>${ lote.Precio } </span> </div>
            <div style="margin: 0; padding: 0;"> <b>Cuota Mensual: </b> <span>${ lote.Cuota }  </span> </div>
            <div style="margin: 0; padding: 0;"> <b>Estado: </b> <span>${ lote.Estado }  </span> </div>
        </div>
        
        ${ lote.Estado == "LIBRE" ? 
            `
            <div class="card_footer" style="display: flex; justify-content: center;">
                <button type=button class=button1>
                    <a target=_blank
                        href="../reserva/crear.html?loteId=${ lote.id }">Realiza tu Reserva
                    </a>
                </button>
            </div>
            `
            :
            ``
        }
    </div>
    `;
}

function cambiarColorCirculo() {
    fetch("../../controllers/lote_controller.php?uv=12A")
    .then(res => res.json())
    .then(items => {

        let lotes = items.data;
        for(let lote of lotes){

            if(lote.Latitud != null && lote.Longitud != null){
                let ubicacion = [ lote.Latitud, lote.Longitud ];
                let circle = dibujarCirculo(lote.ItemCode, ubicacion, lote.Estado);
                let popUpInformacion = dibujarPopup(lote);
                circle.bindPopup(popUpInformacion);
            }
        }
    })
    .catch(err => {
        console.log(err);
    })
}

cambiarColorCirculo();