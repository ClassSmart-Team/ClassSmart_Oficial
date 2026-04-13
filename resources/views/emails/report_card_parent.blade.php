<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .header { background: #1a202c; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .logo { max-width: 120px; margin-bottom: 10px; }
        .btn { background-color: #2d3748; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
        .report-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .report-table th, .report-table td { padding: 12px; border: 1px solid #e2e8f0; text-align: left; }
        .report-table th { background-color: #f7fafc; }
        .grade-cell { font-weight: bold; color: #2b6cb0; text-align: center; }
        .footer { font-size: 11px; color: #888; margin-top: 30px; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ $message->embed(public_path('img/Logo.svg')) }}" alt="ClassSmart" class="logo">
        <h1 style="margin: 0;">Boleta de Calificaciones</h1>
    </div>

    <div style="padding: 20px;">
        <p>Estimado(a) <b>{{ $parentName }}</b>,</p>
        <p>Le informamos que ya se encuentra disponible el reporte de calificaciones de su hijo(a) <b>{{ $studentName }}</b> correspondiente al período: <b>{{ $periodName }}</b>.</p>

        <table class="report-table">
            <thead>
            <tr>
                <th>Materia / Unidad</th>
                <th>Calificación</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ $groupName }} - {{ $unitName }}</td>
                <td class="grade-cell">{{ $average }}</td>
            </tr>
            </tbody>
        </table>

        <p>El seguimiento oportuno de estas evaluaciones permite fortalecer las áreas de oportunidad de los alumnos.</p>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="center">
                    <a href="{{ $url }}" class="btn">Consultar Detalles</a>
                </td>
            </tr>
        </table>
    </div>

    <div style="font-size: 11px; color: #999; margin-top: 15px; text-align: center;">
        Si el botón no funciona: <a href="{{ $url }}">{{ $url }}</a>
    </div>

    <div class="footer">
        Sistema de Gestión Académica ClassSmart.<br>
        &copy; {{ date('Y') }} Todos los derechos reservados.
    </div>
</div>
</body>
</html>
