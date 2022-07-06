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
