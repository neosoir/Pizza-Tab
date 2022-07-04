<?php

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
