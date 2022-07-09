<?php

class U_Pizza_Display {

    // Instace.
    use Pizza_Instantiable;

    public function __construct() {
        add_action( 'woocommerce_before_add_to_cart_button', [$this, 'output_pizza_components']);
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue'] );
        add_filter( 'woocommerce_get_price_html', [$this, 'change_price'], 10, 2 );

        // update price in mini cart
        add_filter('woocommerce_cart_item_price', [$this, 'modify_price_mini_cart'], 10, 3);

    }

    /**
     * 
     */
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

    /**
     * 
     */
    public function enqueue()
    {
        wp_enqueue_style( 'pizza-fansybox', plugins_url( 'assets/css/jquery.fancybox.min.css', U_PIZZA_DIR ), [], '1.0.0', 'all' );
        wp_enqueue_style( 'pizza-front', plugins_url( 'assets/css/main.min.css', U_PIZZA_DIR ), [], '1.0.0', 'all' );

        wp_enqueue_script( 'pizza-fansybox', plugins_url( 'assets/js/jquery.fancybox.min.js', U_PIZZA_DIR ), ['jquery', 'wp-util'], time(), true );
        wp_enqueue_script( 'pizza-front', plugins_url( 'assets/js/pizza-front.js', U_PIZZA_DIR ), ['jquery', 'wp-util'], time(), true );
        wp_localize_script('pizza-front', 'PIZZA_FRONT_DATA', [
            'url'                   =>  plugins_url('/assets/', U_PIZZA_DIR),
            'floor_default_text'    =>  apply_filters('u_pizza_template_empty_floor_text', __('Choose %s flour pizza', 'u-pizza')),
            'floor_default_image'   =>  u_pizza_get_image_placeholder('empty_floor'),
            'side_default_text'     =>  apply_filters('u_pizza_empty_side_text', __('Choose cheese', 'u-pizza')),
            'side_default_image'    =>  u_pizza_get_image_placeholder('empty_side'),
            'wc_symbol'             =>  get_woocommerce_currency_symbol(),
            'price_position'        =>  get_option('woocommerce_currency_pos'),
            'decimals'              =>  wc_get_price_decimals(),
        ]);
        wp_register_script( 'pizza-simple', plugins_url( 'assets/js/pizza-simple.js', U_PIZZA_DIR ), ['jquery', 'wp-util'], time(), true );
    }

    /**
     * 
     */
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
            $price = wc_format_sale_price(
                        wc_get_price_to_display(
                            $product_pizza->get_wc_product(), ['price' => $product_pizza->get_regular_price()]
                        ), 
                        wc_get_price_to_display(
                            $product_pizza->get_wc_product(), ['price' => $product_pizza->get_price()]
                        ),
                    ) . $product_pizza->get_price_suffix();
        } 
        else {
            $price = wc_price( wc_get_price_to_display( $product_pizza->get_wc_product(), ['price' => $product_pizza->get_price()]) ) .  $product_pizza->get_price_suffix();
        }
        return $price;
    }

    /**
     * Modify cart product price in mini cart and on cart page.
     */
    public function modify_price_mini_cart($price, $cart_item, $cart_item_key)
    {
        $product_id = $cart_item['product_id'];
        if (!u_is_pizza_product($product_id)) {
            return $price;
        }
        $product_sid = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];

        $product_pizza_data = get_post_meta($product_id, 'u_product_pizza_data', true);
        $price = U_Pizza_Product::get_product($product_sid)->get_price();

        // Extra components
        if (isset($cart_item['u_pizza_config']['pizza']['extra'])) {
            $pizza_extra = $product_pizza_data['pizza']['extra'];
            foreach ($cart_item['u_pizza_config']['pizza']['extra'] as $component) {
                foreach ($pizza_extra as $c) {
                    if ((int) $component['id'] === (int) $c['id']) {
                        $price += floatval($c['price']) * intval($component['quantity']);
                    }
                }
            }
        }
        
        // Base components.
        if (isset($cart_item['u_pizza_config']['pizza']['base'])) {
            if (U_Pizza_Product::get_product($product_id)->is_price_inc()) {
                $selected_base = $cart_item['u_pizza_config']['pizza']['base'];
                $pizza_base = $product_pizza_data['pizza']['base'];

                if (!empty($pizza_base)) {
                    foreach ($pizza_base as $component) {

                        $found = false;
                        foreach ($selected_base as $selected_component) {
                            if ((int) $component['id'] === (int) $selected_component['id']) {
                                $found = true;
                            }
                        }
                        if (!$found) {
                            $price -= floatval($component['price']);
                        }
                    }
                }
            }
        }

        // Floors components.
        if ( isset( $cart_item['u_pizza_config']['pizza']['floors'] ) ) {
            $pizza_floors = $product_pizza_data['pizza']['floors']['components'];
            foreach ($cart_item['u_pizza_config']['pizza']['floors'] as $component) {
                foreach ($pizza_floors as $floor_id) {
                    if ((int) $component['id'] === (int) $floor_id) {
                        //skip main product (first floor)
                        if ((int) $product_id === (int) $floor_id) continue;
                        $price +=  floatval($component['price']);
                    }
                }
            }
        }

        return wc_price($price);
    }

}