# Plugin WordPress JWT Authentication

Este plugin proporciona un sistema de autenticación JWT (JSON Web Token) para WordPress. Permite la generación, validación y revocación de tokens JWT para la autenticación de usuarios en el sitio de WordPress.

## Características

- **Generación de Tokens:** Autentica a los usuarios mediante nombre de usuario y contraseña, generando un token JWT.
- **Validación de Tokens:** Valida los tokens JWT para asegurar la autenticación en las solicitudes.
- **Revocación de Tokens:** Permite revocar un token JWT específico.
- **Actualización del Perfil:** Permite a los usuarios autenticados actualizar su perfil.
- **Creación de Usuarios:** Crea nuevos usuarios en WordPress mediante un endpoint REST.

## Requisitos

- **WordPress:** Debes tener instalado WordPress 5.0 o superior.
- **PHP:** Este plugin requiere PHP 7.4 o superior.
- **Base de Datos:** Es necesario tener base de datos para almacenar los tokens JWT.

## Instalación

1. **Clona el Repositorio:**

    ```bash
    git clone https://github.com/YisusdevWeb/Plugin_WP_JWT.git
    ```

2. **Accede al Directorio del Plugin:**

    ```bash
    cd Plugin_WP_JWT
    ```

3. **Instala las Dependencias:**

   Este proyecto utiliza Composer para manejar las dependencias. Si aún no lo tienes instalado, sigue [estas instrucciones](https://getcomposer.org/doc/00-intro.md) para instalar Composer.

    ```bash
    composer install
    ```

4. **Activa el Plugin:**

   Sube el plugin a tu carpeta `wp-content/plugins/` y actívalo desde el panel de administración de WordPress.

## Endpoints REST API

El plugin añade los siguientes endpoints a la API REST de WordPress:

- **Generar Token:** `POST /wp-json/my-jwt-auth/v1/token`
    - **Parámetros:** `username`, `password`
    - **Respuesta:** `token`
  
- **Validar Token:** `POST /wp-json/my-jwt-auth/v1/validate-token`
    - **Parámetros:** `token`
    - **Respuesta:** `valid`, `user_id`
  
- **Revocar Token:** `POST /wp-json/my-jwt-auth/v1/revoke-token`
    - **Parámetros:** `token`
    - **Respuesta:**
- **Obtener Información del Usuario:** `GET /wp-json/my-jwt-auth/v1/user-info`
    - **Encabezado:** `Authorization: Bearer <token>`
    - **Respuesta:** `ID`, `username`, `email`, `display_name`

- **Actualizar Perfil:** `POST /wp-json/my-jwt-auth/v1/update-profile`
    - **Encabezado:** `Authorization: Bearer <token>`
    - **Parámetros Opcionales:** `email`, `display_name`
    - **Respuesta:** `status`
  
- **Crear Usuario:** `POST /wp-json/my-jwt-auth/v1/create-user`
    - **Parámetros:** `username`, `password`, `email`
    - **Opcional:** `display_name`
    - **Respuesta:** `status`, `user_id`

## Cómo Usar el Plugin

### 1. Generar un Token

Envía una solicitud `POST` al endpoint `/wp-json/my-jwt-auth/v1/token` con los parámetros `username` y `password`. Recibirás un token JWT en la respuesta.

### 2. Validar un Token

Envía una solicitud `POST` al endpoint `/wp-json/my-jwt-auth/v1/validate-token` con el token que deseas validar.

### 3. Revocar un Token

Envía una solicitud `POST` al endpoint `/wp-json/my-jwt-auth/v1/revoke-token` con el token que deseas revocar.

### 4. Obtener Información del Usuario

Envía una solicitud `GET` al endpoint `/wp-json/my-jwt-auth/v1/user-info`, incluyendo el token en el encabezado de la solicitud como `Authorization: Bearer <token>`.

### 5. Actualizar el Perfil del Usuario

Envía una solicitud `POST` al endpoint `/wp-json/my-jwt-auth/v1/update-profile` con el token en el encabezado y los parámetros `email` y/o `display_name` que deseas actualizar.

### 6. Crear un Nuevo Usuario

Envía una solicitud `POST` al endpoint `/wp-json/my-jwt-auth/v1/create-user` con los parámetros `username`, `password`, y `email`. Opcionalmente, puedes incluir `display_name`.

## Exclusiones del Proyecto

El plugin incluye un archivo `.gitignore` que excluye la carpeta `vendor` y otros archivos no necesarios para el control de versiones.

## Contribuir

Si deseas contribuir a este proyecto, por favor, sigue los siguientes pasos:

1. **Haz un fork** del repositorio.
2. **Clona** tu fork localmente.
3. Crea una **rama** para tu contribución (`git checkout -b feature/nueva-caracteristica`).
4. **Haz commits** de tus cambios (`git commit -am 'Agrega nueva característica'`).
5. **Haz push** de la rama (`git push origin feature/nueva-caracteristica`).
6. Abre un **Pull Request** en GitHub.

## Licencia

Este proyecto está licenciado bajo la [MIT License](LICENSE).

