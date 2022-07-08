<?php

class U_Pizza_Display {

    // Instace.
    use Pizza_Instantiable;

    public function __construct() {
        add_action( 'woocommerce_before_add_to_cart_button', [$this, 'output_pizza_components']);
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue'] );
        add_filter( 'woocommerce_get_price_html', [$this, 'change_price'], 10, 2 );
    }

    public function output_pizza_components()
    {
        global $product;
        if ( ! u_is_pizza_product( $product->get_id() ) ) {
            return;
        } 
        $pizza_product_data = get_post_meta( $product->get_id(), 'u_product_pizza_data', true );
        if ( $pizza_product_data ) {
            wc_get_template( 'pizza/components.php', [ 'data' => $pizza_product_data, 'product' => $product ], '', U_PIZZA_PATH . 'templates/front/' );
        }
    }

    public function enqueue()
    {
        wp_enqueue_style( 'pizza-front', plugins_url( 'assets/css/main.min.css', U_PIZZA_DIR ), [], '1.0.0', 'all' );
        wp_enqueue_script( 'pizza-front', plugins_url( 'assets/js/pizza-front.js', U_PIZZA_DIR ), ['jquery', 'wp-util'], time(), true );
        wp_localize_script('pizza-front', 'PIZZA_FRONT_DATA', [
            'url'               => plugins_url('/assets/', U_PIZZA_DIR),
            'wc_symbol'         => get_woocommerce_currency_symbol(),
            'price_position'    => get_option('woocommerce_currency_pos'),
            'decimals'          => wc_get_price_decimals(),
        ]);
        wp_register_script( 'pizza-simple', plugins_url( 'assets/js/pizza-simple.js', U_PIZZA_DIR ), ['jquery', 'wp-util'], time(), true );
    }

    public function change_price($price, $product)
    {
        ///get parent id if variation product type
        $product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
        if ( ! u_is_pizza_product( $product_id ) ) {
            return $price;
        }
        $product_pizza  = U_Pizza_Product::get_product($product);
        $price          = $product_pizza->get_price();
        if ($product_pizza->is_on_sale()) {
            $price = wc_format_sale_price(wc_get_price_to_display($product_pizza->get_wc_product(), ['price' => $product_pizza->get_regular_price()]), wc_get_price_to_display($product_pizza->get_wc_product(), ['price' => $product_pizza->get_price()])) . $product_pizza->get_price_suffix();
        } 
        else {
            $price = wc_price( wc_get_price_to_display( $product_pizza->get_wc_product(), ['price' => $product_pizza->get_price()]) ) .  $product_pizza->get_price_suffix();
        }


        return $price;
    }

}