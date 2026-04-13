<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { background: #004aad; color: white; padding: 10px; text-align: center; border-radius: 10px 10px 0 0; }
        .footer { font-size: 12px; color: #888; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ $message->embed(public_path('img/Logo.svg')) }}" alt="ClassSmart" class="logo">
        <h1>ClassSmart</h1>
    </div>
    <h2>Hola, {{ $user->name }} {{ $user->lastname }}</h2><br>
    <p>Tu cuenta ha sido creada exitosamente en <b>ClassSmart</b>, nuestra plataforma de gestión académica.</p><br>
    <p><strong>Tus datos de acceso:</strong></p>
    <ul>
        <li><strong>Correo:</strong> {{ $user->email }}</li>
        <li><strong>Rol:</strong>
            @php
                $roles = [
                    'Student' => 'Estudiante',
                    'Parent'  => 'Padre de Familia',
                    'Teacher' => 'Profesor',
                    'Admin'   => 'Administrador'
                ];
                $descripcion = $user->role->description ?? 'Usuario';
                $rol = $roles[$descripcion] ?? $descripcion;
            @endphp
            {{ $rol }}</li>
    </ul><br>
    <p>Ya puedes acceder a la plataforma para gestionar tus actividades académicas y dar seguimiento al progreso escolar.</p><br>

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <a href="https://sutando-user.me/login" class="btn">Ir a ClassSmart</a>
            </td>
        </tr>
    </table>
    <div class="footer">
        Este es un correo automático, por favor no respondas a este mensaje.
    </div>
</div>
</body>
</html>
