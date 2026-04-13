<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .header { background: #004aad; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .logo { max-width: 120px; margin-bottom: 10px; }
        .btn { background-color: #004aad; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
        .info-box { background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 5px solid #28a745; }
        .student-name { color: #004aad; font-weight: bold; }
        .footer { font-size: 12px; color: #888; margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ $message->embed(public_path('img/Logo.svg')) }}" alt="ClassSmart" class="logo">
        <h1 style="margin: 0;">ClassSmart</h1>
    </div>

    <h2>Aviso importante para padres</h2>
    <p>Estimado(a) <b>{{ $parentName }}</b>,</p>
    <p>Se ha publicado un nuevo aviso informativo relacionado con el grupo de su hijo(a): <span class="student-name">{{ $studentName }}</span>.</p>

    <div class="info-box">
        <strong>Materia/Grupo:</strong> {{ $groupName }}<br>
        <strong>Asunto:</strong> {{ $title }}
    </div>

    <p>Le recomendamos ingresar a la plataforma para estar al tanto de las novedades y avisos de la institución.</p>

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <a href="{{ $url }}" class="btn">Acceder a ClassSmart</a>
            </td>
        </tr>
    </table>

    <div style="font-size: 11px; color: #999; margin-top: 15px; text-align: center;">
        Si el botón no funciona: <a href="{{ $url }}">{{ $url }}</a>
    </div>

    <div class="footer">
        Este es un canal de comunicación oficial entre la escuela y los padres de familia.<br>
        &copy; {{ date('Y') }} ClassSmart.
    </div>
</div>
</body>
</html>
