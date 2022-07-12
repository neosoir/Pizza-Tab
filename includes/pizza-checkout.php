<?php
class U_Pizza_Checkout
{
    use Pizza_Instantiable;
    public function __construct()
    {
        //add meta to order
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'add_meta_to_order'], 10, 4);
        add_filter('woocommerce_checkout_cart_item_quantity', [$this, 'display_pizza_type_meta'], 10, 2);

        //dispaly order item meta in Thank you page
        add_filter('woocommerce_order_item_display_meta_key',  [$this, 'display_meta_key'], 10, 2);
        add_filter('woocommerce_order_item_display_meta_value', [$this, 'display_meta_value'], 10, 2);
        add_action('woocommerce_order_item_meta_end',   [$this, 'display_meta_thankyou_main'], 10, 3);
        add_action('woocommerce_order_item_meta_end',   [$this, 'display_meta_thankyou_extra'], 10, 3);

        //display order item meta in admin Orders
        add_action('woocommerce_after_order_itemmeta', [$this, 'display_meta_thankyou_main'], 10, 3);
        add_action('woocommerce_after_order_itemmeta', [$this, 'display_meta_thankyou_extra'], 10, 3);

        //debug order meta
        //add_action('woocommerce_before_thankyou', [$this, 'debug_order'], 10, 1);
    }

    /**
     * Replace '_u_pizza_config' from Dish type
     */
    public function display_meta_key($display_key, $meta)
    {

        if ($display_key === '_u_pizza_config') {

            return apply_filters('u_pizza_checkout_meta_key', esc_html__('Pizza components', 'u-pizza'), $meta);
        }
        return $display_key;
    }

    /**
     * Modify display_value for display_meta_thankyou_extra()
     */
    public function display_meta_value($value, $meta)
    {
        if ($meta->key === '_u_pizza_config') {
            if (!isset($meta->value['dish']['components'])) {
                return;
            }
            $output = '';
            foreach ($meta->value['dish']['components'] as $component) {


                $output .= $component['weight'] !== '' ? '<p>' . $component['name'] . ' ' .  $component['weight'] . '/' .  wc_price($component['price']) . ' x' . $component['quantity'] . '</p>' :  '<p>' . $component['name'] . ' ' .  wc_price($component['price']) . ' x' . $component['quantity'] . '</p>';
            }
            return $output;
        }
        return $value;
    }

    public function add_meta_to_order($order_item, $cart_item_key, $cart_item_data, $order)
    {
        if (isset($cart_item_data['u_pizza_config'])) {
            $order_item->update_meta_data('_u_pizza_config', wc_clean($cart_item_data['u_pizza_config']));
        }
    }

    /**
     * Display Dish type info.
     */
    public function display_meta_thankyou_extra($item_id, $item, $order)
    {
        if (!u_is_pizza_product($item->get_data()['product_id'])) {
            return;
        }

        $item_data = $item->get_meta_data();


        $formatted_meta = [];
        foreach ($item_data as $meta) {

            if ($meta->key !== '_u_pizza_config') {
                continue;
            }
            if (!isset($meta->value['dish']['components'])) {
                continue;
            }

            $formatted_meta[] = [
                'key'           => $meta->key,
                'value'         => $meta->value,
                'display_key'   => apply_filters('woocommerce_order_item_display_meta_key', $meta->key, $meta),
                'display_value' => wpautop(make_clickable(apply_filters('woocommerce_order_item_display_meta_value', $meta->value, $meta))),
            ];
        }

        $strings = array();
        $html    = '';
        $args    = wp_parse_args(
            [],
            array(
                'before'       => '<ul class="wc-item-meta"><li>',
                'after'        => '</li></ul>',
                'separator'    => '</li><li>',
                'echo'         => true,
                'autop'        => false,
                'label_before' => '<strong class="wc-item-meta-label">',
                'label_after'  => ':</strong> ',
            )
        );

        foreach ($formatted_meta as $meta_id => $meta) {
            $value     = $args['autop'] ? wp_kses_post($meta['display_value']) : wp_kses_post(make_clickable(trim($meta['display_value'])));
            if ($meta['key'] === '_u_pizza_config') {

                $strings[] = '<strong class="wc-item-meta-label wc-item-food">' . wp_kses_post($meta['display_key']) . $args['label_after'] . $value;
            } else {
                $strings[] = $args['label_before'] . wp_kses_post($meta['display_key']) . $args['label_after'] . $value;
            }
        }

        if ($strings) {
            $html = $args['before'] . implode($args['separator'], $strings) . $args['after'];
        }

        $html = apply_filters('woocommerce_display_item_meta', $html, $item, $args);

        if ($args['echo']) {

            echo wp_kses_post($html);
        } else {
            return wp_kses_post($html);
        }
    }

    /**
     * Display Pizza type info.
     */
    public function display_meta_thankyou_main($item_id, $item, $order)
    {
        if (!u_is_pizza_product($item->get_data()['product_id'])) {
            return;
        }

        $item_meta_data = $item->get_meta_data();
        $product_id = $item->get_data()['product_id'];
        $product = wc_get_product($product_id);
        $item_data = [];

        foreach ($item_meta_data as $meta) {

            if ($meta->key !== '_u_pizza_config') {
                continue;
            }
            if (isset($meta->value['dish']['components'])) {
                continue;
            }

            if (isset($meta->value['pizza']['extra'])) {

                $item_data['pizza']['extra_text'] = apply_filters('u_pizza_components_adds_text', esc_html__('Components extra:', 'u-pizza'), $product_id);
                foreach ($meta->value['pizza']['extra'] as $component) {
                    $item_data['pizza']['extra'][] = [
                        'key' => $component['name'],
                        'value' => $component['weight'] !== '' ? '<span>' . $component['weight'] . '/' . '</span>' . wc_price($component['price']) . '<span class="pizza-quantity-badge">' . ' x' . $component['quantity'] . '</span>' : wc_price($component['price']) . '<span class="pizza-quantity-badge">' . ' x' . $component['quantity'] . '</span>'
                    ];
                }
            }
            if (isset($meta->value['pizza']['base'])) {
                $item_data['pizza']['base_text'] = apply_filters('u_pizza_components_base_text', esc_html__('Base Components:', 'u-pizza'), $product_id);
                foreach ($meta->value['pizza']['base'] as $component) {
                    $item_data['pizza']['base'][] = [
                        'key' => $component['name'],
                        'value' => $component['weight'] !== '' ? '<span>' . $component['weight'] . '/' . '</span>' . wc_price($component['price']) : wc_price($component['price'])
                    ];
                }
            }
            if (isset($meta->value['pizza']['floors'])) {
                $item_data['pizza']['floor_text'] = apply_filters('u_pizza_components_floor_text', esc_html__('Floors:', 'u-pizza'), $product_id);
                foreach ($meta->value['pizza']['floors'] as $component) {

                    $item_data['pizza']['floors'][] = [
                        'key' => $component['name'],
                        'value' => wc_price($component['price'])
                    ];
                }
            }
            if (isset($meta->value['pizza']['sides'])) {
                $item_data['pizza']['side_text'] = apply_filters('u_pizza_components_side_text', esc_html__('Sides:', 'u-pizza'), $product_id);
                foreach ($meta->value['pizza']['sides'] as $component) {

                    $item_data['pizza']['sides'][] = [
                        'key' => $component['name'],
                        'value' => wc_price($component['price'])
                    ];
                }
            }

            wc_get_template('cart/u-pizza-meta.php', ['product' => $product, 'item_data' => $item_data, 'key' => $item_id], '', U_PIZZA_PATH . 'templates/front/');
        }
    }

    /**
     * Add meta of product type.
     */
    public function display_pizza_type_meta($html, $cart_item)
    {
        $item_data = [];
        $product_id =  $cart_item['data']->get_parent_id() ? $cart_item['data']->get_parent_id() : $cart_item['data']->get_id();
        $product = wc_get_product($product_id);


        if (u_is_pizza_product($product_id)) {
            if (isset($cart_item['u_pizza_config'])) {
                //This fancybox only for pizza type
                if (isset($cart_item['u_pizza_config']['dish'])) {
                    return false;
                }
                if (isset($cart_item['u_pizza_config']['pizza']['extra'])) {

                    $item_data['pizza']['extra_text'] = apply_filters('u_pizza_components_adds_text', esc_html__('Components extra:', 'u-pizza'), $cart_item['data']->get_id());
                    foreach ($cart_item['u_pizza_config']['pizza']['extra'] as $component) {
                        $item_data['pizza']['extra'][] = [
                            'key' => $component['name'],
                            'value' => $component['weight'] !== '' ? '<span>' . $component['weight'] . '/' . '</span>' . wc_price($component['price']) . '<span class="pizza-quantity-badge">' . ' x' . $component['quantity'] . '</span>' : wc_price($component['price']) . '<span class="pizza-quantity-badge">' . ' x' . $component['quantity'] . '</span>'
                        ];
                    }
                }
                if (isset($cart_item['u_pizza_config']['pizza']['base'])) {
                    $item_data['pizza']['base_text'] = apply_filters('u_pizza_components_base_text', esc_html__('Base Components:', 'u-pizza'), $cart_item['data']->get_id());
                    foreach ($cart_item['u_pizza_config']['pizza']['base'] as $component) {
                        $item_data['pizza']['base'][] = [
                            'key' => $component['name'],
                            'value' => $component['weight'] !== '' ? '<span>' . $component['weight'] . '/' . '</span>' . wc_price($component['price']) : wc_price($component['price'])
                        ];
                    }
                }
                if (isset($cart_item['u_pizza_config']['pizza']['floors'])) {
                    $item_data['pizza']['floor_text'] = apply_filters('u_pizza_components_floors_text', esc_html__('Floors:', 'u-pizza'), $cart_item['data']->get_id());
                    foreach ($cart_item['u_pizza_config']['pizza']['floors'] as $component) {

                        $item_data['pizza']['floors'][] = [
                            'key' => $component['name'],
                            'value' => wc_price($component['price'])
                        ];
                    }
                }
                if (isset($cart_item['u_pizza_config']['pizza']['sides'])) {
                    $item_data['pizza']['side_text'] = apply_filters('u_pizza_components_side_text', esc_html__('Side:', 'u-pizza'), $cart_item['data']->get_id());
                    foreach ($cart_item['u_pizza_config']['pizza']['sides'] as $component) {

                        $item_data['pizza']['sides'][] = [
                            'key' => $component['name'],
                            'value' => wc_price($component['price'])
                        ];
                    }
                }
            }
        }
        wc_get_template('cart/u-pizza-meta.php', ['item_data' => $item_data, 'product' => $product, 'key' => $cart_item['key']], '', U_PIZZA_PATH . 'templates/front/');
    }

    /**
     * Debug order.
     */
    public function debug_order($order_id)
    {
        $order = wc_get_order($order_id);
        foreach ($order->get_items() as $item_id => $order_item) {
            $meta_data = $order_item->get_meta_data();
            echo "<pre>";
            print_r($meta_data);

            echo '</pre>';
        }
    }
}
