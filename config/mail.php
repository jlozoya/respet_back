<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Mail Driver
    |--------------------------------------------------------------------------
    |
    | Laravel admite la función "correo" de SMTP y PHP como controladores para
    | envío de correo electrónico. Puedes especificar cuál usas en todo
    | tu aplicación aquí. Por defecto, Laravel está configurado para correo SMTP.
    |
    | Supported: "smtp", "mail", "sendmail", "mailgun", "mandrill", "ses", "log"
    |
    */
    'driver' => env('MAIL_DRIVER', 'smtp'),
    /*
    |--------------------------------------------------------------------------
    | SMTP Host Address
    |--------------------------------------------------------------------------
    |
    | Aquí puede proporcionar la dirección de host del servidor SMTP utilizado por su
    | aplicaciones. Se proporciona una opción predeterminada que es compatible con
    | el servicio de correo Mailgun que proporcionará entregas confiables.
    |
    */
    'host' => env('MAIL_HOST', 'smtp.gmail.com'),
    /*
    |--------------------------------------------------------------------------
    | SMTP Host Port
    |--------------------------------------------------------------------------
    |
    | Este es el puerto SMTP utilizado por su aplicación para enviar correos electrónicos a
    | usuarios de la aplicación. Al igual que el anfitrión, hemos establecido este valor para
    | ser compatible con la aplicación de correo electrónico Mailgun por defecto.
    |
    */
    'port' => env('MAIL_PORT', 587),
    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | Puede desear que se envíen todos los correos electrónicos enviados por su aplicación
    | con la misma dirección. Aquí, puede especificar un nombre y una dirección que sea
    | utilizado globalmente para todos los correos electrónicos que envía su aplicación.
    |
    */
    'from' => ['address' => 'jlozoya1995@gmail.com', 'name' => 'Big Thinks'],
    /*
    |--------------------------------------------------------------------------
    | Global Stream
    |--------------------------------------------------------------------------
    |
    | Estas son las configuraciones globales para la autenticación ssl
    | en caso de que dese que no sea validada para tener un servidor auto
    | certificado.
    |
    */
    'stream' => [
        'ssl' => [
            'allow_self_signed' => true,
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | E-Mail Encryption Protocol
    |--------------------------------------------------------------------------
    |
    | Aquí puede especificar el protocolo de cifrado que se debe usar cuando
    | la aplicación envía mensajes de correo electrónico. Un ajuste sensato usando el
    | El protocolo de seguridad de la capa de transporte debe proporcionar una gran seguridad.
    |
    */
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    /*
    |--------------------------------------------------------------------------
    | SMTP Server Username
    |--------------------------------------------------------------------------
    |
    | Si su servidor SMTP requiere un nombre de usuario para la autenticación, debe
    | ponerlo aquí. Esto se usará para autenticarse con su servidor en la
    | conexión. También puede establecer el valor de "contraseña" debajo de este.
    |
    */
    'username' => env('MAIL_USERNAME', 'jlozoya1995@gmail.com'),
    /*
    |--------------------------------------------------------------------------
    | SMTP Server Password
    |--------------------------------------------------------------------------
    |
    | Aquí puede configurar la contraseña requerida por su servidor SMTP para enviar
    | mensajes de tu aplicación. Esto se dará al servidor en
    | conexión para que la aplicación pueda enviar mensajes.
    |
    */
    'password' => env('MAIL_PASSWORD', 'nyjkzwkqnxwsgnpm'),
    /*
    |--------------------------------------------------------------------------
    | Sendmail System Path
    |--------------------------------------------------------------------------
    |
    | Cuando utilice el controlador "sendmail" para enviar correos electrónicos, necesitaremos saber
    | el camino hacia donde esta Sendmail en este servidor. Aqui tiene ruta predeterminada,
    | que funcionará bien en la mayoría de sus sistemas.
    |
    */
    'sendmail' => '/usr/sbin/sendmail -bs',
    /*
    |--------------------------------------------------------------------------
    | Mail "Pretend"
    |--------------------------------------------------------------------------
    |
    | Cuando esta opción está habilitada, el correo electrónico en realidad no se enviará a través de la
    | web y en su lugar se escribirá en los archivos de registro "logs" de su aplicación para
    | que usted pueda inspeccionar el mensaje. Esto es ideal para el desarrollo local.
    |
    */
    'pretend' => env('MAIL_PRETEND', true),
];