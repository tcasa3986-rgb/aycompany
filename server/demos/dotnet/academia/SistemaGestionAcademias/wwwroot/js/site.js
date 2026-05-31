// Esperar a que cargue el documento
document.addEventListener("DOMContentLoaded", function () {
    
    // Lógica para el botón de alternar menú (Toggle)
    var sidebarToggle = document.getElementById("sidebarToggle");
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", function (event) {
            event.preventDefault();
            document.body.classList.toggle("sb-sidenav-toggled");
            
            // Hack para que los gráficos o calendarios se redibujen si cambia el ancho
            setTimeout(function() {
                window.dispatchEvent(new Event('resize'));
            }, 300);
        });
    }

    // Inicializar Tooltips de Bootstrap (opcional, para estética)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});