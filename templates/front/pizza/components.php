<?php

if ($product->is_type('simple')) {
    wp_enqueue_script('pizza-simple');
} elseif ($product->is_type('variable')) {
    wp_enqueue_script('pizza-variable');
}

// echo "<pre>";
// print_r($data);
// echo "</pre>";

$data_json = wp_json_encode( $data );
$data_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $data_json ) : _wp_specialchars( $data_json, ENT_QUOTES, 'UTF-8', true );
$product_pizza = U_Pizza_Product::get_product($product);

?>
<div class="pizza_components_wrapper" data-pizza="<?php echo $data_attr; ?>" data-price="<?php echo $product_pizza->get_price(); ?>" data-product-id="<?php echo esc_attr(get_the_ID()); ?>">
    <?php if ($data['pizza']['enabled']) : ?>
        <div class="pizza-components-block">
            <div class="pizza-components-nav">
                <ul>
                    <li><a class="active" href="#add-component"><?php esc_html_e('Add ingredient', 'u-pizza'); ?></a></li>
                    <li><a href="#remove-component"><?php esc_html_e('Remove ingredient', 'u-pizza'); ?></a></li>
                </ul>
            </div>
            <div class="pizza-components-tabs">
                <?php if (!empty($data['pizza']['extra'])) : ?>
                    <div id="add-component" class="pizza-components-tab fade-in">
                        <?php foreach ($data['pizza']['extra'] as $c) : ?>
                            <div class="pizza-components-item">
                                <div class="component-buttons" data-food-item="<?php echo esc_attr($c['id']); ?>">
                                    <?php
                                    u_pizza_woo_quantity_input([
                                        'input_name'    => 'ev_quantity[' . $c['id'] . ']',
                                        'min_value'     => 0,
                                        'max_value'     => 100,
                                        'classes'       => ['input-text', 'component-qty', 'text'],
                                        'input_value'   => 0
                                    ]);
                                    ?>
                                </div>
                                <span class="pizza-component-name"><?php echo esc_html($c['name']); ?></span>
                                <img class="pizza-component-image" src="<?php echo esc_url(wp_get_attachment_image_url($c['imageId'], 'medium')); ?>" alt="">
                                <?php if (!empty($c['weight'])) : ?>
                                    <p class="pizza-component-meta"><span class="pizza-component-weight"><?php echo esc_html($c['weight']) . '/'; ?></span><span class="pizza-component-price"><?php echo wc_price($c['price']); ?></span></p>
                                <?php else : ?>
                                    <p class="pizza-component-meta"><span class="pizza-component-price"><?php echo wc_price($c['price']); ?></span></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($data['pizza']['base'])) : ?>
                    <div id="remove-component" class="pizza-components-tab">
                        <?php
                        $pizza_base_input = [];
                        foreach ($data['pizza']['base'] as $c) {
                            $pizza_base_input[] = [$c['id'] => true];
                        }
                        $pizza_base_json = wp_json_encode($pizza_base_input);
                        $pizza_base_attr = function_exists('wc_esc_json') ? wc_esc_json($pizza_base_json) : _wp_specialchars($pizza_base_json, ENT_QUOTES, 'UTF-8', true);
                        ?>
                        <input type="hidden" name="u-pizza-base" value="<?php echo $pizza_base_attr ?>">
                        <?php foreach ($data['pizza']['base'] as $c) : ?>
                            <?php if (! $c['visible']) continue; ?>
                            <div class="pizza-components-item" data-component-id="<?php echo esc_attr( $c['id'] ) ?>">
                                <?php if (!$c['required']) : ?>
                                    <a href="#" class="u-remove-component">
                                        <svg width="14" height="14" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M 4 4 L 16 16 M 16 4 L 4 16" fill="#fff" stroke-width="3" />
                                        </svg>
                                    </a>
                                <?php endif; ?>
                                <span class="pizza-component-name"><?php echo esc_html($c['name']); ?></span>
                                <img class="pizza-component-image" src="<?php echo esc_url(wp_get_attachment_image_url($c['imageId'], 'medium')); ?>" alt="">
                                <?php if (!empty($c['weight'])) : ?>
                                    <p class="pizza-component-meta"><span class="pizza-component-weight"><?php echo esc_html($c['weight']) . '/'; ?></span><span class="pizza-component-price"><?php echo wc_price($c['price']); ?></span></p>
                                <?php else : ?>
                                    <p class="pizza-component-meta"><span class="pizza-component-price"><?php echo wc_price($c['price']); ?></span></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
