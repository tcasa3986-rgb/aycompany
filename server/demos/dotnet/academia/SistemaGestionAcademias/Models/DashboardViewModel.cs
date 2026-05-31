using System.Collections.Generic;

namespace SistemaGestionAcademias.Models
{
    public class DashboardViewModel
    {
        // Contadores Principales (Tarjetas)
        public int TotalAlumnos { get; set; }
        public int TotalInstructores { get; set; }
        public int TotalActividades { get; set; }
        public int ActividadesActivas { get; set; }

        // Tabla de Resumen
        public List<Inscripcion>? UltimasInscripciones { get; set; }

        // DATOS PARA GRÁFICOS (NUEVO)
        // Gráfico 1: Inscritos por Actividad
        public List<string> EtiquetasActividades { get; set; } = new List<string>();
        public List<int> ValoresInscritos { get; set; } = new List<int>();

        // Gráfico 2: Estado de Pagos
        public int TotalPagados { get; set; }
        public int TotalPendientes { get; set; }
    }
}