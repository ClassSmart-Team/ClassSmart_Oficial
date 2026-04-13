<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .header { background: #004aad; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .logo { max-width: 120px; margin-bottom: 10px; }
        .btn { background-color: #004aad; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
        .grade-box { text-align: center; background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #dee2e6; }
        .grade-number { font-size: 32px; color: #004aad; font-weight: bold; display: block; }
        .footer { font-size: 12px; color: #888; margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ $message->embed(public_path('img/Logo.svg')) }}" alt="ClassSmart" class="logo">
        <h1 style="margin: 0;">ClassSmart</h1>
    </div>

    <h2>Tu tarea ha sido calificada</h2>
    <p>Hola, <b>{{ $studentName }}</b>. El profesor ha revisado tu entrega en <b>{{ $groupName }}</b>.</p>

    <div class="grade-box">
        <span style="color: #666;">Calificación obtenida:</span>
        <span class="grade-number">{{ $grade }}</span>
        <p style="margin-top: 10px;"><b>Tarea:</b> {{ $assignmentTitle }}</p>
    </div>

    @if($feedback)
        <p><b>Comentarios del profesor:</b><br>
            <i style="color: #555;">"{{ $feedback }}"</i></p>
    @endif

    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 20px;">
        <tr>
            <td align="center">
                <a href="{{ $url }}" class="btn">Ver detalles en la plataforma</a>
            </td>
        </tr>
    </table>

    <div class="fallback-link">
        Si no puedes ver el botón, usa este enlace:<br>
        <a href="{{ $url }}">{{ $url }}</a>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} ClassSmart - Sistema de Gestión Académica.
    </div>
</div>
</body>
</html>
