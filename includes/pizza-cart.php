<?php
class U_Pizza_Cart
{
    use Pizza_Instantiable;

    public function __construct()
    {
        add_filter('woocommerce_add_cart_item_data', [$this, 'add_item_data'], 10, 4);
        add_action('woocommerce_before_calculate_totals', [$this, 'calculate_totals'], 10, 1);
        add_filter('woocommerce_get_cart_item_from_session', [$this, 'get_cart_data_from_session'], 10, 3);

        //add_action('woocommerce_before_calculate_totals', [$this, 'debug_cart'], 20, 1);

    }

    /**
     * Add data to cart process.
     */
    public function add_item_data($cart_item_data, $product_id, $variation_id, $quantity)
    {
        if ( u_is_pizza_product( $product_id ) ) {

            $pizza_config          = [];
            $product_pizza_data     = get_post_meta( $product_id, 'u_product_pizza_data', true );

            // Extra components
            if ( (isset( $_POST['ev_quantity'] ) ) && ( ! empty( $_POST['ev_quantity'] ) ) ) {
                foreach ( wc_clean( $_POST['ev_quantity'] ) as $componet_id => $quantity ) {
                    if ( $quantity  == 0 ) continue;
                    foreach ( $product_pizza_data['pizza']['extra'] as $componet ) {
                        if ( (int) $componet['id'] === $componet_id ) {
                            $pizza_config['pizza']['extra'][] = [
                                'id'            => $componet['id'],
                                'name'          => $componet['name'],
                                'quantity'      => $quantity,
                                'price'         => $componet['price'],
                                'weight'        => $componet['weight'],
                                'description'   => $componet['description'],
                                'image'         => $componet['image'],
                            ];
                        }
                    }
                }
            }

            // Base component.
            if (isset($_POST['u-pizza-base'])) {
                $pizza_base = json_decode(wp_unslash(sanitize_text_field($_POST['u-pizza-base'])), true);
                if (!empty($pizza_base)) {

                    foreach ($pizza_base as $component_key => $component_val) {
                        foreach ($component_val as $component_id => $component_bool) {
                            foreach ($product_pizza_data['pizza']['base'] as $component) {
                                if ((int) $component['id'] === (int) $component_id && $component_bool) {
                                    $pizza_config['pizza']['base'][] = [
                                        'id'            => $component['id'],
                                        'name'          => $component['name'],
                                        'price'         => $component['price'],
                                        'description'   => $component['description'],
                                        'weight'        => $component['weight'],
                                        'image'         => $component['image']
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            // Floor components.
            if (isset($_POST['pizza-floors-data'])) {
                $pizza_floors = json_decode(wp_unslash(sanitize_text_field($_POST['pizza-floors-data'])), true);
                if (!empty($pizza_floors)) {
                    foreach ($pizza_floors as $product) {
                        $pizza_floor_product = wc_get_product($product['id']);
                        if ($pizza_floor_product) {
                            $pizza_config['pizza']['floors'][]  = [
                                'id'        =>  $pizza_floor_product->get_id(),
                                'name'      =>  $pizza_floor_product->get_name(),
                                'image_id'  =>  $pizza_floor_product->get_image_id(),
                                'price'     =>  U_Pizza_Product::get_product($pizza_floor_product)->get_price()
                            ];
                        }
                    }
                }
            }
            /*
            if (isset($_POST['pizza-sides-data'])) {
                $pizza_sides = json_decode(wp_unslash(sanitize_text_field($_POST['pizza-sides-data'])), true);
                if (!empty($pizza_sides)) {

                    foreach ($pizza_sides as $pizza_side) {

                        foreach ($product_pizza_data['pizza']['sides']['components'] as $component) {
                            if ((int) $component['id'] === (int) $pizza_side['id']) {
                                $pizza_config['pizza']['sides'][] = [
                                    'id' => $component['id'],
                                    'name' => $component['name'],
                                    'price' => $component['price'],
                                    'description' => $component['description'],
                                    'weight' => $component['weight'],
                                    'image' => $component['image']
                                ];
                            }
                        }
                    }
                }
            }
            */

            $cart_item_data['u_pizza_config'] = $pizza_config;

        }
        return $cart_item_data;
    }

    /**
     * 
     */
    public function calculate_totals($cart_object)
    {
        //$cart_object === WC()->cart
        foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
            $product_id = $cart_item['product_id'];
            if ( u_is_pizza_product( $product_id ) ) {
                $product_sid            =   $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
                $product_pizza_data     =   get_post_meta($product_id, 'u_product_pizza_data', true);
                $price                  =   U_Pizza_Product::get_product($product_sid)->get_price();

                // For extra components.
                if ( isset( $cart_item['u_pizza_config']['pizza']['extra'] ) ) {
                    $pizza_extra = $product_pizza_data['pizza']['extra'];
                    foreach ($cart_item['u_pizza_config']['pizza']['extra'] as $component) {
                        foreach ( $pizza_extra as $c ) {
                            if ( (int) $component['id'] === (int) $c['id'] ) {
                                $price +=  floatval( $c['price'] ) * intval( $component['quantity'] );
                            }
                        }
                    }
                }

                // For base components.
                if (isset($cart_item['u_pizza_config']['pizza']['base'])) {
                    if (U_Pizza_Product::get_product($product_id)->is_price_inc()) {
                        $selected_base  = $cart_item['u_pizza_config']['pizza']['base'];
                        $pizza_base     = $product_pizza_data['pizza']['base'];
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

                // For floor components
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
                $cart_item['data']->set_price( $price );
            }
        }
    }

    /**
     * 
     */
    public function get_cart_data_from_session($cart_item, $cart_session_item, $key)
    {
        if (isset($cart_session_item['u_pizza_config'])) {
            $cart_item['u_pizza_config'] = $cart_session_item['u_pizza_config'];
        }
        return $cart_item;
    }

    public function debug_cart()
    {
        $cart = WC()->cart->get_cart();
        echo "<pre>";
        print_r($cart);
        echo "</pre>";
    }

}
