
const map = L.map("map");

function imageOverlay(imageBounds) {
    // var imageUrl  = "../../../public/assets/images/mapa2.jpg";
    const imageUrl = "https://urbanor.com.bo/Barrera/plano1.jpg";
    const myImageOverlay = L.imageOverlay(imageUrl, imageBounds, {
        opacity: 0.8,
        interactive: true,
    });
    map.fitBounds(imageBounds);
    return myImageOverlay;
}

function imageTiles() {
    const myTileLayer =  L.tileLayer("https://urbanor.com.bo/Barrera/plano2/{z}/{x}/{y}.png", {
        minZoom: 13,
        maxZoom: 21,
        tms: true,
        attribution: 'Las barreras 1'
    });
    return myTileLayer;
}

function leyendaInformacion() {
    const legend = L.control({
        position: "bottomright"
    });
    legend.onAdd = function() {
        const legendDiv = L.DomUtil.create("div", "info legend");
        legendDiv.innerHTML = '<table class=table_legend>' +
                '<tr><th colspan="3"><a href="https://urbanor.com.bo/Informacion/report.php" target="_blank">INFORMACION</a></th></tr>' +
                '<tr>' +
                '<td><img src=../../../public/assets/images/rojo.png width=15 height=15></td>' +
                '<td><b>.........Vendidos</b></td>' +
                '</tr>' +
                '<tr>' +
                '<td><img src=../../../public/assets/images/verde.jpg width=15 height=15></td>' +
                '<td><b>........ Disponibles</b></td>' +
                '</tr>' +
                '<tr>' +
                '<td><img src=../../../public/assets/images/amarillo.jpg width=15 height=15></td>' +
                '<td><b>.........Reservados</b></td>' +
                '</tr>' +
                '<tr>' +
                '<td><img src=../../../public/assets/images/morado.png width=15 height=15></td>' +
                '<td><b>.........Deposito</b></td>' +
                '</tr>' +
                '<tr>' +
                '<td><img src=../../../public/assets/images/azul.png width=15 height=15></td>' +
                '<td><b>.........Transferido</b></td>' +
                '</tr>' +
                '<tr>' +
                '<td><img src=../../../public/assets/images/rombo1.0.png width=15 height=15></td>' +
                '<td><b>.........Parques</b></td>' +
                '</tr>' +
                '</table>';
        return legendDiv;
    }
    return legend;
}

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
        case 'BLOQUEADO':
            circle.setStyle({ fillColor: 'gray', color: 'gray' });
            break;
        default:
            console.log("Sin estados");
            //circle.setStyle({ fillColor: 'yellow', color: 'yellow' });
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
                <a href="../reserva/crear.html?loteId=${ lote.id }" class="btn btn-primary">
                    <span class="text-white">Realizar reserva</span>
                </a>
            </div>
            `
            :
            ``
        }
    </div>
    `;
}

function requestEstadoLotes(){
    fetch("../../controllers/lote_controller.php")
    .then(res => res.json())
    .then(items => {

        let lotes = items.data;
        for(let lote of lotes){

            if(lote.Latitud != null && lote.Longitud != null){
                //console.log(lote);
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


function changeLocation(lote) { //Agregar marcador al mapa
    var marker
    if (marker) {
        map.removeLayer(marker); // Elimina el marcador anterior si existe
    }
    const coordinates = `${lote.Latitud}, ${lote.Longitud}`;
    var latLng = coordinates.split(',').map(Number)
    // Utiliza flyTo para moverse suavemente a las nuevas coordenadas con un nivel de zoom de 13
    map.flyTo(latLng, 18);
    // Agrega un nuevo marcador en la ubicación seleccionada
    marker = L.marker(latLng).addTo(map)
        .bindPopup(`Estas en lote: ${lote.Lote}`)
        .openPopup();
}

function buscarLoteUvMznLote() {
    document.getElementById("btnBuscar").addEventListener("click", () => { //Buscar lote por uv-mzno-lote
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
}

 function setMyCoordinates(){
    let c = new L.Control.Coordinates({
        precision:14
    });
    c.addTo(map); // Agregamos el control al mapa
    map.on('click', function(e) {
        c.setCoordinates(e);
    });
 }
////////////////
function addCircleToMap(map, lat, lng) {
    var circle = L.circle([lat, lng], {
        className: 'circle-transition',
        radius: 3,
        fillColor: 'green',
        color: 'green',
        weight: 1,
        opacity: 1,
        fillOpacity: 0.8
    }).addTo(map);
}
addCircleToMap(map,  -17.44774579922538, -63.16731579601766);
/////////////
//Si es verificado realiza todas las operaciones
function verificarAuth(){
    fetch("../../controllers/check_auth_controller.php")
    .then( response => {
        if(response.status == 401) {
            window.location.href = "../login";
        }else if( response.status == 200){
            document.body.style.display = "block";

            map.setView( L.latLng(-17.44975518060025, -63.15991852720831), 16 );
            const bounds = [ 
                                [ -17.458608915621834, -63.14721626085109 ],
                                [ -17.44090144557866, -63.172620793565535 ] 
                            ];

            // imageOverlay(bounds).addTo(map).on("load", function() {
            //     document.getElementById("preloader").style.display = "none";
            // });
            imageTiles().addTo(map).on("load", function() {
                document.getElementById("preloader").style.display = "none";
            });
            setMyCoordinates();
            //addCircleToMap().addTo(map);
            leyendaInformacion().addTo(map);
            requestEstadoLotes();
            buscarLoteUvMznLote();
        }
    })
    .catch( error => {
        console.error(error);
    })
}

verificarAuth();

var coordControl = L.Control.Coordinates(); // Creamos una instancia del control
coordControl.addTo(map); // Añadimos el control al mapa



