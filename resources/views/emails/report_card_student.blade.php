<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .header { background: #004aad; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .logo { max-width: 120px; margin-bottom: 10px; }
        .btn { background-color: #004aad; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
        .summary-card { background-color: #f0f7ff; padding: 25px; border-radius: 8px; margin: 20px 0; text-align: center; border: 1px solid #004aad; }
        .grade-highlight { font-size: 48px; color: #004aad; font-weight: bold; display: block; }
        .footer { font-size: 11px; color: #888; margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ $message->embed(public_path('img/Logo.svg')) }}" alt="ClassSmart" class="logo">
        <h1 style="margin: 0;">Reporte de Calificaciones</h1>
    </div>

    <div style="padding: 20px;">
        <h2>¡Buen trabajo, {{ $studentName }}!</h2>
        <p>Se han publicado oficialmente las calificaciones finales para: <b>{{ $periodName }}</b> ({{ $unitName }}).</p>

        <div class="summary-card">
            <span style="color: #666; text-transform: uppercase; letter-spacing: 1px;">Promedio del Período</span>
            <span class="grade-highlight">{{ $average }}</span>
            <p style="margin-top: 5px;">Materia: <b>{{ $groupName }}</b></p>
        </div>

        <p>Tu esfuerzo constante es la clave de tu éxito. Puedes revisar el desglose detallado de tu evaluación en tu portal.</p>

        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 20px;">
            <tr>
                <td align="center">
                    <a href="{{ $url }}" class="btn">Ver Boleta Completa</a>
                </td>
            </tr>
        </table>
    </div>

    <div style="font-size: 11px; color: #999; margin-top: 15px; text-align: center;">
        Si el botón no funciona: <a href="{{ $url }}">{{ $url }}</a>
    </div>

    <div class="footer">
        Este documento es una notificación informativa. La boleta oficial debe ser consultada en el sistema.<br>
        &copy; {{ date('Y') }} ClassSmart.
    </div>
</div>
</body>
</html>
