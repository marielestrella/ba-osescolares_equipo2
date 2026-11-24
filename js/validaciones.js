document.addEventListener("DOMContentLoaded", function () {
    const formReporte = document.getElementById("formReporte");

    if (formReporte) {
        formReporte.addEventListener("submit", function (evento) {
            const ubicacion   = document.getElementById("ubicacion").value.trim();
            const tipoBano    = document.getElementById("tipo_bano").value.trim();
            const tipoFalla   = document.getElementById("tipo_falla").value.trim();
            const descripcion = document.getElementById("descripcion").value.trim();

            if (ubicacion === "" || tipoBano === "" || tipoFalla === "" || descripcion === "") {
                evento.preventDefault();
                alert("Por favor, completa todos los campos sin dejar espacios en blanco.");
                return false;
            }

            if (descripcion.length < 5) {
                evento.preventDefault();
                alert("La descripción debe tener al menos 5 caracteres.");
                return false;
            }
        });
    }
});

// Función para asignar la acción al presionar Enviar o Archivar
function seleccionarAccion(tipo) {
    document.getElementById("accion").value = tipo;
}

// Confirmación antes de eliminar un reporte
function confirmarEliminacion(tipoReporte) {
    return confirm("¿Estás seguro de que deseas eliminar este reporte " + tipoReporte + "?");
}