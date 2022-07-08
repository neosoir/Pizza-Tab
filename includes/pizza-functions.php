<?php

/**
 * Default data for U pizza components
 */
function u_pizza_get_default_data()
{

    $data = [
        [
            'id' => 1,
            'group_name' => __('Cheese', 'u-pizza'),
            'image' => plugins_url('assets/images/placeholder.svg', U_PIZZA_DIR),
            'imageId' => '',
            'components' => [
                [
                    'id' => 1,
                    'name' => __('Chedar', 'u-pizza'),
                    'price' => 120,
                    'description' => __('Some long text', 'u-pizza'),
                    'image' => plugins_url('assets/images/placeholder.svg', U_PIZZA_DIR),
                    'imageId' => '',
                    'weight' => '',
                    'meta' => true, //for pizza sides
                    'required' => false,
                    'visible' => true
                ],
                [
                    'id' => 2,
                    'name' => __('Gauda', 'u-pizza'),
                    'price' => 90,
                    'description' => __('Some no so long text', 'u-pizza'),
                    'image' => plugins_url('assets/images/placeholder.svg', U_PIZZA_DIR),
                    'imageId' => '',
                    'weight' => '20g.',
                    'meta' => true,
                    'required' => false,
                    'visible' => true
                ]
            ]
        ],
        [
            'id' => 2,
            'group_name' => __('Vegetables', 'u-pizza'),
            'image' => plugins_url('assets/images/placeholder.svg', U_PIZZA_DIR),
            'imageId' => '',
            'components' => [
                [
                    'id' => 3,
                    'name' => __('Tomato', 'u-pizza'),
                    'price' => 10,
                    'description' => __('Some long text', 'u-pizza'),
                    'image' => plugins_url('assets/images/placeholder.svg', U_PIZZA_DIR),
                    'imageId' => '',
                    'weight' => '',
                    'meta' => false,
                    'required' => false,
                    'visible' => true
                ],
                [
                    'id' => 4,
                    'name' => __('Red pepper', 'u-pizza'),
                    'price' => 20,
                    'description' => __('Some no so long text', 'u-pizza'),
                    'image' => plugins_url('assets/images/placeholder.svg', U_PIZZA_DIR),
                    'imageId' => '',
                    'weight' => '1 item',
                    'meta' => false,
                    'required' => false,
                    'visible' => true
                ]
            ]
        ]
    ];

    return apply_filters('u_pizza_default_data', $data);
}
/**
 * Get components with meta checked
 * @return array
 */

/**
 * 
 */
function u_pizza_sides()
{
    $pizza_data = get_option('u_pizza_data');
    if (!$pizza_data) {
        return [];
    }
    $pizza_components = array_merge(...wp_list_pluck($pizza_data, 'components'));
    $sides_components =  array_filter($pizza_components, function ($component) {
        return $component['meta'];
    });
    return $sides_components;
}

/**
 * 
 */
function u_flatten_array( $array )
{
    $newArray = [];

    foreach ($array as $key => $value) {
        foreach ($value as $component_key => $component) {
            $newArray[$component_key] = $component;
        }
    }
    return $newArray;
}

/**
 * Get array [group => [id => '', group_name =>'', components => []]] from components array
 */
function u_pizza_tab_components( $product_id )
{
    $pizza_data             = get_option('u_pizza_data');
    $product_pizza_data     = get_post_meta($product_id, 'u_product_pizza_data', true);
    if (empty($product_pizza_data['dish']['components'])) {
        return;
    }
    $tab_components         = [];
    foreach ($pizza_data as $group_key => $group) {
        $tab_components[$group_key] = [
            'id' => $group['id'],
            'group_name' => $group['group_name'],
            'image' => $group['image'],
            'imageId' => $group['imageId'],
        ];
        foreach ($group['components'] as $component) {
            if (array_key_exists($component['id'], $product_pizza_data['dish']['components'])) {
                $tab_components[$group_key]['components'][$component['id']] = $component;
            }
        }
    }
    return array_filter($tab_components, function ($group) {
        return isset($group['components']);
    });
}

/**
 * Check if Pizza checkbox enabled for product
 */
function u_is_pizza_product( $product_id ) {
    return get_post_meta( $product_id, '_u_pizza', true );
}

/**
 * Create custom quantity inputs with help of @see woocommerce_quantity_input()
 */
function u_pizza_woo_quantity_input($args = array(), $product = null, $echo = true)
{
    if (is_null($product)) {
        $product = $GLOBALS['product'];
    }

    $defaults = array(
        'input_id'     => uniqid('quantity_'),
        'input_name'   => 'quantity',
        'input_value'  => '1',
        'classes'      => apply_filters('woocommerce_quantity_input_classes', array('input-text', 'qty', 'text'), $product),
        'max_value'    => apply_filters('woocommerce_quantity_input_max', -1, $product),
        'min_value'    => apply_filters('woocommerce_quantity_input_min', 0, $product),
        'step'         => apply_filters('woocommerce_quantity_input_step', 1, $product),
        'pattern'      => apply_filters('woocommerce_quantity_input_pattern', has_filter('woocommerce_stock_amount', 'intval') ? '[0-9]*' : ''),
        'inputmode'    => apply_filters('woocommerce_quantity_input_inputmode', has_filter('woocommerce_stock_amount', 'intval') ? 'numeric' : ''),
        'product_name' => $product ? $product->get_title() : '',
        'placeholder'  => apply_filters('woocommerce_quantity_input_placeholder', '', $product),
    );

    $args = apply_filters('woocommerce_quantity_input_args', wp_parse_args($args, $defaults), $product);

    // Apply sanity to min/max args - min cannot be lower than 0.
    $args['min_value'] = max($args['min_value'], 0);
    $args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : '';

    // Max cannot be lower than min if defined.
    if ('' !== $args['max_value'] && $args['max_value'] < $args['min_value']) {
        $args['max_value'] = $args['min_value'];
    }

    ob_start();

    wc_get_template('global/component-quantity-input.php', $args, '', U_PIZZA_PATH . 'templates/front/');

    if ($echo) {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo ob_get_clean();
    } else {
        return ob_get_clean();
    }
}

/**
 * Get image placeholders from Settings page
 */
function u_pizza_get_image_placeholder($image)
{
    switch ($image) {
        case 'empty_floor':
            return plugins_url('assets/images/pizza-floor.png', U_PIZZA_DIR);
        case 'empty_side':
            return plugins_url('assets/images/pizza-side.png', U_PIZZA_DIR);
    }
    return false;
}
/**
 * Check if tipps enabled
 */
/* function u_pizza_tipps_enabled()
{
    $tipps_enabled = get_option('pizza_tipps');
    if (!$tipps_enabled) {
        return false;
    }
    return $tipps_enabled === 'yes' ? true : false;
} */

