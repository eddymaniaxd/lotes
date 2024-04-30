export function alertSuccessMessage(message){
    let divAlert = document.createElement("div");
    divAlert.classList.add("me-4", "mb-4", "alert", "alert-success", "alert-dismissible", "fade", "show");
    divAlert.setAttribute("role", "alert");
    divAlert.style.position = "fixed";
    divAlert.style.right = "0";
    divAlert.style.bottom = "0";
    divAlert.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
    return divAlert;
}

export function alertErrorMessage(message){
    let divAlert = document.createElement("div");
    divAlert.classList.add("me-4", "mb-4", "alert", "alert-danger", "alert-dismissible", "fade", "show");
    divAlert.setAttribute("role", "alert");
    divAlert.style.position = "fixed";
    divAlert.style.right = "0";
    divAlert.style.bottom = "0";
    divAlert.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
    return divAlert;
}
