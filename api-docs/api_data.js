define({ "api": [
  {
    "type": "get",
    "url": "/user/:id",
    "title": "Obtiene la información de un usuario por su id.",
    "version": "0.0.1",
    "name": "GetUserById",
    "group": "Admin",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Id del usuario.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "Object",
            "optional": false,
            "field": "user",
            "description": "<p>Información del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "user.id",
            "description": "<p>Id del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.name",
            "description": "<p>Nombre de usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.first_name",
            "description": "<p>Primer nombre del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.last_name",
            "description": "<p>Apellido del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.gender",
            "description": "<p>Genero del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.email",
            "description": "<p>Email del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.media_id",
            "description": "<p>Id de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Object",
            "optional": true,
            "field": "user.media",
            "description": "<p>Información de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.media.id",
            "description": "<p>Id de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.media.type",
            "description": "<p>Tipo de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.media.url",
            "description": "<p>Url de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.media.alt",
            "description": "<p>Alt de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.media.width",
            "description": "<p>Ancho de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.media.height",
            "description": "<p>Alto de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.phone",
            "description": "<p>Telefono del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.lang",
            "description": "<p>Idioma del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.birthday",
            "description": "<p>Fecha de nacimiento del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Boolean",
            "optional": true,
            "field": "user.confirmed",
            "description": "<p>Si el correo del usuario está confirmado.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.grant_type",
            "description": "<p>Fuente desde la que se registro el usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.role",
            "description": "<p>Rol del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address_id",
            "description": "<p>Id de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Object",
            "optional": true,
            "field": "user.address",
            "description": "<p>Dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.id",
            "description": "<p>Información del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.contry",
            "description": "<p>País de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.administrative_area_level_1",
            "description": "<p>Estado de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.administrative_area_level_2",
            "description": "<p>Municipio de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.route",
            "description": "<p>Calle de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address.street_number",
            "description": "<p>Numero de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address.postal_code",
            "description": "<p>Código postal de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address.lat",
            "description": "<p>Latitud de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address.lng",
            "description": "<p>Longitud postal de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.created_at",
            "description": "<p>Información del usuario.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "406": [
          {
            "group": "406",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_NOT_FOUND",
            "description": "<p>En caso de que no se encuentre el usuario relacionado el token.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "/users",
    "title": "Obtiene la lista de usuario.",
    "version": "0.0.1",
    "name": "GetUsers",
    "group": "Admin",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "page",
            "description": "<p>Número de la página a consultar.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "search",
            "description": "<p>Texto en caso en caso de querer realizar una búsqueda.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "Object[]",
            "optional": false,
            "field": "user",
            "description": "<p>Información del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "user.id",
            "description": "<p>Id del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.name",
            "description": "<p>Nombre de usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.first_name",
            "description": "<p>Primer nombre del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.last_name",
            "description": "<p>Apellido del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.gender",
            "description": "<p>Genero del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.email",
            "description": "<p>Email del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.media_id",
            "description": "<p>Id de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Object",
            "optional": true,
            "field": "user.media",
            "description": "<p>Información de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.media.id",
            "description": "<p>Id de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.media.type",
            "description": "<p>Tipo de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.media.url",
            "description": "<p>Url de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.media.alt",
            "description": "<p>Alt de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.media.width",
            "description": "<p>Ancho de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.media.height",
            "description": "<p>Alto de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.phone",
            "description": "<p>Telefono del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.lang",
            "description": "<p>Idioma del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.birthday",
            "description": "<p>Fecha de nacimiento del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Boolean",
            "optional": true,
            "field": "user.confirmed",
            "description": "<p>Si el correo del usuario está confirmado.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.grant_type",
            "description": "<p>Fuente desde la que se registro el usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.role",
            "description": "<p>Rol del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address_id",
            "description": "<p>Id de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Object",
            "optional": true,
            "field": "user.address",
            "description": "<p>Dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.id",
            "description": "<p>Información del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.contry",
            "description": "<p>País de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.administrative_area_level_1",
            "description": "<p>Estado de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.administrative_area_level_2",
            "description": "<p>Municipio de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.route",
            "description": "<p>Calle de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address.street_number",
            "description": "<p>Numero de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address.postal_code",
            "description": "<p>Código postal de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address.lat",
            "description": "<p>Latitud de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address.lng",
            "description": "<p>Longitud postal de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.created_at",
            "description": "<p>Información del usuario.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Admin"
  },
  {
    "type": "put",
    "url": "/user/avatar/:id",
    "title": "Establecer el avatar de un usuario por su id.",
    "version": "0.0.1",
    "name": "SetAvatarById",
    "group": "Admin",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Id del usuario a actualizar.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "file",
            "description": "<p>Es el archivo a almacenar, puede ser de tipo archivo se recomienda usar.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "file_name",
            "description": "<p>Nombre del archivo.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "type",
            "description": "<p>Define el tipo de archivo, en caso de ser base64 se debe indicar.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "202": [
          {
            "group": "202",
            "type": "String",
            "optional": false,
            "field": "fileUrl",
            "description": "<p>Url donde se almaceno el archivo.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Admin"
  },
  {
    "type": "put",
    "url": "/user/role/:id",
    "title": "Actualizar el rol del usuario por su id.",
    "version": "0.0.1",
    "name": "UpdateUserAddressById",
    "group": "Admin",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Id del usuario a actualizar.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "role",
            "description": "<p>Nuevo rol del usuario.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "202": [
          {
            "group": "202",
            "type": "String",
            "optional": false,
            "field": "role",
            "description": "<p>Nuevo rol del usuario.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "404": [
          {
            "group": "404",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_NOT_REGISTRED",
            "description": "<p>Cuando el usuario no está registrado.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Admin"
  },
  {
    "type": "put",
    "url": "/user/address/:id",
    "title": "Actualizar la dirección de un usuario por su id.",
    "version": "0.0.1",
    "name": "UpdateUserAddressById",
    "group": "Admin",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Id del usuario a actualizar.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "country",
            "description": "<p>País del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "administrative_area_level_1",
            "description": "<p>Estado del ususario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "administrative_area_level_2",
            "description": "<p>Ciudad del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "route",
            "description": "<p>Calle del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "street_number",
            "description": "<p>Número del domicilio del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "postal_code",
            "description": "<p>Código postal del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "lat",
            "description": "<p>Latitud del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "lng",
            "description": "<p>Longitud del usuario.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "201": [
          {
            "group": "201",
            "type": "Address",
            "optional": false,
            "field": "userAddress",
            "description": "<p>Información de la dirección del usuario.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "406": [
          {
            "group": "406",
            "type": "QueryException",
            "optional": false,
            "field": "error",
            "description": "<p>Error al ejecutar la consulta.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Admin"
  },
  {
    "type": "put",
    "url": "/user/:id",
    "title": "Actualizar la información de un usuario por su id.",
    "version": "0.0.1",
    "name": "UpdateUserById",
    "group": "Admin",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Id del usuario a actualizar.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "name",
            "description": "<p>Nombre de usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "first_name",
            "description": "<p>Primer nombre.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "last_name",
            "description": "<p>Apellido.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "gender",
            "description": "<p>Genero del usuario 'male' | 'female' | 'other'.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "phone",
            "description": "<p>Número de teléfono del usuario.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "201": [
          {
            "group": "201",
            "type": "Object",
            "optional": false,
            "field": "user",
            "description": "<p>Información del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": false,
            "field": "user.id",
            "description": "<p>Id del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.name",
            "description": "<p>Nombre de usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.first_name",
            "description": "<p>Primer nombre del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.last_name",
            "description": "<p>Apellido del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "user.gender",
            "description": "<p>Genero del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.email",
            "description": "<p>Email del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": true,
            "field": "user.media_id",
            "description": "<p>Id de la imagen del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "user.phone",
            "description": "<p>Telefono del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.lang",
            "description": "<p>Idioma del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "user.birthday",
            "description": "<p>Fecha de nacimiento del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Boolean",
            "optional": true,
            "field": "user.confirmed",
            "description": "<p>Si el correo del usuario está confirmado.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.grant_type",
            "description": "<p>Fuente desde la que se registro el usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.role",
            "description": "<p>Rol del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": true,
            "field": "user.address_id",
            "description": "<p>Id de la dirección del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.created_at",
            "description": "<p>Información del usuario.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "406": [
          {
            "group": "406",
            "type": "QueryException",
            "optional": false,
            "field": "error",
            "description": "<p>Error al ejecutar la consulta.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Admin"
  },
  {
    "type": "put",
    "url": "/user/email/:id",
    "title": "Actualizar el email de un usuario por su id.",
    "version": "0.0.1",
    "name": "UpdateUserEmailById",
    "group": "Admin",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>Nuevo email del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "grant_type",
            "description": "<p>Nombre de la fuente con la que se inicia sesión 'facebook' | 'google' | 'password'.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "201": [
          {
            "group": "201",
            "type": "Object",
            "optional": false,
            "field": "user",
            "description": "<p>Información del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": false,
            "field": "user.id",
            "description": "<p>Id del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.name",
            "description": "<p>Nombre de usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.first_name",
            "description": "<p>Primer nombre del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.last_name",
            "description": "<p>Apellido del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "user.gender",
            "description": "<p>Genero del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.email",
            "description": "<p>Email del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": true,
            "field": "user.media_id",
            "description": "<p>Id de la imagen del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "user.phone",
            "description": "<p>Telefono del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.lang",
            "description": "<p>Idioma del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "user.birthday",
            "description": "<p>Fecha de nacimiento del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Boolean",
            "optional": true,
            "field": "user.confirmed",
            "description": "<p>Si el correo del usuario está confirmado.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.grant_type",
            "description": "<p>Fuente desde la que se registro el usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.role",
            "description": "<p>Rol del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": true,
            "field": "user.address_id",
            "description": "<p>Id de la dirección del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.created_at",
            "description": "<p>Información del usuario.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "404": [
          {
            "group": "404",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_NOT_FOUND",
            "description": "<p>Cuando no se encontró</p>"
          }
        ],
        "406": [
          {
            "group": "406",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_EMAIL_ALREADY_EXISTS",
            "description": "<p>Cuando un usuario ya tiene un correo registrado.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Admin"
  },
  {
    "type": "put",
    "url": "/user/lang/:id",
    "title": "Actualizar el idioma de un usuario por su id.",
    "version": "0.0.1",
    "name": "UpdateUserLangById",
    "group": "Admin",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Id del usuario a actualizar.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "lang",
            "description": "<p>Nuevo idioma del usuario.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "202": [
          {
            "group": "202",
            "type": "String",
            "optional": false,
            "field": "lang",
            "description": "<p>Regresa el idioma que se registro.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "404": [
          {
            "group": "404",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_NOT_REGISTRED",
            "description": "<p>Cuando el usuario no fue localizado.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Admin"
  },
  {
    "type": "get",
    "url": "/analytics",
    "title": "Obtener las analíticas de los usuario en la base de datos.",
    "version": "0.0.1",
    "name": "GetBasicAnalytics",
    "group": "Analytics",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "Object",
            "optional": false,
            "field": "analytics",
            "description": "<p>Objeto con la información de los usuarios en la base de datos.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "analytics.users_number",
            "description": "<p>Numero de usuarios.</p>"
          },
          {
            "group": "200",
            "type": "Object",
            "optional": false,
            "field": "analytics.gender",
            "description": "<p>Objeto con la información del genero de los usuarios.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "analytics.gender.male_number",
            "description": "<p>Numero de hombres.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "analytics.gender.female_number",
            "description": "<p>Numero de mujeres.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "analytics.supports_number",
            "description": "<p>Numero de solicitudes de soporte.</p>"
          },
          {
            "group": "200",
            "type": "Object",
            "optional": false,
            "field": "analytics.ages",
            "description": "<p>Objeto con la información de la edad de los usuarios.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "analytics.ages.children",
            "description": "<p>Numero de niños.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "analytics.ages.teens",
            "description": "<p>Numero de adolecentes.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "analytics.ages.young_adults",
            "description": "<p>Numero de adultos jovenes</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "analytics.ages.unknown",
            "description": "<p>Numero de usuarios con edad desconocida.</p>"
          },
          {
            "group": "200",
            "type": "Object",
            "optional": false,
            "field": "analytics.grant_types",
            "description": "<p>Objeto con la información del origen desde donde se registraron los usuarios.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "analytics.grant_types.app",
            "description": "<p>Numero de ususarios registrados desde la aplicación.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "analytics.grant_types.facebook",
            "description": "<p>Numero de ususarios registrados desde facebook.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "analytics.grant_types.google",
            "description": "<p>Numero de ususarios registrados desde google.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Analytics"
  },
  {
    "type": "post",
    "url": "/analytics/users/registration Obtener la cantidad de usuario",
    "title": "registrados en la base de datos.",
    "version": "0.0.1",
    "name": "GetUsersRegistration",
    "group": "Analytics",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "interval",
            "description": "<p>Intervalo de tiempo a consultar.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "Object[]",
            "optional": false,
            "field": "analytics",
            "description": "<p>Objeto con la información de los usuarios en la base de datos.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "analytics.created_at",
            "description": "<p>Fecha en que se registraron.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "analytics.users",
            "description": "<p>Numero de ususario.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Analytics"
  },
  {
    "type": "get",
    "url": "/",
    "title": "Obtiene la versión de laravel lumen.",
    "version": "0.0.1",
    "name": "Version",
    "group": "Api",
    "permission": [
      {
        "name": "none"
      }
    ],
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "version",
            "description": "<p>Versión de laravel lumen.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Api"
  },
  {
    "type": "post",
    "url": "/bulletin",
    "title": "Crear una nueva noticia.",
    "version": "0.0.1",
    "name": "Create_Bulletin",
    "group": "Bulletin",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "title",
            "description": "<p>Título de la noticia.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "description",
            "description": "<p>Descripción de la noticia.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "date",
            "description": "<p>Fecha de la noticia.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "202": [
          {
            "group": "202",
            "type": "Bulletin",
            "optional": false,
            "field": "bulletin",
            "description": "<p>Noticia creada.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Bulletin"
  },
  {
    "type": "delete",
    "url": "/bulletin/:id",
    "title": "Eliminar una noticia por su id.",
    "version": "0.0.1",
    "name": "DeleteBulletin",
    "group": "Bulletin",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Id de la noticia.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "204": [
          {
            "group": "204",
            "type": "Null",
            "optional": false,
            "field": "Null",
            "description": "<p>Noticia eliminada.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "404": [
          {
            "group": "404",
            "type": "String",
            "optional": false,
            "field": "SERVER.BULLETIN_NOT_FOUND",
            "description": "<p>Cuando no se encontró una noticia</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Bulletin"
  },
  {
    "type": "get",
    "url": "/bulletins",
    "title": "Consultar noticias por pagina.",
    "version": "0.0.1",
    "name": "GetBulletins",
    "group": "Bulletin",
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "page",
            "description": "<p>Número de la página a consultar.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "Pagination",
            "optional": false,
            "field": "pagination",
            "description": "<p>Noticias paginadas.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Bulletin"
  },
  {
    "type": "put",
    "url": "/bulletin/img",
    "title": "Establece la imagen de la noticia.",
    "version": "0.0.1",
    "name": "SetImg",
    "group": "Bulletin",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "File",
            "optional": false,
            "field": "file",
            "description": "<p>Es el archivo a almacenar.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "file_name",
            "description": "<p>Nombre del archivo.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "type",
            "description": "<p>Define el tipo de archivo, en caso de ser base64 se debe indicar.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "202": [
          {
            "group": "202",
            "type": "Bulletin",
            "optional": false,
            "field": "bulletin",
            "description": "<p>Noticia creada.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Bulletin"
  },
  {
    "type": "get",
    "url": "/bulletins",
    "title": "Consultar noticias por id.",
    "version": "0.0.1",
    "name": "ShowBulletin",
    "group": "Bulletin",
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Id de la noticia a consultar.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "Bulletin",
            "optional": false,
            "field": "bulletin",
            "description": "<p>Una noticia.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Bulletin"
  },
  {
    "type": "put",
    "url": "/bulletin",
    "title": "Actualizar una noticia.",
    "version": "0.0.1",
    "name": "UpdateBulletin",
    "group": "Bulletin",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "title",
            "description": "<p>Título de la noticia.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "description",
            "description": "<p>Descripción de la noticia.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "date",
            "description": "<p>Fecha de la noticia.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "media_id",
            "description": "<p>Referencia a un archivo de medios.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "202": [
          {
            "group": "202",
            "type": "Bulletin",
            "optional": false,
            "field": "bulletin",
            "description": "<p>Noticia creada.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Bulletin"
  },
  {
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "optional": false,
            "field": "varname1",
            "description": "<p>No type.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "varname2",
            "description": "<p>With type.</p>"
          }
        ]
      }
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "./api-docs/main.js",
    "group": "C__xampp_htdocs_respet_back_api_docs_main_js",
    "groupTitle": "C__xampp_htdocs_respet_back_api_docs_main_js",
    "name": ""
  },
  {
    "type": "post",
    "url": "/password/email",
    "title": "Recupera una contraseña con un email.",
    "version": "0.0.1",
    "name": "PostEmail",
    "group": "Password",
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "grant_type",
            "description": "<p>Nombre de la fuente con la que se inicia sesión 'facebook' | 'google' | 'password'.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>Email del usuario.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "SERVER.EMAIL_READY",
            "description": "<p>Confirmación de que se envió un correo para recuperar la contraseña.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "404": [
          {
            "group": "404",
            "type": "String",
            "optional": false,
            "field": "SERVER.WRONG_USER",
            "description": "<p>Cuando no se encontró la información del usuario.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Password"
  },
  {
    "type": "put",
    "url": "/password/reset",
    "title": "Para actualizar la contraseña.",
    "version": "0.0.1",
    "name": "PostReset",
    "group": "Password",
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>Email del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Nueva contraseña el usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password_confirmation",
            "description": "<p>Confirmación de la nueva contraseña el usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>Token para resetear la contraseña.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "grant_type",
            "description": "<p>Nombre de la fuente con la que se inicia sesión 'password'.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "Redirect",
            "optional": false,
            "field": "redirect",
            "description": "<p>Redirección a la página principal.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "View",
            "optional": false,
            "field": "auth.emails.password",
            "description": "<p>En caso de que falle el reseteo de la contraseña.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Password"
  },
  {
    "type": "get",
    "url": "/password/reset Habré una vista para",
    "title": "resetear la contraseña.",
    "version": "0.0.1",
    "name": "ShowResetForm",
    "group": "Password",
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>Token para resetear la contraseña.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>Email del usuario.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "view",
            "description": "<p>Vista con el formulario para resetear la contraseña.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Password"
  },
  {
    "type": "post",
    "url": "/pay",
    "title": "Para emitir pagos.",
    "version": "0.0.1",
    "name": "CreatePay",
    "group": "Pay",
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id",
            "description": "<p>Token de la tarjeta emitidito por mercado pago.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "installments",
            "description": "<p>Numero de plazos.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "issuer_id",
            "description": "<p>Id del banco emisor.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "payment_method_id",
            "description": "<p>Id del método de pago.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "202": [
          {
            "group": "202",
            "type": "MercadoPagoPayResponse",
            "optional": false,
            "field": "response",
            "description": "<p>Objeto de respuesta emitido por mercado pago.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "406": [
          {
            "group": "406",
            "type": "BadResponseException",
            "optional": false,
            "field": "error",
            "description": "<p>Un error obtenido del api del mercado pago en caso de una solicitud errónea.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Pay"
  },
  {
    "type": "get",
    "url": "/user/confirm/email",
    "title": "Para confirmar una cuenta de usuario.",
    "version": "0.0.1",
    "name": "ConfirmEmail",
    "group": "User",
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>Token para confirmar un email.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "Redirection",
            "optional": false,
            "field": "APP_REDIRECTS_LINK",
            "description": "<p>Redirección a la página principal.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "406": [
          {
            "group": "406",
            "type": "String",
            "optional": false,
            "field": "SERVER.TOKEN_EXPIRED",
            "description": "<p>En caso de que el token está expirado.</p>"
          },
          {
            "group": "406",
            "type": "String",
            "optional": false,
            "field": "SERVER.WRONG_TOKEN",
            "description": "<p>En caso de que lo token sea invalido.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "put",
    "url": "/user/social/link",
    "title": "Establecer el avatar del usuario propio.",
    "version": "0.0.1",
    "name": "CreateSocialLink",
    "group": "User",
    "permission": [
      {
        "name": "user"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "grant_type",
            "description": "<p>Es el nombre de la red social a vincular.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "extern_id",
            "description": "<p>Es la id del usuario correspondiente a el usuario en dicha red social.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "accessToken",
            "description": "<p>Token emitido por la red social.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "202": [
          {
            "group": "202",
            "type": "Object",
            "optional": false,
            "field": "socialLink",
            "description": "<p>Contenido del registro del nuevo vínculo con la red social.</p>"
          },
          {
            "group": "202",
            "type": "Number",
            "optional": false,
            "field": "socialLink.id",
            "description": "<p>Id del nuevo registro.</p>"
          },
          {
            "group": "202",
            "type": "Number",
            "optional": false,
            "field": "socialLink.user_id",
            "description": "<p>Id del usuario.</p>"
          },
          {
            "group": "202",
            "type": "Number",
            "optional": false,
            "field": "socialLink.extern_id",
            "description": "<p>Id del usuario en la red social correspondiente.</p>"
          },
          {
            "group": "202",
            "type": "String",
            "optional": false,
            "field": "socialLink.grant_type",
            "description": "<p>Fuente del vinculo con la red social.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "401": [
          {
            "group": "401",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_SOCIAL_ALREADY_USED",
            "description": "<p>Cuando ya está en uso ese vínculo por otro usuario.</p>"
          }
        ],
        "404": [
          {
            "group": "404",
            "type": "String",
            "optional": false,
            "field": "SERVER.WRONG_USER",
            "description": "<p>Cuando no se localiza el usuario.</p>"
          }
        ],
        "406": [
          {
            "group": "406",
            "type": "String",
            "optional": false,
            "field": "SERVER.WRONG_TOKEN",
            "description": "<p>Cuando el token de la red social es invalido.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "delete",
    "url": "/user/social/link/:id Eliminar un vínculo propio",
    "title": "con una red social.",
    "version": "0.0.1",
    "name": "DeleteSocialLink",
    "group": "User",
    "permission": [
      {
        "name": "user"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Id del registro del vínculo con la red social.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "204": [
          {
            "group": "204",
            "type": "Null",
            "optional": false,
            "field": "Null",
            "description": "<p>Cuando se logró eliminar un vínculo con una red social.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "404": [
          {
            "group": "404",
            "type": "String",
            "optional": false,
            "field": "SERVER.WRONG_SOCIAL_LINK_ID",
            "description": "<p>Cuando no se localizó el vínculo con una red social.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "delete",
    "url": "/user",
    "title": "Eliminar una cuenta de usuario propia.",
    "version": "0.0.1",
    "name": "DeleteUser",
    "group": "User",
    "permission": [
      {
        "name": "user"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "204": [
          {
            "group": "204",
            "type": "Null",
            "optional": false,
            "field": "Null",
            "description": "<p>Cuando se eliminó un usuario correctamente.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "404": [
          {
            "group": "404",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_NOT_FOUND",
            "description": "<p>Cuando no se encontró la cuenta de usuario.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "delete",
    "url": "/user",
    "title": "Eliminar una cuenta de usuario por su id.",
    "version": "0.0.1",
    "name": "DeleteUser",
    "group": "User",
    "permission": [
      {
        "name": "user"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Id del usuario a actualizar.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "204": [
          {
            "group": "204",
            "type": "Null",
            "optional": false,
            "field": "Null",
            "description": "<p>Cuando se eliminó un usuario correctamente.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "404": [
          {
            "group": "404",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_NOT_FOUND",
            "description": "<p>Cuando no se encontró la cuenta de usuario.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "/oauth/token",
    "title": "Solicita una sesión al servidor.",
    "version": "0.0.1",
    "name": "Login",
    "group": "User",
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "client_id",
            "description": "<p>Id del cliente con el que se desea acceder.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "client_secret",
            "description": "<p>Contraseña del cliente con el que se desea acceder.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "grant_type",
            "description": "<p>Nombre de la fuente con la que se inicia sesión 'facebook' | 'google' | 'password'.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>Email del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>Contraseña del usuario.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Id del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>Token con la sesión del usuario.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "404": [
          {
            "group": "404",
            "type": "String",
            "optional": false,
            "field": "SERVER.WRONG_USER",
            "description": "<p>Cuando no se encontró la información del usuario.</p>"
          },
          {
            "group": "404",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_NOT_REGISTRED",
            "description": "<p>Cuando el email no está registrado.</p>"
          }
        ],
        "406": [
          {
            "group": "406",
            "type": "String",
            "optional": false,
            "field": "SERVER.INCORRECT_USER",
            "description": "<p>Cuando el usuario o contraseña no están registrados.</p>"
          },
          {
            "group": "406",
            "type": "String",
            "optional": false,
            "field": "SERVER.WRONG_TOKEN",
            "description": "<p>Cuando el token enviado es incorrecto.</p>"
          }
        ]
      }
    },
    "filename": "./routes/oauth.php",
    "groupTitle": "User"
  },
  {
    "type": "delete",
    "url": "/user/sesion",
    "title": "Eliminar un token de autorización propio.",
    "version": "0.0.1",
    "name": "Logout",
    "group": "User",
    "permission": [
      {
        "name": "user"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "204": [
          {
            "group": "204",
            "type": "Null",
            "optional": false,
            "field": "Null",
            "description": "<p>Cuando se logró eliminar un token de autorización.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "/user/confirm/email Re enviar un correo para",
    "title": "confirmar la dirección de correo.",
    "version": "0.0.1",
    "name": "ReSendConfirmEmail",
    "group": "User",
    "permission": [
      {
        "name": "user"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_ALREADY_CONFIRMED",
            "description": "<p>En caso de que el email del usuario ya este confirmado.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "SERVER.EMAIL_SEND",
            "description": "<p>Email de confirmación enviado.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "400": [
          {
            "group": "400",
            "type": "String",
            "optional": false,
            "field": "SERVER.EMAIL_FAIL",
            "description": "<p>Cuando no se logra enviar le email.</p>"
          }
        ],
        "404": [
          {
            "group": "404",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_NOT_FOUND",
            "description": "<p>No se encontró el usuario a confirmar.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "put",
    "url": "/user/avatar",
    "title": "Establecer el avatar del usuario propio.",
    "version": "0.0.1",
    "name": "SetAvatar",
    "group": "User",
    "permission": [
      {
        "name": "user"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "File",
            "optional": false,
            "field": "file",
            "description": "<p>Es el archivo a almacenar.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "file_name",
            "description": "<p>Nombre del archivo.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "type",
            "description": "<p>Define el tipo de archivo, en caso de ser base64 se debe indicar.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "202": [
          {
            "group": "202",
            "type": "String",
            "optional": false,
            "field": "fileUrl",
            "description": "<p>Url donde se almaceno el archivo.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "/user",
    "title": "Resgistra un nuevo usuario.",
    "version": "0.0.1",
    "name": "Store",
    "group": "User",
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Nombre de usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "first_name",
            "description": "<p>Primer nombre.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "last_name",
            "description": "<p>Apellido.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>Email del nuevo usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "grant_type",
            "description": "<p>Fuente con la que se crea la cuenta 'facebook' | 'google' | 'password'.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "gender",
            "description": "<p>Genero del usuario 'male' | 'female' | 'other'.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "lang",
            "description": "<p>Idioma del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "Media",
            "optional": true,
            "field": "media",
            "description": "<p>Imagen del nuevo usuario a registrar {media: {url: String}.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "201": [
          {
            "group": "201",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Id del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>Token con la sesión del usuario.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "401": [
          {
            "group": "401",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_ALREADY_EXISTS",
            "description": "<p>Cuando el usuario ya existe.</p>"
          },
          {
            "group": "401",
            "type": "String",
            "optional": false,
            "field": "SERVER.UNAUTHORIZED",
            "description": "<p>Cuando el usuario no está autorizado.</p>"
          }
        ],
        "406": [
          {
            "group": "406",
            "type": "QueryException",
            "optional": false,
            "field": "error",
            "description": "<p>Error al ejecutar la consulta.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "put",
    "url": "/user",
    "title": "Actualizar la información del usuario propio.",
    "version": "0.0.1",
    "name": "UpdateUser",
    "group": "User",
    "permission": [
      {
        "name": "user"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "name",
            "description": "<p>Nombre de usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "first_name",
            "description": "<p>Primer nombre.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "last_name",
            "description": "<p>Apellido.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "gender",
            "description": "<p>Genero del usuario 'male' | 'female' | 'other'.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "phone",
            "description": "<p>Número de teléfono del usuario.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "201": [
          {
            "group": "201",
            "type": "Object",
            "optional": false,
            "field": "user",
            "description": "<p>Información del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": false,
            "field": "user.id",
            "description": "<p>Id del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.name",
            "description": "<p>Nombre de usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.first_name",
            "description": "<p>Primer nombre del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.last_name",
            "description": "<p>Apellido del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "user.gender",
            "description": "<p>Genero del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.email",
            "description": "<p>Email del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": true,
            "field": "user.media_id",
            "description": "<p>Id de la imagen del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "user.phone",
            "description": "<p>Telefono del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.lang",
            "description": "<p>Idioma del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "user.birthday",
            "description": "<p>Fecha de nacimiento del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Boolean",
            "optional": true,
            "field": "user.confirmed",
            "description": "<p>Si el correo del usuario está confirmado.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.grant_type",
            "description": "<p>Fuente desde la que se registro el usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.role",
            "description": "<p>Rol del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": true,
            "field": "user.address_id",
            "description": "<p>Id de la dirección del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.created_at",
            "description": "<p>Información del usuario.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "406": [
          {
            "group": "406",
            "type": "QueryException",
            "optional": false,
            "field": "error",
            "description": "<p>Error al ejecutar la consulta.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "put",
    "url": "/user/address",
    "title": "Actualizar la dirección del usuario propio.",
    "version": "0.0.1",
    "name": "UpdateUserAddress",
    "group": "User",
    "permission": [
      {
        "name": "user"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "country",
            "description": "<p>País del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "administrative_area_level_1",
            "description": "<p>Estado del ususario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "administrative_area_level_2",
            "description": "<p>Ciudad del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "route",
            "description": "<p>Calle del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "street_number",
            "description": "<p>Número del domicilio del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "postal_code",
            "description": "<p>Código postal del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "lat",
            "description": "<p>Latitud del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "lng",
            "description": "<p>Longitud del usuario.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "201": [
          {
            "group": "201",
            "type": "Object",
            "optional": true,
            "field": "address",
            "description": "<p>userDirección del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "address.id",
            "description": "<p>Información del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "address.contry",
            "description": "<p>País de la dirección del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "address.administrative_area_level_1",
            "description": "<p>Estado de la dirección del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "address.administrative_area_level_2",
            "description": "<p>Municipio de la dirección del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "address.route",
            "description": "<p>Calle de la dirección del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": true,
            "field": "address.street_number",
            "description": "<p>Numero de la dirección del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": true,
            "field": "address.postal_code",
            "description": "<p>Código postal de la dirección del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": true,
            "field": "address.lat",
            "description": "<p>Latitud de la dirección del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": true,
            "field": "address.lng",
            "description": "<p>Longitud postal de la dirección del usuario.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "406": [
          {
            "group": "406",
            "type": "QueryException",
            "optional": false,
            "field": "error",
            "description": "<p>Error al ejecutar la consulta.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "put",
    "url": "/user/email",
    "title": "Actualizar el email del usuario propio.",
    "version": "0.0.1",
    "name": "UpdateUserEmail",
    "group": "User",
    "permission": [
      {
        "name": "user"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>Nuevo email del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "grant_type",
            "description": "<p>Nombre de la fuente con la que se inicia sesión 'facebook' | 'google' | 'password'.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "201": [
          {
            "group": "201",
            "type": "Object",
            "optional": false,
            "field": "user",
            "description": "<p>Información del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": false,
            "field": "user.id",
            "description": "<p>Id del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.name",
            "description": "<p>Nombre de usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.first_name",
            "description": "<p>Primer nombre del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.last_name",
            "description": "<p>Apellido del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "user.gender",
            "description": "<p>Genero del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.email",
            "description": "<p>Email del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": true,
            "field": "user.media_id",
            "description": "<p>Id de la imagen del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "user.phone",
            "description": "<p>Telefono del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.lang",
            "description": "<p>Idioma del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": true,
            "field": "user.birthday",
            "description": "<p>Fecha de nacimiento del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Boolean",
            "optional": true,
            "field": "user.confirmed",
            "description": "<p>Si el correo del usuario está confirmado.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.grant_type",
            "description": "<p>Fuente desde la que se registro el usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.role",
            "description": "<p>Rol del usuario.</p>"
          },
          {
            "group": "201",
            "type": "Number",
            "optional": true,
            "field": "user.address_id",
            "description": "<p>Id de la dirección del usuario.</p>"
          },
          {
            "group": "201",
            "type": "String",
            "optional": false,
            "field": "user.created_at",
            "description": "<p>Información del usuario.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "406": [
          {
            "group": "406",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_EMAIL_ALREADY_EXISTS",
            "description": "<p>Cuando un usuario ya tiene un correo registrado.</p>"
          },
          {
            "group": "406",
            "type": "QueryException",
            "optional": false,
            "field": "error",
            "description": "<p>Error al ejecutar la consulta.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "put",
    "url": "/user/lang",
    "title": "Actualizar el idioma del usuario propio.",
    "version": "0.0.1",
    "name": "UpdateUserLang",
    "group": "User",
    "permission": [
      {
        "name": "user"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "lang",
            "description": "<p>Nuevo idioma del usuario.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "202": [
          {
            "group": "202",
            "type": "String",
            "optional": false,
            "field": "lang",
            "description": "<p>Regresa el idioma que se registro.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "404": [
          {
            "group": "404",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_NOT_REGISTRED",
            "description": "<p>Cuando el usuario no fue localizado.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "get",
    "url": "/user",
    "title": "Obtiene la información del usuario propio.",
    "version": "0.0.1",
    "name": "index",
    "group": "User",
    "permission": [
      {
        "name": "user"
      }
    ],
    "header": {
      "fields": {
        "Auth": [
          {
            "group": "Auth",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "description": "<p>Token de autorización.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "Object",
            "optional": false,
            "field": "user",
            "description": "<p>Información del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": false,
            "field": "user.id",
            "description": "<p>Id del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.name",
            "description": "<p>Nombre de usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.first_name",
            "description": "<p>Primer nombre del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.last_name",
            "description": "<p>Apellido del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.gender",
            "description": "<p>Genero del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.email",
            "description": "<p>Email del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.media_id",
            "description": "<p>Id de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Object",
            "optional": true,
            "field": "user.media",
            "description": "<p>Información de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.media.id",
            "description": "<p>Id de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.media.type",
            "description": "<p>Tipo de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.media.url",
            "description": "<p>Url de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.media.alt",
            "description": "<p>Alt de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.media.width",
            "description": "<p>Ancho de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.media.height",
            "description": "<p>Alto de la imagen del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.phone",
            "description": "<p>Telefono del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.lang",
            "description": "<p>Idioma del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.birthday",
            "description": "<p>Fecha de nacimiento del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Boolean",
            "optional": true,
            "field": "user.confirmed",
            "description": "<p>Si el correo del usuario está confirmado.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.grant_type",
            "description": "<p>Fuente desde la que se registro el usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.role",
            "description": "<p>Rol del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address_id",
            "description": "<p>Id de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Object",
            "optional": true,
            "field": "user.address",
            "description": "<p>Dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.id",
            "description": "<p>Información del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.contry",
            "description": "<p>País de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.administrative_area_level_1",
            "description": "<p>Estado de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.administrative_area_level_2",
            "description": "<p>Municipio de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": true,
            "field": "user.address.route",
            "description": "<p>Calle de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address.street_number",
            "description": "<p>Numero de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address.postal_code",
            "description": "<p>Código postal de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address.lat",
            "description": "<p>Latitud de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "Number",
            "optional": true,
            "field": "user.address.lng",
            "description": "<p>Longitud postal de la dirección del usuario.</p>"
          },
          {
            "group": "200",
            "type": "String",
            "optional": false,
            "field": "user.created_at",
            "description": "<p>Información del usuario.</p>"
          }
        ]
      }
    },
    "error": {
      "fields": {
        "406": [
          {
            "group": "406",
            "type": "String",
            "optional": false,
            "field": "SERVER.USER_NOT_FOUND",
            "description": "<p>En caso de que no se encuentre el usuario relacionado el token.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "/contact/send",
    "title": "Enviar un correo de contacto a administradores.",
    "version": "0.0.1",
    "name": "CreateSupport",
    "group": "Visitor",
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "name",
            "description": "<p>Nombre del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "phone",
            "description": "<p>Teléfono del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "email",
            "description": "<p>Email del usuario.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "lang",
            "description": "<p>Idioma del usuario.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "201": [
          {
            "group": "201",
            "type": "User",
            "optional": false,
            "field": "user",
            "description": "<p>Es la información del usuario que envió la solicitud.</p>"
          }
        ]
      }
    },
    "filename": "./routes/web.php",
    "groupTitle": "Visitor"
  }
] });
