<?php
/* echo "<pre>";
print_r($data);
echo "</pre>"; */
?>

<div class="pizza_components_wrapper" >
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
                                        'input_name' => 'ev_quantity[' . $c['id'] . ']',
                                        'min_value' => 0,
                                        'max_value' => 100,
                                        'classes'      => ['input-text', 'component-qty', 'text'],
                                        'input_value' => 0
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
                        <?php foreach ($data['pizza']['base'] as $c) : ?>
                            <?php if (! $c['visible']) continue; ?>
                            <div class="pizza-components-item">
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
