<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cita Cancelada</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            max-width: 600px;
            margin: 40px auto;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .08);
        }

        .header {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            padding: 36px 40px;
            text-align: center;
        }

        .header .icon {
            font-size: 48px;
            margin-bottom: 12px;
            display: block;
        }

        .header h1 {
            color: #fff;
            font-size: 22px;
            margin: 0;
            font-weight: 700;
        }

        .header p {
            color: rgba(255, 255, 255, .8);
            margin: 6px 0 0;
            font-size: 14px;
        }

        .body {
            padding: 36px 40px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 16px;
        }

        .detail-box {
            background: #fff5f5;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 20px 24px;
            margin: 20px 0;
        }

        .detail-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #fee2e2;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-row .emoji {
            font-size: 18px;
            flex-shrink: 0;
        }

        .detail-row strong {
            color: #991b1b;
            font-size: 13px;
            display: block;
        }

        .detail-row span {
            color: #374151;
            font-size: 15px;
            font-weight: 600;
        }

        .btn {
            display: block;
            width: fit-content;
            margin: 24px auto 0;
            background: #2563eb;
            color: #fff !important;
            text-decoration: none;
            padding: 13px 32px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
        }

        .footer {
            text-align: center;
            padding: 24px 40px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <span class="icon">❌</span>
                <h1>Cita Cancelada</h1>
                <p>{{ config('settings.clinic_name', 'CitasMédicas') }}</p>
            </div>
            <div class="body">
                <p class="greeting">Hola, {{ $patientName }}.</p>
                <p style="color:#4b5563;font-size:15px;line-height:1.6;">Lamentamos informarte que tu cita médica ha
                    sido <strong>cancelada</strong>.</p>

                <div class="detail-box">
                    <div class="detail-row">
                        <span class="emoji">📅</span>
                        <div>
                            <strong>Fecha y Hora</strong>
                            <span>{{ $appointment->date->translatedFormat('l, d \d\e F \d\e Y \a \l\a\s H:i') }}</span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <span class="emoji">👨‍⚕️</span>
                        <div>
                            <strong>Médico</strong>
                            <span>Dr. {{ $appointment->doctor->user->name ?? '—' }}</span>
                        </div>
                    </div>
                    @if($appointment->cancellation_reason)
                        <div class="detail-row">
                            <span class="emoji">📝</span>
                            <div>
                                <strong>Motivo de cancelación</strong>
                                <span>{{ $appointment->cancellation_reason }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <p style="color:#4b5563;font-size:14px;">Si deseas reagendar, puedes hacerlo desde tu portal de
                    paciente.</p>
                <a href="{{ url('/portal/appointments') }}" class="btn">Agendar nueva cita</a>
            </div>
            <div class="footer">
                <p>{{ config('settings.clinic_name', 'CitasMédicas') }}</p>
            </div>
        </div>
    </div>
</body>

</html>