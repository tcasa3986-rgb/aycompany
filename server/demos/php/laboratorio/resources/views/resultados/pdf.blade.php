<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resultado Médicos - {{ $orden->numero_orden }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #8e54e9;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .logo-title {
            color: #8e54e9;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .info-paciente {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-paciente th {
            background-color: #f4f5f9;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
            width: 15%;
        }
        .info-paciente td {
            padding: 8px;
            border: 1px solid #ddd;
            width: 35%;
        }
        .resultados-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .resultados-table th {
            background-color: #f4f5f9;
            color: #1a2750;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }
        .resultados-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .area-header {
            background-color: #e5e9f5;
            font-weight: bold;
            color: #4776e6;
        }
        .critico {
            color: red;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 120px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .firma {
            border-top: 1px solid #333;
            width: 200px;
            margin: 40px auto 10px auto;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td width="70%">
                    <h1 class="logo-title">LabSalud - Laboratorio Clínico</h1>
                    <p style="margin: 5px 0;">Av. Principal 123, Ciudad Salud | Tel: (01) 456-7890<br>
                    Email: info@labsalud.pe | Web: www.labsalud.pe</p>
                </td>
                <td width="30%" style="text-align: right;">
                    <img src="data:image/svg+xml;base64,{{ $qrcode }}" alt="QR Validador">
                    <p style="font-size: 9px; margin-top: 5px;">Escanee para validar<br>autenticidad</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-paciente">
        <tr>
            <th>Paciente:</th>
            <td><strong>{{ $orden->paciente->nombre_completo }}</strong></td>
            <th>H. Clínica:</th>
            <td>{{ $orden->paciente->historia_clinica }}</td>
        </tr>
        <tr>
            <th>Edad/Sexo:</th>
            <td>{{ $orden->paciente->edad ? $orden->paciente->edad.' años' : 'N/E' }} / {{ $orden->paciente->sexo ?? 'N/E' }}</td>
            <th>Orden Nro:</th>
            <td><strong>{{ $orden->numero_orden }}</strong></td>
        </tr>
        <tr>
            <th>Médico:</th>
            <td>{{ $orden->medico->nombre_completo ?? 'Particular' }}</td>
            <th>Fecha:</th>
            <td>{{ $orden->fecha_registro->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <h3 style="color: #1a2750; text-transform: uppercase;">INFORME DE RESULTADOS</h3>

    <table class="resultados-table">
        <thead>
            <tr>
                <th width="35%">EXAMEN</th>
                <th width="20%">RESULTADO</th>
                <th width="15%">UNIDADES</th>
                <th width="30%">VALORES DE REFERENCIA</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $resultadoPorArea = $orden->detalles->groupBy('prueba.area.nombre');
            @endphp
            
            @foreach($resultadoPorArea as $area => $detalles)
                <tr>
                    <td colspan="4" class="area-header">{{ strtoupper($area) }}</td>
                </tr>
                @foreach($detalles as $detalle)
                    @php $res = $detalle->resultado; @endphp
                    @if($res)
                        <tr>
                            <td>{{ $detalle->prueba->nombre }}<br>
                                <small style="color:#777; font-size:9px;">Método: {{ $res->metodo }}</small>
                            </td>
                            <td>
                                <strong class="{{ $res->interpretacion == 'Crítico' ? 'critico' : '' }}">
                                    {{ $res->valor }}
                                </strong>
                                @if($res->interpretacion != 'Normal')
                                    <span style="font-size: 10px;">({{ $res->interpretacion }})</span>
                                @endif
                            </td>
                            <td>{{ $res->unidad }}</td>
                            <td>{!! nl2br(e($res->valores_referencia)) !!}</td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ $detalle->prueba->nombre }}</td>
                            <td colspan="3" style="color: #999;"><em>Sin resultados registrados</em></td>
                        </tr>
                    @endif
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="firma">
            <strong>TM. Validador</strong><br>
            C.T.M.P. 12345<br>
            Director de Laboratorio
        </div>
        <p style="font-size: 10px; color: #777;">
            Este documento representa un informe certificado. La validación del mismo puede ser comprobada mediante el código QR seguro.<br>
            Los resultados deben ser interpretados por un Médico de acuerdo con el cuadro clínico del paciente.
        </p>
    </div>

</body>
</html>
