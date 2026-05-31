using ERP.Application.Interfaces;
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace ERP.Application.Features.Comunicaciones;

public class MensajeDto
{
    public int Id { get; set; }
    public string Remitente { get; set; } = string.Empty;
    public string Asunto { get; set; } = string.Empty;
    public string Contenido { get; set; } = string.Empty;
    public DateTime FechaEnvio { get; set; }
    public bool Leido { get; set; }
}

public record GetMensajesQuery(string UserEmail) : IRequest<List<MensajeDto>>;

public class GetMensajesQueryHandler : IRequestHandler<GetMensajesQuery, List<MensajeDto>>
{
    // Simulación de bandeja de entrada para demostración
    public async Task<List<MensajeDto>> Handle(GetMensajesQuery request, CancellationToken cancellationToken)
    {
        return new List<MensajeDto>
        {
            new() { Id = 1, Remitente = "Secretaría Académica", Asunto = "Confirmación de Matrícula 2026-I", Contenido = "Estimado usuario, su proceso de matrícula ha sido completado exitosamente.", FechaEnvio = DateTime.Now.AddDays(-2), Leido = true },
            new() { Id = 2, Remitente = "Tesorería", Asunto = "Recordatorio de Pago", Contenido = "Le recordamos que la pensión del mes de Mayo vence en 5 días.", FechaEnvio = DateTime.Now.AddHours(-5), Leido = false },
            new() { Id = 3, Remitente = "Docente: Carlos Quispe", Asunto = "Material de Clase - ISW", Contenido = "He subido las diapositivas de la semana 4 al portal.", FechaEnvio = DateTime.Now.AddDays(-1), Leido = false }
        };
    }
}
