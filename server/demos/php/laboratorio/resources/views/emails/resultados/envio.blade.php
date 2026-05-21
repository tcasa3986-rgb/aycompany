<x-mail::message>
# Hola {{ $orden->paciente->nombres }},

Tus resultados de laboratorio correspondientes a la orden **#{{ $orden->numero_orden }}** ya están listos.

Adjunto a este correo electrónico encontrarás el documento PDF oficial con la interpretación de tus análisis clínicos.

<x-mail::button :url="url('/resultados/validar/' . $orden->numero_orden)">
Validar Resultado en Línea
</x-mail::button>

En caso de requerir asistencia o para cualquier consulta sobre estos resultados, te recomendamos ponerte en contacto con tu médico tratante.

Gracias por confiar en **LabSalud**,
<br>
El equipo de Laboratorio Clínico
</x-mail::message>
