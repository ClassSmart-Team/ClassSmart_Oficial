<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .header { background: #004aad; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .logo { max-width: 120px; margin-bottom: 10px; }
        .btn { background-color: #004aad; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; }
        .announcement-card { background-color: #f0f7ff; padding: 20px; border-radius: 8px; margin: 15px 0; border: 1px dashed #004aad; }
        .footer { font-size: 12px; color: #888; margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
        .badge { background: #004aad; color: white; padding: 3px 8px; border-radius: 12px; font-size: 11px; text-transform: uppercase; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ $message->embed(public_path('img/Logo.svg')) }}" alt="ClassSmart" class="logo">
        <h1 style="margin: 0;">ClassSmart</h1>
    </div>

    <h2>¡Nuevo anuncio en tu clase!</h2>
    <p>Hola, <b>{{ $userName }}</b>. Se ha publicado un nuevo mensaje en tu grupo.</p>

    <div class="announcement-card">
        <span class="badge">{{ $groupName }}</span>
        <h3 style="margin-top: 10px;">{{ $title }}</h3>
        <p style="font-style: italic; color: #555;">"{{ Str::limit($content, 150) }}"</p>
    </div>

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <a href="{{ $url }}" class="btn">Ver anuncio completo</a>
            </td>
        </tr>
    </table>

    <div style="font-size: 11px; color: #999; margin-top: 15px; text-align: center;">
        Si el botón no funciona: <a href="{{ $url }}">{{ $url }}</a>
    </div>

    <div class="footer">
        Recuerda revisar el foro regularmente para no perderte avisos importantes.<br>
        &copy; {{ date('Y') }} ClassSmart.
    </div>
</div>
</body>
</html>
