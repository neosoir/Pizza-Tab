<?php

if ($product->is_type('simple')) {
    wp_enqueue_script('pizza-simple');
} 
elseif ($product->is_type('variable')) {
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
    <!-- Components enabled -->
    <?php if ($data['pizza']['enabled']) : ?>
        <!-- Add and remove pizza components. -->
        <div class="pizza-components-block">
            <div class="pizza-components-nav">
                <ul>
                    <li><a class="active" href="#add-component"><?php esc_html_e('Add ingredient', 'u-pizza'); ?></a></li>
                    <li><a href="#remove-component"><?php esc_html_e('Remove ingredient', 'u-pizza'); ?></a></li>
                </ul>
            </div>
            <div class="pizza-components-tabs">
                <!-- Add Extra components. -->
                <?php if (!empty($data['pizza']['extra'])) : ?>
                    <div id="add-component" class="pizza-components-tab fade-in">
                        <?php foreach ($data['pizza']['extra'] as $c) : ?>
                            <div class="pizza-components-item">
                                <?php if (u_pizza_tipps_enabled() && trim($c['description']) !== '') : ?>
                                    <div class="pizza-tippy" data-tippy-content="<?php echo esc_attr($c['description']); ?>">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.75C7.44365 3.75 3.75 7.44365 3.75 12C3.75 16.5563 7.44365 20.25 12 20.25C16.5563 20.25 20.25 16.5563 20.25 12C20.25 7.44365 16.5563 3.75 12 3.75ZM2.25 12C2.25 6.61522 6.61522 2.25 12 2.25C17.3848 2.25 21.75 6.61522 21.75 12C21.75 17.3848 17.3848 21.75 12 21.75C6.61522 21.75 2.25 17.3848 2.25 12ZM13 16C13 16.5523 12.5523 17 12 17C11.4477 17 11 16.5523 11 16C11 15.4477 11.4477 15 12 15C12.5523 15 13 15.4477 13 16ZM10.75 10C10.75 9.30964 11.3096 8.75 12 8.75C12.6904 8.75 13.25 9.30964 13.25 10V10.1213C13.25 10.485 13.1055 10.8338 12.8483 11.091L11.4697 12.4697C11.1768 12.7626 11.1768 13.2374 11.4697 13.5303C11.7626 13.8232 12.2374 13.8232 12.5303 13.5303L13.909 12.1517C14.4475 11.6132 14.75 10.8828 14.75 10.1213V10C14.75 8.48122 13.5188 7.25 12 7.25C10.4812 7.25 9.25 8.48122 9.25 10V10.5C9.25 10.9142 9.58579 11.25 10 11.25C10.4142 11.25 10.75 10.9142 10.75 10.5V10Z" fill="#22282F" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
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
                <!-- Remove Base componenets. -->
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
                                <?php if (u_pizza_tipps_enabled() && trim($c['description']) !== '') : ?>
                                    <div class="pizza-tippy" data-tippy-content="<?php echo esc_attr($c['description']); ?>">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.75C7.44365 3.75 3.75 7.44365 3.75 12C3.75 16.5563 7.44365 20.25 12 20.25C16.5563 20.25 20.25 16.5563 20.25 12C20.25 7.44365 16.5563 3.75 12 3.75ZM2.25 12C2.25 6.61522 6.61522 2.25 12 2.25C17.3848 2.25 21.75 6.61522 21.75 12C21.75 17.3848 17.3848 21.75 12 21.75C6.61522 21.75 2.25 17.3848 2.25 12ZM13 16C13 16.5523 12.5523 17 12 17C11.4477 17 11 16.5523 11 16C11 15.4477 11.4477 15 12 15C12.5523 15 13 15.4477 13 16ZM10.75 10C10.75 9.30964 11.3096 8.75 12 8.75C12.6904 8.75 13.25 9.30964 13.25 10V10.1213C13.25 10.485 13.1055 10.8338 12.8483 11.091L11.4697 12.4697C11.1768 12.7626 11.1768 13.2374 11.4697 13.5303C11.7626 13.8232 12.2374 13.8232 12.5303 13.5303L13.909 12.1517C14.4475 11.6132 14.75 10.8828 14.75 10.1213V10C14.75 8.48122 13.5188 7.25 12 7.25C10.4812 7.25 9.25 8.48122 9.25 10V10.5C9.25 10.9142 9.58579 11.25 10 11.25C10.4142 11.25 10.75 10.9142 10.75 10.5V10Z" fill="#22282F" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
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
        <!-- Add pizza floors and Slides. -->
        <div class="pizza-components-buttons">
            <!-- Floors. -->
            <?php if ($data['pizza']['floors']['enabled']) : ?>
                <input type="hidden" name="pizza-floors-data" value="">
                <button class="u-pizza-button" id="pizza-floor-button"><?php esc_html_e('Add floor', 'u-pizza'); ?></button>
                <div class="pizza-fancybox" id="u-pizza-floors-fancybox" style="display: none;">
                    <?php wc_get_template('pizza/floors.php', ['data' => $data, 'product' => $product], '', U_PIZZA_PATH . 'templates/front/'); ?>
                </div>
            <?php endif; ?>
            <!-- Slides. -->
            <?php if ($data['pizza']['sides']['enabled']) : ?>
                <input type="hidden" name="pizza-sides-data" value="">
                <button class="u-pizza-button" id="pizza-sides-button"><?php esc_html_e('Choose side', 'u-pizza'); ?></button>
                <div class="pizza-fancybox" id="u-pizza-sides-fancybox" style="display: none;">
                    <?php wc_get_template('pizza/sides.php', ['data' => $data, 'product' => $product], '', U_PIZZA_PATH . 'templates/front/'); ?>
                </div>
            <?php endif; ?>
        </div>
    <!-- Dish Enable -->
    <?php elseif ($data['dish']['enabled']) : ?>
        <?php if ($data['dish']['tabs']) : ?>
            <div class="pizza-component-tabs-wrapper">
                <h3><?php echo apply_filters('u_pizza_extras_text', esc_html('Extras for an additional fee', 'u-pizza')); ?></h3>
                <ul class="pizza-tab-nav">
                    <?php $tabs_components = u_pizza_tab_components($product->get_id()); ?>
                    <?php foreach ($tabs_components as $tab_key => $tab) : ?>
                        <li>
                            <a href="" data-tab-id="<?php echo esc_attr($tab['id']); ?>" class="pizza-tab-link<?php echo $tab_key === 1 ? ' active' : ''; ?>" title="<?php echo esc_attr($tab['group_name']); ?>">
                                <img src="<?php echo esc_url($tab['image']); ?>" alt="">
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="tab-components-wrapper">
                    <?php foreach ($tabs_components as $tab_key => $tab) : ?>
                        <div id="<?php echo esc_attr($tab['id']); ?>" class="component-item-tab <?php echo $tab_key === 1 ? 'fade-in' : ''; ?>">
                            <?php foreach ($tab['components'] as $c) : ?>
                                <div class="component-item">
                                    <div class="component-img" style="background-image: url(<?php echo esc_url(wp_get_attachment_image_url($c['imageId'], 'medium')); ?>);background-repeat:no-repeat">
                                        <div class="component-buttons" data-food-item="<?php echo esc_attr($c['id']); ?>">
                                            <?php
                                            u_pizza_woo_quantity_input([
                                                'input_name' => 'evc_quantity[' . $c['id'] . ']',
                                                'min_value' => 0,
                                                'max_value' => 100,
                                                'classes'      => ['input-text', 'component-qty', 'text'],
                                                'input_value' => 0
                                            ]);
                                            ?>
                                        </div>
                                    </div>
                                    <p><?php echo esc_html($c['name']); ?></p>
                                    <?php if (!empty($c['weight'])) : ?>
                                        <p><?php echo esc_html($c['weight']) . '/' . wc_price($c['price']); ?></p>
                                    <?php else : ?>
                                        <p><?php echo wc_price($c['price']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else : ?>
            <div class="pizza-components-wrapper">
                <h3><?php echo apply_filters('u_pizza_extras_text', esc_html('Extras for an additional fee', 'u-pizza')); ?></h3>
                <div class="components-item-wrapper">
                    <?php foreach ($data['dish']['components'] as $c) : ?>
                        <div class="component-item">
                            <div class="component-img" style="background-image: url(<?php echo esc_url(wp_get_attachment_image_url($c['imageId'], 'medium')); ?>);background-repeat:no-repeat">
                                <div class="component-buttons" data-food-item="<?php echo esc_attr($c['id']); ?>">
                                    <?php
                                    u_pizza_woo_quantity_input([
                                        'input_name' => 'evc_quantity[' . $c['id'] . ']',
                                        'min_value' => 0,
                                        'max_value' => 100,
                                        'classes'      => ['input-text', 'component-qty', 'text'],
                                        'input_value' => 0
                                    ]);
                                    ?>
                                </div>
                            </div>

                            <p><?php echo esc_html($c['name']); ?></p>

                            <?php if (!empty($c['weight'])) : ?>
                                <p><?php echo esc_html($c['weight']) . '/' . wc_price($c['price']); ?></p>
                            <?php else : ?>
                                <p><?php echo wc_price($c['price']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Templating floors -->
<script type="text/html" id="tmpl-pizza-floor-selected">
    <div class="pizza-floors-selected__item" data-product-id="{{{data.product_id}}}">
        <a href="#" class="u-remove-floor">
            <svg width="10" height="9" viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.00426 3.44918L7.97954 0H9.90622L5.98465 4.46291L10 9H8.05627L5.00426 5.48901L1.93521 9H0L4.02387 4.46291L0.0937766 0H2.01194L5.00426 3.44918Z" fill="#C3C3C3" />
            </svg>
        </a>
        <div class="pizza-floors-left">
            <img src="{{{data.image}}}" alt="">
        </div>
        <div class="pizza-floors-right">
            <span>{{{data.name}}}</span>
            <span class="pizza-variable-price">{{{data.price}}}</span>
        </div>
    </div>
</script>
<!-- Templating floors default -->
<script type="text/html" id="tmpl-pizza-floor-default">
    <div class="pizza-floors-selected__item" data-product-id="">
        <a href="#" class="u-remove-floor">
            <svg width="10" height="9" viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.00426 3.44918L7.97954 0H9.90622L5.98465 4.46291L10 9H8.05627L5.00426 5.48901L1.93521 9H0L4.02387 4.46291L0.0937766 0H2.01194L5.00426 3.44918Z" fill="#C3C3C3" />
            </svg>
        </a>
        <div class="pizza-floors-left">
            <img src="{{{data.image}}}" alt="">
        </div>
        <div class="pizza-floors-right">
            <span class="pizza-text-placeholder">{{{data.name}}}</span>
        </div>
    </div>
</script>

<!-- Templating sides -->
<script type="text/html" id="tmpl-pizza-side-selected">
    <div class="pizza-floors-selected__item pizza-sides-selected__item">
        <a href="#" class="u-remove-side">
            <svg width="10" height="9" viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.00426 3.44918L7.97954 0H9.90622L5.98465 4.46291L10 9H8.05627L5.00426 5.48901L1.93521 9H0L4.02387 4.46291L0.0937766 0H2.01194L5.00426 3.44918Z" fill="#C3C3C3" />
            </svg>
        </a>
        <div class="pizza-floors-left">
            <img src="{{{data.image}}}" alt="">
        </div>
        <div class="pizza-floors-right">
            <span>{{{data.name}}}</span>
            <span>{{{data.price}}}</span>
        </div>
    </div>
</script>
<!-- Templating sides default -->
<script type="text/html" id="tmpl-pizza-side-default">
    <div class="pizza-floors-selected__item pizza-sides-selected__item">
        <div class="pizza-floors-left">
            <img src="{{{data.image}}}" alt="">
        </div>
        <div class="pizza-floors-right">
            <span class="pizza-text-placeholder">{{{data.name}}}</span>
        </div>
    </div>
</script>
