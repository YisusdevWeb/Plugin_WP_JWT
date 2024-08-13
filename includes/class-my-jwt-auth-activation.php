<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function my_jwt_auth_activate() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'jwt_tokens';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        token varchar(64) NOT NULL,
        user_id bigint(20) NOT NULL,
        expiry bigint(20) NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY token (token)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
    $result = dbDelta( $sql );

    if ( $result === false ) {
        error_log( 'Error creating JWT tokens table', 3, JWT_LOG_FILE );
    } else {
        error_log( 'JWT tokens table created or updated successfully', 3, JWT_LOG_FILE );
    }
}
