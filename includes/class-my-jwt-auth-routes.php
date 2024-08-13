<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class My_JWT_Auth_Routes {

    public static function register_routes() {
        // Ruta para generar el token
        register_rest_route( 'my-jwt-auth/v1', '/token', array(
            'methods' => 'POST',
            'callback' => array( 'My_JWT_Auth_User', 'generate_token' ),
            'args' => array(
                'username' => array(
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'password' => array(
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));

        // Ruta para validar el token
        register_rest_route( 'my-jwt-auth/v1', '/validate-token', array(
            'methods' => 'POST',
            'callback' => array( 'My_JWT_Auth_Token', 'validate_token' ),
            'args' => array(
                'token' => array(
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));

        // Ruta para obtener información del usuario
        register_rest_route( 'my-jwt-auth/v1', '/user-info', array(
            'methods' => 'GET',
            'callback' => array( 'My_JWT_Auth_User', 'get_user_info' ),
            'permission_callback' => array( 'My_JWT_Auth_Token', 'validate_request' ),
        ));

        // Ruta para revocar el token
        register_rest_route( 'my-jwt-auth/v1', '/revoke-token', array(
            'methods' => 'POST',
            'callback' => array( 'My_JWT_Auth_Token', 'revoke_token' ),
            'permission_callback' => array( 'My_JWT_Auth_Token', 'validate_request' ),
            'args' => array(
                'token' => array(
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));

        // Ruta para actualizar el perfil del usuario
        register_rest_route( 'my-jwt-auth/v1', '/update-profile', array(
            'methods' => 'POST',
            'callback' => array( 'My_JWT_Auth_User', 'update_profile' ),
            'permission_callback' => array( 'My_JWT_Auth_Token', 'validate_request' ),
            'args' => array(
                'email' => array(
                    'required' => false,
                    'sanitize_callback' => 'sanitize_email',
                ),
                'display_name' => array(
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));

         // Ruta para crear un nuevo usuario
         register_rest_route('my-jwt-auth/v1', '/create-user', array(
            'methods' => 'POST',
            'callback' => array('My_JWT_Auth_User', 'create_user'),
            'permission_callback' => array('My_JWT_Auth_Token', 'validate_request'),
            'args' => array(
                'username' => array(
                    'required' => true,
                    'sanitize_callback' => 'sanitize_user',
                ),
                'password' => array(
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'email' => array(
                    'required' => true,
                    'sanitize_callback' => 'sanitize_email',
                ),
                'display_name' => array(
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));
        
    }
}
