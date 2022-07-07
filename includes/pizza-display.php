<?php

class U_Pizza_Display {

    // Instace.
    use Pizza_Instantiable;

    public function __construct() {
        add_action('woocommerce_before_add_to_cart_button', [$this, 'output_pizza_components']);

        add_action( 'wp_enqueue_scripts', [$this, 'enqueue'] );
    }

    public function output_pizza_components()
    {
        global $product;
        if ( ! u_is_pizza_product( $product->get_id() ) ) {
            return;
        } 
        $pizza_product_data = get_post_meta( $product->get_id(), 'u_product_pizza_data', true );
        if ( $pizza_product_data ) {
            wc_get_template( 'pizza/components.php', ['data' => $pizza_product_data ], '', U_PIZZA_PATH . 'templates/front/' );
        }
    }

    public function enqueue()
    {
        wp_enqueue_style( 'pizza-front', plugins_url( 'assets/css/main.min.css', U_PIZZA_DIR ), [], '1.0.0', 'all' );
    }

}