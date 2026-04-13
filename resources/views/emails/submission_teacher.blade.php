<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .header { background: #004aad; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .logo { max-width: 120px; margin-bottom: 10px; }
        .btn { background-color: #004aad; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
        .info-box { background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 5px solid #004aad; }
        .status-late { color: #d9534f; font-weight: bold; }
        .footer { font-size: 12px; color: #888; margin-top: 30px; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ $message->embed(public_path('img/Logo.svg')) }}" alt="ClassSmart" class="logo">
        <h1 style="margin: 0;">ClassSmart</h1>
    </div>

    <h2>Nueva entrega recibida</h2>
    <p>Hola, profesor(a) <b>{{ $teacherName }}</b>. Un estudiante ha realizado una entrega.</p>

    <div class="info-box">
        <strong>Estudiante:</strong> {{ $studentName }}<br>
        <strong>Tarea:</strong> {{ $assignmentTitle }}<br>
        <strong>Grupo:</strong> {{ $groupName }}<br>
        <strong>Estatus:</strong> <span class="{{ $isLate ? 'status-late' : '' }}">{{ $status }}</span>
    </div>

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <a href="{{ $url }}" class="btn">Ir a calificar</a>
            </td>
        </tr>
    </table>

    <div style="font-size: 11px; color: #999; margin-top: 15px; text-align: center;">
        Si el botón no funciona: <a href="{{ $url }}">{{ $url }}</a>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} ClassSmart - Panel Docente.
    </div>
</div>
</body>
</html>
