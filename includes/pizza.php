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
        // Add new tab in woocommerce settings
        add_filter( 'woocommerce_settings_tabs_array', [$this, 'add_settings_tab'], 50 );
        add_action( 'woocommerce_settings_u_pizza', [$this, 'settings_page'] );
        add_action( 'woocommerce_update_options_u_pizza', [$this, 'update_woo_settings'] );
        
        // Add custon data filter
        //add_filter( 'u_pizza_default_data', [$this, 'modify_default_data'] );
        
        // Add admin assets
        add_action( 'admin_enqueue_scripts', [$this, 'admin_scripts'] );

        // Add new product type
        //add_filter('product_type_selector', [$this, 'add_product_types']);
        //add_action( 'admin_footer', [$this, 'display_prices_for_footer'] );

        //product admin page
        add_filter('product_type_options', [$this, 'woo_type_options']);
        add_filter('woocommerce_product_data_tabs',  [$this, 'woo_set_pizza_tabs']);
        add_action('woocommerce_product_data_panels', [$this, 'woo_add_product_pizza_fields']);
        add_action('woocommerce_process_product_meta',  [$this, 'woo_save_data'], 10, 2);

    }

    /**
     * Add new tab in wocoommerce settings.
     */
    public function add_settings_tab( $settings_tabs )
    {
        $settings_tabs['u_pizza'] = esc_html__( 'Pizza', 'u_pizza' );
        return $settings_tabs;
    }

    /**
     * Get the content of settings page.
     */
    public function settings_page()
    {
        require_once U_PIZZA_PATH . 'templates/admin/pizza-settings.php';
    }

    /**
     * Save the data of settings page.
     */
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

    /**
     * Add custon data filter
     */
    public function modify_default_data( $data )
    {
        $data[] = [
            'id'            => 3,
            'group_name'    => 'Group 3',
        ];

        return $data;
    }

    /**
     * Add admin settings files.
     */
    public function admin_scripts() 
    {
        global $post;
        wp_register_style( 'pizza-admin', plugins_url('assets/css/admin.min.css', U_PIZZA_DIR), [], time(), 'all' );

        if (isset( $_GET['tab']) && $_GET['tab'] === 'u_pizza' ) {

            wp_enqueue_media();
            wp_enqueue_style('pizza-admin');
            wp_enqueue_script('pizza-admin-settings', plugins_url('assets/js/adminPizzaSettings.js', U_PIZZA_DIR), ['jquery'], time(), true);
            wp_localize_script('pizza-admin-settings', 'U_PIZZA_DATA', [
                'url' => plugins_url( '/assets/', U_PIZZA_DIR ),
            ]);
        }
        if ( $post->post_type === 'product' ) {
            $pizza_data         = get_option( 'u_pizza_data' );
            $pizza_components   = array_merge( ...wp_list_pluck($pizza_data, 'components') );
            wp_enqueue_media();
            wp_enqueue_style('pizza-admin');
            wp_enqueue_script('pizza-admin-product', plugins_url('assets/js/adminPizzaProduct.js', U_PIZZA_DIR), ['jquery'], time(), true);
            wp_localize_script('pizza-admin-product', 'U_PRODUCT_DATA', [
                'url'               => plugins_url('/assets/', U_PIZZA_DIR),
                'pizza_components'  => $pizza_components,
                'wc_symbol'         => get_woocommerce_currency_symbol(),
                'price_position'    => get_option('woocommerce_currency_pos'),
                'decimals'          => wc_get_price_decimals(),
            ]);
        }
    }

    /**
     * Add new product type.
     */
    public function add_product_types( $types )
    {
        $types['u_pizza']           = esc_html('U Pizza Simple', 'u-pizza');
        $types['u_pizza_variable']  = esc_html('U Pizza Varible', 'u-pizza');
        return $types;
    }

    /**
     * Display price for a new product.
     */
    public function display_prices_for_footer()
    {
        global $post;
        if ( $post->post_type !== 'product' ) {
            return;
        }

        ?>
        <script>
            jQuery(document).ready(function() {
                jQuery('#general_product_data .pricing').addClass('show_if_u_pizza');
            })
        </script>
        <?php
    }

    /**
     * Add checkbox to pizza settings.
     */
    public function woo_type_options( $options )
    {
        $options['u_pizza'] = [
            'id'                =>  '_u_pizza',
            'wrapper_class'     =>  'show_if_simple show_if_variable',
            'label'             =>  esc_html__('Pizza', 'u-pizza'),
            'default'           =>  'no'
        ];

        return $options;
    }

    /**
     * Add space to pizza setting.
     */
    public function woo_set_pizza_tabs($tabs)
    {
        $tabs['u_pizza'] = [
            'label' => esc_html__('Pizza data', 'u-pizza'),
            'target' => 'u_pizza_product_data',
            'class' => 'show_if_u_pizza',
            'priority' => 75

        ];
        return $tabs;
    }

    /**
     * Get the content of pizza settings
     */
    public function woo_add_product_pizza_fields()
    {
        require_once U_PIZZA_PATH . 'templates/admin/pizza-product.php';
    }

    /**
     * Update data of pizza settings
     */
    public function woo_save_data($post_id, $post)
    {
        update_post_meta($post_id, '_u_pizza', isset($_POST['_u_pizza']) ? 'yes' : 'no');

        if ( isset( $_POST['_u_pizza'] ) ) {

            $pizza_data         = get_option('u_pizza_data');
            $pizza_components   = u_flatten_array(wp_list_pluck($pizza_data, 'components'));

            $base_components    = array_map(
                function ($component) {
                    if (array_key_exists($component['id'], $_POST['pizza_base'])) {
                        $component['price'] = $_POST['pizza_base'][$component['id']]['price'];
                        $component['weight'] = $_POST['pizza_base'][$component['id']]['weight'];
                        $component['required'] = isset($_POST['pizza_base'][$component['id']]['required']) ? 1 : 0;
                        $component['visible'] = isset($_POST['pizza_base'][$component['id']]['visible']) ? 1 : 0;
                    }
                    return $component;
                },
                array_filter($pizza_components, function ($component) {
                    return in_array($component['id'], $_POST['pizza_base_components']);
                })
            );
            $extra_components   = array_filter( $pizza_components, function( $componet ) {
                return in_array( $componet['id'], $_POST['pizza_extra_components'] );
            });
            $dish_components    = array_filter($pizza_components, function ($component) {
                return in_array($component['id'], $_POST['dish_components']);
            });
            $sides_components = array_map(
                function ($component) {
                    $component['price'] = $_POST['pizza_side'][$component['id']]['price'];
                    $component['weight'] = $_POST['pizza_side'][$component['id']]['weight'];
                    return $component;
                },
                u_pizza_sides()
            );
            $data = [
                'pizza' => [
                    'enabled'       => $_POST['pizza_type'] == 1 ? 1 : 0,
                    'base'          => $base_components, ///components
                    'extra'         => $extra_components, ///components
                    'sides'         => [
                        'enabled'       => isset($_POST['pizza_sides']) ? 1 : 0,
                        'components'    => $sides_components
                    ],
                    'floors'        => [
                        'enabled'       => isset($_POST['pizza_floors']) ? 1 : 0,
                        'components'    => wc_clean( $_POST['pizza_floor_products'] ) //ids
                    ],
                    'price_inc' => isset($_POST['price_inc']) ? 1 : 0,
                ],
                'dish' => [
                    'enabled'       => $_POST['pizza_type'] == 2 ? 1 : 0,
                    'components'    => $dish_components, //store components
                    'tabs'          => isset($_POST['dish_tabs']) ? 1 : 0,
                ]
            ];
            update_post_meta( $post_id, 'u_product_pizza_data', $data );
        }
    }
}