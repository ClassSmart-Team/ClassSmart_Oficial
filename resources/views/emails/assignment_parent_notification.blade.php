<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .header { background: #004aad; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .logo { max-width: 150px; margin-bottom: 10px; }
        .btn { background-color: #004aad; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
        .footer { font-size: 12px; color: #888; margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
        .info-box { background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 5px solid #004aad; }
        .student-name { color: #004aad; font-weight: bold; }
        .fallback-link { font-size: 11px; color: #666; word-break: break-all; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ $message->embed(public_path('img/Logo.svg')) }}" alt="ClassSmart" class="logo">
        <h1 style="margin: 0;">ClassSmart</h1>
    </div>

    <h2>Nueva actividad</h2>
    <p>Hola, <b>{{ $parentName }}</b>.</p>
    <p>Te informamos que se ha publicado una nueva tarea para tu hijo(a): <span class="student-name">{{ $studentName }}</span>.</p>

    <div class="info-box">
        <p style="margin: 0;"><strong>Materia:</strong> {{ $groupName }}</p>
        <p style="margin: 5px 0;"><strong>Tarea:</strong> {{ $assignmentTitle }}</p>
        <p style="margin: 0;"><strong>Fecha límite:</strong> {{ $dueDate }}</p>
    </div>

    <p>Es fundamental el seguimiento de estas actividades para asegurar el cumplimiento de los objetivos académicos.</p>

    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 20px 0;">
        <tr>
            <td align="center">
                <a href="{{ $url }}" class="btn">Revisar Tarea</a>
            </td>
        </tr>
    </table>

    <div class="fallback-link">
        Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:<br>
        <a href="{{ $url }}">{{ $url }}</a>
    </div>

    <div class="footer">
        Este es un correo automático generado por <b>ClassSmart</b>. Por favor, no respondas a este mensaje.<br>
        &copy; {{ date('Y') }} ClassSmart - Gestión Académica.
    </div>
</div>
</body>
</html>
