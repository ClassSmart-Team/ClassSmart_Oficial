<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .header { background: #004aad; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .logo { max-width: 120px; margin-bottom: 10px; }
        .btn { background-color: #004aad; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
        .feedback-card { background-color: #fcf8e3; border: 1px solid #faebcc; color: #8a6d3b; padding: 20px; border-radius: 8px; margin: 20px 0; position: relative; }
        .feedback-card::before { content: '"'; font-size: 50px; color: #e5e5e5; position: absolute; top: -10px; left: 10px; }
        .footer { font-size: 12px; color: #888; margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ $message->embed(public_path('img/Logo.svg')) }}" alt="ClassSmart" class="logo">
        <h1 style="margin: 0;">ClassSmart</h1>
    </div>

    <h2>Tu profesor ha dejado comentarios</h2>
    <p>Hola, <b>{{ $studentName }}</b>. Tienes nueva retroalimentación sobre tu tarea <b>{{ $assignmentTitle }}</b> en la materia de {{ $groupName }}.</p>

    <div class="feedback-card">
        <p style="margin: 0; position: relative; z-index: 1;">{{ $feedback }}</p>
    </div>

    <p>Revisar tus comentarios te ayudará a mejorar en tus próximas actividades.</p>

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <a href="{{ $url }}" class="btn">Ver mi entrega</a>
            </td>
        </tr>
    </table>

    <div style="font-size: 11px; color: #999; margin-top: 15px; text-align: center;">
        Si el botón no funciona: <a href="{{ $url }}">{{ $url }}</a>
    </div>

    <div class="footer">
        Este mensaje fue enviado desde el portal académico ClassSmart.<br>
        &copy; {{ date('Y') }} ClassSmart.
    </div>
</div>
</body>
</html>
