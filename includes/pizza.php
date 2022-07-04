<?php

class U_Pizza {

    protected static $instance = null;

    public static function instance() 
    {

        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;

    }

    public function __construct() 
    {
        add_filter( 'woocommerce_settings_tabs_array', [$this, 'add_settings_tab'], 50 );
        add_action( 'woocommerce_settings_u_pizza', [$this, 'settings_page'] );
        add_action( 'woocommerce_update_options_u_pizza', [$this, 'update_woo_settings'] );
    }

    public function add_settings_tab( $settings_tabs )
    {
        $settings_tabs['u_pizza'] = esc_html__( 'Pizza', 'u_pizza' );
        return $settings_tabs;
    }

    public function settings_page()
    {
        require_once U_PIZZA_PATH . 'templates/admin/pizza-settings.php';
    }

    public function update_woo_settings()
    {
        if  (   ( empty( $_POST['_pizzanonce'] ) ) ||
                ( ! wp_verify_nonce( $_POST['_pizzanonce'], 'u_pizza_woo_settings' ) )
            ) 
        {
            return;
        }

        update_option( 'u_pizza_data',  $_POST['pizza_data'] );
    }

    public function modify_default_data( $data )
    {
        $data[] = [
            'id'            => 3,
            'group_name'    => 'Group 3',
        ];

        return $data;
    }

}