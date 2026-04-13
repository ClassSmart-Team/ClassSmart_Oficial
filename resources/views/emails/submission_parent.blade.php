<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .header { background: #28a745; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .logo { max-width: 120px; margin-bottom: 10px; }
        .success-icon { font-size: 40px; }
        .footer { font-size: 12px; color: #888; margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ $message->embed(public_path('img/Logo.svg')) }}" alt="ClassSmart" class="logo">
        <h1 style="margin: 0;">Tarea Entregada</h1>
    </div>

    <div style="text-align: center; padding: 20px;">
        <p style="font-size: 18px;">Estimado(a) <b>{{ $parentName }}</b>,</p>
        <p>Le informamos que su hijo(a) <b>{{ $studentName }}</b> ha entregado la siguiente actividad:</p>

        <div style="background: #f0fff4; border: 1px solid #c6f6d5; padding: 15px; border-radius: 8px; display: inline-block; width: 80%;">
            <strong style="color: #2f855a;">{{ $assignmentTitle }}</strong><br>
            <span style="color: #4a5568;">Materia: {{ $groupName }}</span>
        </div>
    </div>

    <p style="text-align: center; color: #666;">El docente revisará la entrega próximamente. Podrá ver la calificación en su panel una vez sea publicada.</p>

    <div style="font-size: 11px; color: #999; margin-top: 15px; text-align: center;">
        Si el botón no funciona: <a href="{{ $url }}">{{ $url }}</a>
    </div>

    <div class="footer">
        Este es un mensaje de confirmación para su tranquilidad.<br>
        &copy; {{ date('Y') }} ClassSmart.
    </div>
</div>
</body>
</html>
