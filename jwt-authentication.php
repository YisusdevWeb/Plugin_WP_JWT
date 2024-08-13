<?php
/*
Plugin Name: JWT Authentication
Plugin URI: https://github.com/WordPress-API/WordPress-API
Description: JWT Authentication
Version: 0.1.0      
Author: Enlaweb- Yisus_Dev
Author URI: https://github.com/WordPress-API/WordPress-API
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: jwt-authentication
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! defined( 'JWT_PLUGIN_URL' ) ) {
    define( 'JWT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'JWT_LOG_FILE' ) ) {
    define( 'JWT_LOG_FILE', plugin_dir_path( __FILE__ ) . 'jwt-errors.log' );
}

// Cargar Composer autoload si existe
if ( file_exists( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
}

// Incluye la clase principal del plugin
require_once plugin_dir_path( __FILE__ ) . 'includes/class-my-jwt-auth.php';

// Incluye el archivo de activación
require_once plugin_dir_path( __FILE__ ) . 'includes/class-my-jwt-auth-activation.php';

// Inicializa el plugin
function my_jwt_auth_init() {
    $my_jwt_auth = new My_JWT_Auth();
    $my_jwt_auth->init();
}
add_action( 'plugins_loaded', 'my_jwt_auth_init' );

// Hook de activación
register_activation_hook( __FILE__, 'my_jwt_auth_activate' );
