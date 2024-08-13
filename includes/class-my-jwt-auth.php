<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class My_JWT_Auth {

    public function init() {
        $this->includes(); // Incluye los archivos necesarios
        $this->init_hooks(); // Inicializa los hooks
    }

    private function includes() {
        // Incluye las clases necesarias para el plugin
        require_once plugin_dir_path( __FILE__ ) . 'class-my-jwt-auth-routes.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-my-jwt-auth-token.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-my-jwt-auth-user.php';
    }

    private function init_hooks() {
        // Registra las rutas de la API REST cuando el plugin se inicializa
        add_action( 'rest_api_init', array( 'My_JWT_Auth_Routes', 'register_routes' ) );
    }
}
