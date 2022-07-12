<?php

class U_Pizza_Display {

    // Instace.
    use Pizza_Instantiable;

    public function __construct() {
        add_action( 'woocommerce_before_add_to_cart_button', [$this, 'output_pizza_components']);
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue'] );
        // Display pizza type info (popup) in order page.
        add_action('admin_enqueue_scripts', [$this, 'enqueue_order_admin']);

        add_filter( 'woocommerce_get_price_html', [$this, 'change_price'], 10, 2 );

        // update price in mini cart
        add_filter('woocommerce_cart_item_price', [$this, 'modify_price_mini_cart'], 10, 3);

        // Modify varible variation.
        add_filter('woocommerce_available_variation', [$this, 'modify_attr_array'], 10, 3);

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
     * Change the price displayed.
     */
    public function change_price($price, $product)
    {
        ///get parent id if variation product type
        $product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
        if (!u_is_pizza_product($product_id)) {
            return $price;
        }
        $product_pizza = U_Pizza_Product::get_product($product);

        ///simple prices
        if ($product->is_type('simple')) {
            if ($product_pizza->is_on_sale()) {
                $price = wc_format_sale_price(wc_get_price_to_display($product_pizza->get_wc_product(), ['price' => $product_pizza->get_regular_price()]), wc_get_price_to_display($product_pizza->get_wc_product(), ['price' => $product_pizza->get_price()])) . $product_pizza->get_price_suffix();
            } 
            else {
                $price = wc_price(wc_get_price_to_display($product_pizza->get_wc_product(), ['price' => $product_pizza->get_price()])) .  $product_pizza->get_price_suffix();
            }
        }

        ///variable prices
        elseif ($product->is_type('variable')) {
            $prices = $product->get_variation_prices();
            if (empty($prices['price'])) {
                $price = apply_filters('woocommerce_variable_empty_price_html', '', $product);
            } 
            else {
                $min_price     = $product_pizza->get_price(current($prices['price']));
                $max_price     = $product_pizza->get_price(end($prices['price']));
                $min_reg_price = $product_pizza->get_price(current($prices['regular_price']));
                $max_reg_price = $product_pizza->get_price(end($prices['regular_price']));

                if ($min_price !== $max_price) {
                    $price = wc_format_price_range($min_price, $max_price);
                } elseif ($product_pizza->is_on_sale() && $min_reg_price === $max_reg_price) {
                    $price = wc_format_sale_price(wc_price($max_reg_price), wc_price($min_price));
                } else {
                    $price = wc_price($min_price);
                }
                $price = apply_filters('woocommerce_variable_price_html', $price . $product_pizza->get_price_suffix(), $product);
            }
        } 
        
        // variations price.
        elseif ($product->is_type('variation')) {
            if ($product_pizza->is_on_sale()) {
                $price = wc_format_sale_price(wc_get_price_to_display($product_pizza->get_wc_product(), ['price' => $product_pizza->get_regular_price()]), wc_get_price_to_display($product_pizza->get_wc_product(), ['price' => $product_pizza->get_price()])) . $product_pizza->get_price_suffix();
            } 
            else {
                $price = wc_price(wc_get_price_to_display($product_pizza->get_wc_product(), ['price' => $product_pizza->get_price()])) .  $product_pizza->get_price_suffix();
            }
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

        // For sides components
        if (isset($cart_item['u_pizza_config']['pizza']['sides'])) {
            $pizza_sides = $product_pizza_data['pizza']['sides']['components'];
            foreach ($cart_item['u_pizza_config']['pizza']['sides'] as $component) {
                foreach ($pizza_sides as $side_component) {
                    if ((int) $component['id'] === (int) $side_component['id']) {
                        $price +=  floatval($side_component['price']);
                    }
                }
            }
        }

        // For dish section.
        if (isset($cart_item['u_pizza_config']['dish']['components'])) {

            $dish_add = $product_pizza_data['dish']['components'];
            foreach ($cart_item['u_pizza_config']['dish']['components'] as $component) {
                foreach ($dish_add as $add_component) {
                    if ((int) $component['id'] === (int) $add_component['id']) {
                        $price +=  floatval($add_component['price']) * intval($component['quantity']);
                    }
                }
            }
        }

        return wc_price($price);
    }

    /**
     * Assets for fronted.
     */
    public function enqueue()
    {
        // Libaries
        if (is_product() && u_pizza_tipps_enabled()) {
            wp_enqueue_script('pizza-popper', plugins_url('assets/js/popper.min.js', U_PIZZA_DIR), [], '2.11.0', true);
            wp_enqueue_script('pizza-tipps', plugins_url('assets/js/tippy.min.js', U_PIZZA_DIR), [], '6.3.7', true);
        }
        if (is_product()) {
            wp_enqueue_script('pizza-slimscroller', plugins_url('assets/js/jquery.slimscroll.min.js', U_PIZZA_DIR), ['jquery'], '1.3.8', true);
            wp_enqueue_style('pizza-slick', plugins_url('assets/css/slick.css', U_PIZZA_DIR), [], '1.8.1', 'all');
            wp_enqueue_script('pizza-slick', plugins_url('assets/js/slick.min.js', U_PIZZA_DIR), ['jquery'], '1.8.1', true);
        }

        wp_enqueue_style( 'pizza-fancybox', plugins_url( 'assets/css/jquery.fancybox.min.css', U_PIZZA_DIR ), [], '1.0.0', 'all' );
        wp_enqueue_style( 'pizza-front', plugins_url( 'assets/css/main.min.css', U_PIZZA_DIR ), [], '1.0.0', 'all' );
        wp_enqueue_script( 'pizza-fancybox', plugins_url( 'assets/js/jquery.fancybox.min.js', U_PIZZA_DIR ), ['jquery', 'wp-util'], time(), true );
        
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

        // Register for simple and variable product.
        wp_register_script( 'pizza-simple', plugins_url( 'assets/js/pizza-simple.js', U_PIZZA_DIR ), ['jquery', 'wp-util'], time(), true );
        wp_register_script( 'pizza-variable', plugins_url( 'assets/js/pizza-variable.js', U_PIZZA_DIR ), ['jquery', 'wp-util'], time(), true );
    }

    /**
     *  Pop up of pizza type.
     */
    public function enqueue_order_admin()
    {
        global $current_screen;
        if ($current_screen->id === 'shop_order') {
            wp_enqueue_style( 'pizza-fancybox', plugins_url( 'assets/css/jquery.fancybox.min.css', U_PIZZA_DIR ), [], '1.0.0', 'all' );
            wp_enqueue_style( 'pizza-front', plugins_url( 'assets/css/main.min.css', U_PIZZA_DIR ), [], '1.0.0', 'all' );
            wp_enqueue_script( 'pizza-fancybox', plugins_url( 'assets/js/jquery.fancybox.min.js', U_PIZZA_DIR ), ['jquery', 'wp-util'], time(), true );
            wp_add_inline_script('pizza-fancybox', '
                jQuery(document.body).on("click", ".pizza-composition-toggle", function () {
                    jQuery.fancybox.open({
                        src: `#u-pizza-${jQuery(this).attr("data-product-id")}`,
                        type: "inline",
                        touch: false,
                    });
                });
            ');
        }
    } 

    /**
     * Set prices for variation product types in form.cart attributes
     */
    public function modify_attr_array($data, $product_variable, $variation)
    {
        if (!u_is_pizza_product($product_variable->get_id())) {
            return $data;
        }
        $product_pizza = U_Pizza_Product::get_product($product_variable);
        $data['display_price']          =   wc_get_price_to_display($variation, ['price' => $product_pizza->get_price($variation->get_price())]);
        $data['display_regular_price']  =   wc_get_price_to_display($variation, ['price' => $product_pizza->get_price($variation->get_regular_price())]);
        return $data;
    }
}