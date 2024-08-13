<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
class My_JWT_Auth_User {

    // Genera un nuevo token para el usuario autenticado
    public static function generate_token( $request ) {
        $username = $request->get_param( 'username' );
        $password = $request->get_param( 'password' );

        if ( ! $username || ! $password ) {
            return new WP_Error( 'rest_invalid_param', 'Username and password are required', array( 'status' => 400 ) );
        }

        $user = wp_authenticate( $username, $password );

        if ( is_wp_error( $user ) ) {
            return new WP_Error( 'rest_invalid_credentials', 'Invalid username or password', array( 'status' => 401 ) );
        }

        $token = My_JWT_Auth_Token::create_token( $user->ID );

        return array( 'token' => $token );
    }

    // Verifica si el usuario tiene permisos para realizar la acción
    public static function check_permissions() {
        // La verificación del token se maneja en `validate_request`
        return true;
    }

    // Obtiene información del usuario basado en el token
    public static function get_user_info( $request ) {
        // Verifica permisos usando el token
        $permission_check = My_JWT_Auth_Token::validate_request( $request );
        if ( is_wp_error( $permission_check ) ) {
            return $permission_check;
        }

        $user_id = My_JWT_Auth_Token::validate_token( array( 'token' => str_replace('Bearer ', '', $request->get_header('Authorization')) ) )['user_id'];
        $user = get_user_by( 'ID', $user_id );

        return array(
            'ID' => $user->ID,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'display_name' => $user->display_name,
        );
    }

    // Actualiza la información del perfil del usuario
    public static function update_profile( $request ) {
        $permission_check = My_JWT_Auth_Token::validate_request( $request );
        if ( is_wp_error( $permission_check ) ) {
            return $permission_check;
        }

        $user_id = My_JWT_Auth_Token::validate_token( array( 'token' => str_replace('Bearer ', '', $request->get_header('Authorization')) ) )['user_id'];

        $email = $request->get_param( 'email' );
        $display_name = $request->get_param( 'display_name' );

        $update_data = array();
        if ( $email ) {
            $update_data['user_email'] = $email;
        }
        if ( $display_name ) {
            $update_data['display_name'] = $display_name;
        }

        if ( ! empty( $update_data ) ) {
            wp_update_user( array_merge( array( 'ID' => $user_id ), $update_data ) );
        }

        return array( 'status' => 'success' );
    }

    public static function create_user( $request ) {
        // Verifica permisos
        $permission_check = My_JWT_Auth_Token::validate_request( $request );
        if ( is_wp_error( $permission_check ) ) {
            return $permission_check;
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
    
}