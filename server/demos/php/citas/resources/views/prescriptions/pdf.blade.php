<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Receta_Medica_{{ $prescription->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 40px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .clinic-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }

        .clinic-subtitle {
            font-size: 12px;
            color: #6b7280;
            margin: 5px 0 0 0;
        }

        .doctor-info {
            float: right;
            text-align: right;
            font-size: 12px;
            width: 45%;
        }

        .doctor-name {
            font-weight: bold;
            font-size: 16px;
            color: #1e40af;
            margin-bottom: 4px;
        }

        .patient-info {
            float: left;
            width: 50%;
            font-size: 13px;
        }

        .patient-name {
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 4px;
        }

        .clear {
            clear: both;
        }

        .rx-symbol {
            font-size: 40px;
            font-weight: bold;
            font-style: italic;
            color: #cbd5e1;
            margin: 30px 0 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px dashed #f1f5f9;
            vertical-align: top;
        }

        .med-name {
            font-weight: bold;
            color: #0f172a;
            font-size: 14px;
        }

        .med-dosage {
            color: #475569;
            font-size: 13px;
            margin-top: 4px;
        }

        .med-instructions {
            color: #64748b;
            font-size: 12px;
            font-style: italic;
            margin-top: 4px;
        }

        .notes-section {
            margin-top: 40px;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            font-size: 13px;
        }

        .notes-title {
            font-weight: bold;
            color: #475569;
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 80px;
            text-align: center;
        }

        .signature-line {
            width: 250px;
            border-top: 1px solid #94a3b8;
            margin: 0 auto 10px auto;
        }

        .signature-text {
            font-size: 12px;
            color: #64748b;
        }

        .meta-info {
            position: absolute;
            bottom: 30px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 class="clinic-name">{{ config('app.name', 'Clinica Médica') }}</h1>
            <p class="clinic-subtitle">Cuidando de su salud integralmente</p>
        </div>

        <div>
            <div class="patient-info">
                <p style="margin: 0; color: #64748b; font-size: 11px; text-transform: uppercase;">Datos del Paciente</p>
                <div class="patient-name">{{ $prescription->patient->user->name }}</div>
                <div>Fecha de nacimiento:
                    {{ \Carbon\Carbon::parse($prescription->patient->dob)->format('d/m/Y') ?? 'N/A' }}</div>
                @if($prescription->patient->allergies)
                    <div style="color: #ef4444; margin-top: 4px;"><strong>Alergias:</strong>
                        {{ $prescription->patient->allergies }}</div>
                @endif
            </div>

            <div class="doctor-info">
                <div class="doctor-name">Dr. {{ $prescription->doctor->user->name }}</div>
                <div>{{ $prescription->doctor->specialty->name }}</div>
                <div><strong>Col:</strong> {{ $prescription->doctor->collegiate_number }}</div>
                <div style="margin-top: 10px; font-size: 11px; color: #64748b;">
                    Fecha de emisión: <br>
                    <strong>{{ \Carbon\Carbon::parse($prescription->date)->format('d de F, Y') }}</strong>
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="rx-symbol">Rx</div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">Medicamento / Dosis / Frecuencia</th>
                    <th style="width: 50%;">Indicaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescription->items as $index => $item)
                    <tr>
                        <td style="color: #94a3b8;">{{ $index + 1 }}</td>
                        <td>
                            <div class="med-name">{{ $item->medication_name }}</div>
                            <div class="med-dosage">
                                {{ $item->dosage ? $item->dosage . ' | ' : '' }}
                                {{ $item->frequency }}
                                {{ $item->duration ? ' por ' . $item->duration : '' }}
                            </div>
                        </td>
                        <td>
                            <div class="med-instructions">{{ $item->instructions ?? 'Según indicación médica.' }}</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($prescription->notes)
            <div class="notes-section">
                <div class="notes-title">Recomendaciones Adicionales:</div>
                <div>{{ $prescription->notes }}</div>
            </div>
        @endif

        <div class="footer">
            <div class="signature-line"></div>
            <div class="signature-text">
                Firma y Sello<br>
                Dr. {{ $prescription->doctor->user->name }}
            </div>
        </div>

        <div class="meta-info">
            Receta N° {{ str_pad($prescription->id, 6, '0', STR_PAD_LEFT) }} | Generada el
            {{ now()->format('d/m/Y H:i') }} | Válida por 30 días
        </div>
    </div>
</body>

</html>