<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Asegúrate de definir JWT_LOG_FILE en otro lugar en tu plugin
if ( ! defined( 'JWT_LOG_FILE' ) ) {
    define( 'JWT_LOG_FILE', plugin_dir_path( __FILE__ ) . 'jwt-errors.log' );
}

class My_JWT_Auth_Token {

    public static function create_token( $user_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'jwt_tokens';

        // Verifica si la tabla existe
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
            error_log( 'Token table does not exist', 3, JWT_LOG_FILE );
            return new WP_Error( 'rest_error', 'Token table does not exist', array( 'status' => 500 ) );
        }

        $token = bin2hex(random_bytes(32)); // Genera un token aleatorio de 64 caracteres hexadecimales
        $expiry = time() + ( HOUR_IN_SECONDS * 24 ); // Token válido por 24 horas

        // Inserta el token en la base de datos
        $inserted = $wpdb->insert(
            $table_name,
            array(
                'token' => $token,
                'user_id' => $user_id,
                'expiry' => $expiry
            )
        );

        // Registra errores si la inserción falla
        if ( false === $inserted ) {
            error_log( 'Failed to insert token into database', 3, JWT_LOG_FILE );
        } else {
            error_log( "Token created: $token for user_id: $user_id", 3, JWT_LOG_FILE );
        }

        return $token;
    }

    public static function validate_token( $data ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'jwt_tokens';
    
        // Verifica si la tabla existe
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
            error_log( 'Token table does not exist', 3, JWT_LOG_FILE );
            return new WP_Error( 'rest_error', 'Token table does not exist', array( 'status' => 500 ) );
        }
    
        $token = isset($data['token']) ? $data['token'] : '';
    
        // Verifica que el token esté presente
        if ( empty( $token ) ) {
            return new WP_Error( 'rest_error', 'Token is required', array( 'status' => 400 ) );
        }
    
        // Registra el token que se está validando
        error_log( "Validating token: $token", 3, JWT_LOG_FILE );
    
        // Busca el token en la base de datos
        $result = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table_name WHERE token = %s AND expiry > %d",
            $token,
            time()
        ) );
    
        // Registra el resultado de la validación
        if ( $result ) {
            error_log( "Token valid: $token", 3, JWT_LOG_FILE );
            return array( 'valid' => true, 'user_id' => $result->user_id );
        } else {
            error_log( "Token invalid or expired: $token", 3, JWT_LOG_FILE );
            return array( 'valid' => false, 'message' => 'Invalid or expired token' );
        }
    }
    public static function revoke_token( $data ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'jwt_tokens';

        // Verifica si la tabla existe
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) !== $table_name ) {
            error_log( 'Token table does not exist', 3, JWT_LOG_FILE );
            return new WP_Error( 'rest_error', 'Token table does not exist', array( 'status' => 500 ) );
        }

        $token = isset($data['token']) ? $data['token'] : '';

        // Verifica que el token esté presente
        if ( empty( $token ) ) {
            return new WP_Error( 'rest_error', 'Token is required', array( 'status' => 400 ) );
        }

        // Elimina el token de la base de datos
        $wpdb->delete(
            $table_name,
            array( 'token' => $token )
        );

        return array( 'status' => 'success' );
    }



    /**
     * Crea un nuevo usuario.
     *
     * @param WP_REST_Request $request La solicitud REST.
     * @return array|WP_Error Resultado de la creación del usuario.
     */
    public static function create_user( $request ) {
        // Verifica permisos
        if ( ! current_user_can( 'manage_options' ) ) { // Solo administradores pueden crear usuarios
            return new WP_Error( 'rest_forbidden', 'You do not have permission to create users', array( 'status' => 403 ) );
        }
    
        $username = $request->get_param( 'username' );
        $password = $request->get_param( 'password' );
        $email = $request->get_param( 'email' );
        $display_name = $request->get_param( 'display_name' );
    
        // Verifica que los datos estén presentes
        if ( ! $username || ! $password || ! $email ) {
            return new WP_Error( 'rest_invalid_param', 'Username, password, and email are required', array( 'status' => 400 ) );
        }
    
        // Verifica si el nombre de usuario ya existe
        if ( username_exists( $username ) ) {
            return new WP_Error( 'rest_user_exists', 'Username already exists', array( 'status' => 400 ) );
        }
    
        // Verifica si el correo electrónico ya está registrado
        if ( email_exists( $email ) ) {
            return new WP_Error( 'rest_email_exists', 'Email already exists', array( 'status' => 400 ) );
        }
    
        // Prepara los datos para el nuevo usuario
        $user_data = array(
            'user_login' => $username,
            'user_pass'  => $password,
            'user_email' => $email,
            'display_name' => $display_name,
            'role'       => 'subscriber' // Puedes ajustar el rol según sea necesario
        );
    
        // Crea el nuevo usuario
        $user_id = wp_insert_user( $user_data );
    
        // Verifica si hubo un error al crear el usuario
        if ( is_wp_error( $user_id ) ) {
            return $user_id; // Devuelve el error
        }
    
        return array( 'status' => 'success', 'user_id' => $user_id );
    }

    public static function validate_request( $request ) {
        $token = $request->get_header('Authorization');

        // Verifica que el token esté presente en el encabezado
        if ( ! $token ) {
            return new WP_Error( 'rest_forbidden', 'No token provided', array( 'status' => 403 ) );
        }

        $token = str_replace('Bearer ', '', $token); // Elimina 'Bearer ' del encabezado

        // Valida el token
        $validation = self::validate_token( array( 'token' => $token ) );

        // Verifica la validez del token
        if ( ! $validation['valid'] ) {
            return new WP_Error( 'rest_forbidden', 'Invalid token', array( 'status' => 403 ) );
        }

        return true;
    }
}
