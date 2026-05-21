<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ficha del Paciente - {{ $patient->user->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #047857; /* Verde primary */
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #047857;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            color: #047857;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            width: 30%;
            color: #6b7280;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .mt-4 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('settings.app_name', config('app.name', 'Laravel')) }}</h1>
        <p>Ficha Médica del Paciente</p>
    </div>

    <div class="section">
        <h2 class="section-title">Información Personal</h2>
        <table>
            <tr>
                <th>Nombre Completo:</th>
                <td>{{ $patient->user->name }}</td>
            </tr>
            <tr>
                <th>Correo Electrónico:</th>
                <td>{{ $patient->user->email }}</td>
            </tr>
            <tr>
                <th>Teléfono:</th>
                <td>{{ $patient->phone ?: 'No registrado' }}</td>
            </tr>
            <tr>
                <th>Fecha de Nacimiento:</th>
                <td>{{ $patient->dob ? $patient->dob->format('d/m/Y') . ' (' . $patient->age . ' años)' : 'No registrada' }}</td>
            </tr>
            <tr>
                <th>Género:</th>
                <td>{{ ucfirst(__($patient->gender)) ?? 'No especificado' }}</td>
            </tr>
            <tr>
                <th>Dirección:</th>
                <td>{{ $patient->address ?: 'No registrada' }}</td>
            </tr>
            <tr>
                <th>Médico de Cabecera:</th>
                <td>{{ $patient->primaryDoctor ? 'Dr. ' . $patient->primaryDoctor->user->name : 'Ninguno asignado' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Datos Médicos Clave</h2>
        <table>
            <tr>
                <th>Tipo de Sangre:</th>
                <td>{{ $patient->blood_type ?: 'Desconocido' }}</td>
            </tr>
            <tr>
                <th>Alergias Conocidas:</th>
                <td style="color: {{ $patient->allergies ? '#dc2626' : '#333' }}; font-weight: {{ $patient->allergies ? 'bold' : 'normal' }};">
                    {{ $patient->allergies ?: 'Ninguna registrada' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Últimas Consultas</h2>
        @if($patient->appointments->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 25%;">Fecha</th>
                        <th style="width: 35%;">Médico / Especialidad</th>
                        <th style="width: 20%;">Estado</th>
                        <th style="width: 20%;">Receta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patient->appointments->take(5) as $appointment)
                        <tr>
                            <td>{{ $appointment->date->format('d/m/Y H:i') }}</td>
                            <td>
                                Dr. {{ $appointment->doctor->user->name }}<br>
                                <small style="color: #6b7280;">{{ $appointment->specialty->name }}</small>
                            </td>
                            <td>{{ ucfirst(__($appointment->status)) }}</td>
                            <td>{{ $appointment->prescriptions->count() > 0 ? 'Sí (' . $appointment->prescriptions->count() . ')' : 'No' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>El paciente no tiene consultas registradas en el sistema.</p>
        @endif
    </div>

    <div class="mt-4 text-center">
        <p style="font-size: 12px; color: #9ca3af;">Documento generado el {{ now()->format('d/m/Y H:i') }} - Este documento es estrictamente confidencial.</p>
    </div>
</body>
</html>
