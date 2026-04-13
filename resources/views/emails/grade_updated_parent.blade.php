<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .header { background: #004aad; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .logo { max-width: 120px; margin-bottom: 10px; }
        .btn { background-color: #004aad; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
        .info-card { background-color: #ffffff; padding: 15px; border: 2px solid #004aad; border-radius: 8px; margin: 15px 0; }
        .footer { font-size: 12px; color: #888; margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ $message->embed(public_path('img/Logo.svg')) }}" alt="ClassSmart" class="logo">
        <h1 style="margin: 0;">ClassSmart</h1>
    </div>

    <h2>Reporte de Calificación</h2>
    <p>Estimado(a) <b>{{ $parentName }}</b>,</p>
    <p>Le informamos que se ha registrado una nueva calificación para su hijo(a) <b>{{ $studentName }}</b>.</p>

    <div class="info-card">
        <p style="margin: 0;"><strong>Materia:</strong> {{ $groupName }}</p>
        <p style="margin: 5px 0;"><strong>Tarea:</strong> {{ $assignmentTitle }}</p>
        <p style="margin: 0;"><strong>Calificación:</strong> <span style="color: #004aad; font-weight: bold; font-size: 18px;">{{ $grade }}</span></p>
    </div>

    <p>Le invitamos a ingresar a la plataforma para revisar el progreso académico completo.</p>

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <a href="{{ $url }}" class="btn">Revisar Progreso</a>
            </td>
        </tr>
    </table>

    <div class="fallback-link">
        Si no puedes ver el botón, usa este enlace:<br>
        <a href="{{ $url }}">{{ $url }}</a>
    </div>

    <div class="footer">
        Este es un reporte automático de seguimiento escolar.<br>
        &copy; {{ date('Y') }} ClassSmart.
    </div>
</div>
</body>
</html>
