function obtenerUsuarioAutenticado() {
    fetch("../../controllers/login_controller.php")
    .then( data => data.json() )
    .then( result => {
        if(result.status == 200){
            document.body.style.display="block";
            const user = result.data;
            document.getElementById("titleH1").textContent += ` ${user.nombre}`;
            if(user.rol == 2){ //Usuario normal
                document.getElementById("btnUser").style.display="none";
                document.getElementById("btnHistorialMigracion").style.display="none";
            }
        }
        else if(result.status == 401 ){
            window.location.href = "../login";
        }
    })
    .catch( erro => {
        console.log(erro);
    });
}

document.getElementById("btnLogout").addEventListener("click", () => {
    fetch("../../controllers/logout.php", {
        method: "POST"
    })
    .then( data => data.json() )
    .then( result => {
        if(result.status == 200){
            window.location.href = "../login";
        }
    })
    .catch( error => {
        console.log( error );
    });
});

obtenerUsuarioAutenticado();