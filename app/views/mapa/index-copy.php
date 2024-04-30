<?php

session_start();
error_reporting(0);

$validar = $_SESSION['user'];

if ($validar == null || $validar = '') {

    header("Location: ../login");
    die();
}
?>
<html>

<head>
    <title>URBANOR_SA</title>
    <meta charset="utf-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <link rel="stylesheet" href="../../../public/coordinates/Control.Coordinates.css" />
    <link rel="stylesheet" href="../../../public/css/style.css">
    <link rel="stylesheet" href="../../../public/css/button.css">
    <link rel="stylesheet" href="../../../public/css/preloader.css">

</head>

<body>

    <div id="preloader">
        <img src="../../../public/assets/images/barrera.png" alt="Cargando...">
    </div>


    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script src="../../../public/coordinates/Control.Coordinates.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


    <div id="map">
    </div>

    <a class="btn btn-secondary" style="position: absolute; left: 50px; top: 30px; z-index: 1;" href="../home">Volver</a>

    <!-- <select name="" style="position: absolute; right: 10px; top: 30px; z-index: 1;" id="uvSelect">
    </select>
    <select name="" style="position: absolute; right: 60px; top: 30px; z-index: 1;" id="manzanoSelect">
    </select>
    <select name="" style="position: absolute; right: 110px; top: 30px; z-index: 1;" id="loteSelect">
    </select> -->

    <div style="width: auto; position: absolute; right: 10px; top: 30px; z-index: 1;">
        <input type="text" id="uvInput" style="width: 80px;" placeholder="UV">
        <input type="text" id="mznInput" style="width: 80px;" placeholder="Manzano">
        <input type="text" id="loteInput" style="width: 80px;" placeholder="Lote">
        <button class="btn btn-primary" type="button" id="btnBuscar">Buscar</button>
    </div>

    <select id="location-selector" onchange="changeLocation(this.value)">
        <option value="51.50841025 , -0.10537595">UV:16A-M:55-L1</option>
        <option value="51.50858220 , -0.10541886">UV:16A-M:55-L2</option>
        <option value="51.50876416 , -0.10547787">UV:16A-M:55-L3</option>
        <option value="51.50896616 , -0.10555565">UV:16A-M:55-L4</option>
        <option value=" 51.50907550 , -0.10508761">UV:16A-M:55-L5</option>
        <option value="51.50908969 , -0.10493807">UV:16A-M:55-L6</option>
        <option value="51.50910597 , -0.10478653">UV:16A-M:55-L7</option>
        <option value="51.50912433 , -0.10463364">UV:16A-M:55-L8</option>
        <option value="51.50913852 , -0.10448076">UV:16A-M:55-L9</option>
        <option value="51.50915960 , -0.10433726">UV:16A-M:55-L10</option>
        <option value=" 51.50917483 , -0.10418471">UV:16A-M:55-L11</option>
        <option value=" 51.50919048 , -0.10403384">UV:16A-M:55-L12</option>
        <option value="51.50920843 , -0.10388162">UV:16A-M:55-L13</option>
        <option value="51.50922366, -0.10373678">UV:16A-M:55-L14</option>
        <option value="1.50923806 , -0.10358691">UV:16A-M:55-L15</option>
        <option value="51.50925454,  -0.10343570">UV:16A-M:55-L16</option>
        <option value="51.50927061 ,  -0.10327980">UV:16A-M:55-L17</option>
        <option value="51.50928668  ,-0.10313161">UV:16A-M:55-L18</option>
        <option value="51.50930275 , -0.10298274">UV:16A-M:55-L19</option>
        <option value="51.50931819 , -0.10283254">UV:16A-M:55-L20</option>
        <option value=" 51.50933175 , -0.10268770">UV:16A-M:55-L21</option>
        <option value="51.50938601 , -0.10212712">UV:16A-M:55-L22</option>
        <option value="51.50921865 , -0.10206610">UV:16A-M:55-L23</option>
        <option value=" 51.50913059 , -0.10204129">UV:16A-M:55-L24</option>
        <option value="51.50903460 , -0.10201380">UV:16A-M:55-L25</option>
        <option value=" 51.50893945 , -0.10198765">UV:16A-M:55-L26</option>
        <option value="51.50880130  ,-0.10194808">UV:16A-M:55-L27</option>
        <option value="51.50870239 , -0.10250062">UV:16A-M:55-L28</option>
        <option value="51.50868486,  -0.10265552">UV:16A-M:55-L29</option>
        <option value="51.50867026,  -0.10280706">UV:16A-M:55-L30</option>
        <option value="51.50865022  ,-0.10295860">UV:16A-M:55-L31</option>
        <option value="51.50863437,  -0.10310277">UV:16A-M:55-L32</option>
        <option value="51.50861684 , -0.10325633">UV:16A-M:55-L33</option>
        <option value="51.50860223 , -0.10340318">UV:16A-M:55-L34</option>
        <option value="51.50858470 , -0.10355674">UV:16A-M:55-L35</option>
        <option value="51.50856842 , -0.10370560">UV:16A-M:55-L36</option>
        <option value="51.50855090 , -0.10385513">UV:16A-M:55-L37</option>
        <option value=" 51.50853587 , -0.10400265">UV:16A-M:55-L38</option>
        <option value="51.50851876 , -0.10415487">UV:16A-M:55-L39</option>
        <option value="51.50850207 , -0.10430373">UV:16A-M:55-L40</option>
        <option value="51.50848537 , -0.10445192">UV:16A-M:55-L41</option>
        <option value="51.50846784 , -0.10460213">UV:16A-M:55-L42</option>
        <option value="51.50845073 , -0.10475703">UV:16A-M:55-L43</option>
        <option value="51.50843487 , -0.10490522">UV:16A-M:55-L44</option>

    </select>




    <script>
        var map = L.map('map').setView([51.505, -0.09], 13);


        // // Capa de imagen para el primer contenedor (sin restricciones de zoom)
        var imageUrl = 'https://urbanor.com.bo/Barrera/mapa3.0min.png',
            imageBounds = [

                [51.49, -0.12],
                [51.52, -0.05]
            ];

        //

        var imageLayer = L.imageOverlay(imageUrl, imageBounds).addTo(map);
        /////////////////////////////////////////////////
        // Capa de mosaico para el segundo contenedor (con restricciones de zoom)
        var map2Extent = [-17.44669093, -63.16734447, -17.44662184, -63.16705748];
        var map2MinZoom = 15;
        var map2MaxZoom = 25;
        var map2Bounds = new L.LatLngBounds(
            new L.LatLng(map2Extent[1], map2Extent[0]),
            new L.LatLng(map2Extent[3], map2Extent[2])
        );
        var map2Layer = L.tileLayer('{z}/{x}/{y}.png', {
            minZoom: map2MinZoom,
            maxZoom: map2MaxZoom,
            opacity: 1.0,
            tms: false
        }).addTo(map);
        // Ajustar vista del segundo contenedor
        map.fitBounds(map2Bounds);
        // Añadir leyenda al segundo contenedor
        var legend = L.control({
            position: 'bottomright',
        });
        legend.onAdd = function(map) {
            var div = L.DomUtil.create('div', 'info legend');
            div.innerHTML = '<table class=table_legend>' +
                '<tr><th colspan="3"><a href="https://urbanor.com.bo/Informacion/report.php" target="_blank">INFORMACION</a></th></tr>' +
                '<tr>' +
                '<td><img src=../../../public/assets/images/rojo.png width=15 height=15></td>' +
                '<td><b>..............Vendidos</b></td>' +
                '</tr>' +
                '<tr>' +
                '<td><img src=../../../public/assets/images/verde.jpg width=15 height=15></td>' +
                '<td><b>..........Disponibles</b></td>' +
                '</tr>' +
                '<tr>' +
                '<td><img src=../../../public/assets/images/amarillo.jpg width=15 height=15></td>' +
                '<td><b>..........Reservados</b></td>' +
                '</tr>' +
                '<tr>' +
                '<td><img src=../../../public/assets/images/morado.png width=15 height=15></td>' +
                '<td><b>.........Deposito</b></td>' +
                '</tr>' +
                '<tr>' +
                '<td><img src=../../../public/assets/images/azul.png width=15 height=15></td>' +
                '<td><b>..........Trasferido</b></td>' +
                '</tr>' +
                '<tr>' +
                '<td><img src=../../../public/assets/images/rombo1.0.png width=15 height=15></td>' +
                '<td><b>..........Parques</b></td>' +
                '</tr>' +
                '</table>';
            return div;
        };


        legend.addTo(map);
        // Deshabilitar el control de zoom para la capa de mosaico
        map2Layer.options.zoomControl = false;
        //
        //////////////////////////////////////////////////

        var c = new L.Control.Coordinates();
        c.addTo(map);

        function onMapClick(e) {
            c.setCoordinates(e);
        }

        map.on('click', onMapClick);














        ///////////////////// ZOOM DE LA IMAGEN ///////////////////////////////////////////
        var imageOverlay = L.imageOverlay(imageUrl, imageBounds).addTo(map);
        // Aplicar zoom in a la imagen después de 1 segundo
        setTimeout(function() {
            map.fitBounds(imageBounds, {
                maxZoom: 100
            }); // ajusta el mapa para que encaje en los límites de la imagen y aumenta el nivel de zoom
        }, 1000);








        //  ///////Datos de php traidos para usar en las circunferencias /////////////////////
        var locationSelector = document.getElementById('location-selector');
        var marker

        function changeLocation(coordinates) {
            if (marker) {
                map.removeLayer(marker); // Elimina el marcador anterior si existe
            }
            var latLng = coordinates.split(',').map(Number)
            // Utiliza flyTo para moverse suavemente a las nuevas coordenadas con un nivel de zoom de 13
            map.flyTo(latLng)
            // Agrega un nuevo marcador en la ubicación seleccionada
            marker = L.marker(latLng).addTo(map)
                .bindPopup('A pretty CSS popup.<br> Easily customizable.')
                .openPopup();
        }

        var imageLayer = L.imageOverlay(imageUrl, imageBounds).addTo(map).on('load', function() {
            // Ocultar el preloader cuando la imagen esté cargada
            document.getElementById('preloader').style.display = 'none';
            console.log("ok preloader");
        });


        // var latlngs = [
        //     [51.49549055, -0.09284500],
        //     [51.49562853, -0.09164337]
        // ];

        // var polygon = L.polygon(latlngs, {
        //     color: 'green'
        // }).addTo(map);

        // zoom the map to the polygon

        //map.fitBounds(polygon.getBounds());


        //var latlngs = [[-17.44567892, -63.16887785],[-17.44570422, -63.16887460]];
        //
        //var polygon = L.polygon(latlngs, {color: 'green'}).addTo(map);
    </script>


    <script src="circle_ajax.js"></script>
    <script src="index.js"></script>

</body>

</html>