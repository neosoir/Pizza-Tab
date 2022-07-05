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
        //add_filter( 'u_pizza_default_data', [$this, 'modify_default_data'] );
        add_action( 'admin_enqueue_scripts', [$this, 'admin_scripts'] );
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

        foreach (  $_POST['pizza_data'] as &$group ) {
            foreach ( $group['components'] as &$componet ) {
                if ( isset( $componet['meta'] ) ) {
                    $componet['meta'] = 1;
                }
                else {
                    $componet['meta'] = 0;
                }
                $componet['required']    = 0;
                $componet['visible']     = 1;
            }
        }
        update_option( 'u_pizza_data',  wc_clean( $_POST['pizza_data'] ) );
        
    }

    public function modify_default_data( $data )
    {
        $data[] = [
            'id'            => 3,
            'group_name'    => 'Group 3',
        ];

        return $data;
    }

    public function admin_scripts() 
    {

        if (isset($_GET['tab']) && $_GET['tab'] === 'u_pizza') {

            wp_enqueue_media();
            wp_enqueue_style( 'pizza-admin', plugins_url('assets/css/admin.min.css', U_PIZZA_DIR), [], time(), 'all' );
            wp_enqueue_script( 'pizza-admin-settings', plugins_url('assets/js/adminPizzaSettings.js', U_PIZZA_DIR), ['jquery'], time(), true );
            wp_localize_script('pizza-admin-settings', 'U_PIZZA_DATA', [
                'url' => plugins_url('/assets/', U_PIZZA_DIR),
            ]);
        }
    }

}