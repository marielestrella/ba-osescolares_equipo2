document.addEventListener("DOMContentLoaded", () => {
    const formulario = document.getElementById("formReporte");
    const inputImagen = document.getElementById("imagen");
    const vistaPrevia = document.getElementById("vistaPrevia");

    // 1. Validar imagen y mostrar vista previa antes de enviar
    if (inputImagen) {
        inputImagen.addEventListener("change", function () {
            const archivo = this.files[0];
            if (!archivo) {
                vistaPrevia.style.display = "none";
                return;
            }

            const tiposPermitidos = ["image/jpeg", "image/png", "image/webp", "image/jpg"];
            const tamanoMaximo = 5 * 1024 * 1024; // 5 MB

            if (!tiposPermitidos.includes(archivo.type)) {
                alert("Formato no válido. Solo se permiten imágenes JPG, PNG o WEBP.");
                this.value = "";
                vistaPrevia.style.display = "none";
                return;
            }

            if (archivo.size > tamanoMaximo) {
                alert("La imagen pesa demasiado. El tamaño máximo permitido es 5 MB.");
                this.value = "";
                vistaPrevia.style.display = "none";
                return;
            }

            // Mostrar miniatura de la foto seleccionada
            const lector = new FileReader();
            lector.onload = (e) => {
                vistaPrevia.src = e.target.result;
                vistaPrevia.style.display = "block";
            };
            lector.readAsDataURL(archivo);
        });
    }

    // 2. Validar campos de texto antes de enviar el formulario
    if (formulario) {
        formulario.addEventListener("submit", (e) => {
            const ubicacion = document.getElementById("ubicacion").value.trim();
            const tipoBano = document.getElementById("tipo_bano").value;
            const falla = document.getElementById("falla").value;
            const descripcion = document.getElementById("descripcion").value.trim();

            if (ubicacion.length < 3) {
                e.preventDefault();
                alert("Por favor escribe una ubicación válida (mínimo 3 caracteres).");
                return;
            }

            if (tipoBano === "" || falla === "") {
                e.preventDefault();
                alert("Selecciona el tipo de baño y el tipo de falla.");
                return;
            }

            if (descripcion.length < 10) {
                e.preventDefault();
                alert("Describe la falla con un poco más de detalle (mínimo 10 caracteres).");
            }
        });
    }
});
