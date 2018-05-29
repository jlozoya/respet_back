<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Esta opción controla el "guard" y la contraseña de autenticación predeterminados
    | restablece las opciones para tu aplicación. Puede cambiar estos valores predeterminados
    | según sea necesario, pero son un comienzo perfecto para la mayoría de las aplicaciones.
    |
    */
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'api'),
        'passwords' => 'users',
    ],
    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | A continuación, puede definir cada guardia de autenticación para su aplicación.
    | Por supuesto, se ha definido una gran configuración predeterminada para usted
    | aquí que usa almacenamiento de sesión y el proveedor de usuario Eloquent.
    |
    | Todos los controladores de autenticación tienen un proveedor de usuario. Esto define cómo
    | los usuarios son realmente recuperados de su base de datos u otro almacenamiento
    | mecanismos utilizados por esta aplicación para persistir los datos de su usuario.
    |
    | Compatible: "token"
    |
    */
    'guards' => [
        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Todos los controladores de autenticación tienen un proveedor de usuario. Esto define cómo
    | los usuarios son realmente recuperados de su base de datos u otro almacenamiento
    | mecanismos utilizados por esta aplicación para persistir los datos de su usuario.
    |
    | Si tiene varias tablas de usuario o modelos, puede configurar múltiples
    | fuentes que representan cada modelo / tabla. Estas fuentes pueden entonces
    | ser asignado a cualquier guardia de autenticación adicional que haya definido.
    |
    | Compatible: "base de datos", "elocuente"
    |
    */
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Aquí puede establecer las opciones para restablecer contraseñas, incluida la vista que
    | ese es su correo electrónico de restablecimiento de contraseña. También puede establecer el nombre del
    | tabla que mantiene todos los tokens de reinicio para su aplicación.
    |
    | Puede especificar varias configuraciones de restablecimiento de contraseña si tiene más
    | de una tabla de usuario o modelo en la aplicación y desea tener
    | configuración de restablecimiento de contraseña por separado en función de los tipos de usuarios específicos.
    |
    | El tiempo de caducidad es el número de minutos que el token de reinicio debe ser
    | considerado válido Esta característica de seguridad mantiene las fichas de corta vida, por lo que
    | tienen menos tiempo para adivinar. Puede cambiar esto según sea necesario.
    |
    */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'email' => 'auth.emails.password',
            'table' => 'passwords_resets',
            'expire' => 60,
        ],
    ]
];