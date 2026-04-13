<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .header { background: #004aad; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .logo { max-width: 120px; margin-bottom: 10px; }
        .btn { background-color: #004aad; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
        .assignment-box { background-color: #f0f4f8; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 5px solid #004aad; }
        .footer { font-size: 12px; color: #888; margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
        .fallback-link { font-size: 11px; color: #999; margin-top: 15px; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ $message->embed(public_path('img/Logo.svg')) }}" alt="ClassSmart" class="logo">
        <h1 style="margin: 0;">ClassSmart</h1>
    </div>

    <h2>¡Tienes una nueva tarea!</h2>
    <p>Hola, <b>{{ $studentName }}</b>. Se ha publicado una nueva actividad en la plataforma.</p>

    <div class="assignment-box">
        <p style="margin: 0;"><strong>Materia:</strong> {{ $groupName }}</p>
        <p style="margin: 5px 0;"><strong>Actividad:</strong> {{ $assignmentTitle }}</p>
        <p style="margin: 0;"><strong>Fecha de entrega:</strong> {{ $dueDate }}</p>
    </div>

    <p>Asegúrate de revisar las instrucciones completas y subir tu trabajo antes de la fecha límite para evitar contratiempos.</p>

    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 20px 0;">
        <tr>
            <td align="center">
                <a href="{{ $url }}" class="btn">Abrir Tarea</a>
            </td>
        </tr>
    </table>

    <div class="fallback-link">
        Si no puedes ver el botón, usa este enlace:<br>
        <a href="{{ $url }}">{{ $url }}</a>
    </div>

    <div class="footer">
        Este es un aviso automático de <b>ClassSmart</b>.<br>
        &copy; {{ date('Y') }} Gestión Académica.
    </div>
</div>
</body>
</html>
