using System.Collections.Generic;
using SistemaGestionAcademias.Models;

namespace SistemaGestionAcademias.ViewModels
{
    public class AsistenciaPlanillaViewModel
    {
        public int SesionId { get; set; }
        
        // --- NUEVO CAMPO: Necesario para que el botón "Cancelar" sepa a dónde volver ---
        public int ActividadId { get; set; } 
        
        public string NombreActividad { get; set; }
        public string FechaSesion { get; set; }
        public string Tema { get; set; }
        
        public List<AsistenciaItem> Asistencias { get; set; } = new List<AsistenciaItem>();
    }

    public class AsistenciaItem
    {
        public int? AsistenciaId { get; set; }
        public int AlumnoId { get; set; }
        public string NombreAlumno { get; set; }
        public string Estado { get; set; } 
        public string? Observacion { get; set; }
    }
}