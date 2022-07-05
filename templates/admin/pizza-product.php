<?php
global $post;
$pizza_data = get_option('u_pizza_data');
$pizza_product_data = get_post_meta($post->ID, 'u_product_pizza_data', true);

// echo "<pre>";
// print_r($pizza_product_data['pizza']['sides']['components']);
// echo '</pre>';
$wc_products = wc_get_products([
    'limit' => -1,
    'type' => ['simple', 'variation'],
    'exclude' => [$post->ID]
]);
?>

<div id="u_pizza_product_data" class="panel wc-metaboxes-wrapper hidden woocommerce_options_panel">

    <div class='pizza-type-select'>
        <div class="form-group">
            <input id="pizza_type_1" type="radio" name="pizza_type" value="1" <?php echo $pizza_product_data ? checked($pizza_product_data['pizza']['enabled'], true, false) : 'checked'; ?>>

            <label for="pizza_type_1"><?php esc_html_e('Pizza type'); ?></label>
        </div>
        <div class="form-group">
            <input id="pizza_type_2" type="radio" name="pizza_type" value="2" <?php echo $pizza_product_data ? checked($pizza_product_data['dish']['enabled'], true, false) : ''; ?>>

            <label for="pizza_type_2"><?php esc_html_e('Dish type'); ?></label>
        </div>
    </div>
    <div class="pizza-product-content">
        <!-- For Pizza -->
        <div id="pizza_block_1">
            <div class="form-group-full form-price-inc">
                <input type="checkbox" id="price_inc" name="price_inc" <?php $pizza_product_data ? checked($pizza_product_data['pizza']['price_inc'], true) : ''; ?>>
                <label for="price_inc"><?php esc_html_e('Enable price include', 'u-pizza'); ?></label>
            </div>
            <div>
                <div class="form-group">
                    <label for="pizza_base_components"><?php esc_html_e('Base components', 'u-pizza'); ?></label>
                    <select id="pizza_base_components" name="pizza_base_components[]" multiple>
                        <?php foreach ($pizza_data as $group) : ?>
                            <optgroup label="<?php echo esc_attr($group['group_name']); ?>">
                                <?php foreach ($group['components'] as $component) : ?>
                                    <option value="<?php echo esc_attr($component['id']); ?>" <?php $pizza_product_data ? selected(in_array($component['id'], wp_list_pluck($pizza_product_data['pizza']['base'], 'id')), true) : ''; ?>><?php echo esc_html($component['name']); ?> </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for=""><?php esc_html_e('Extra components', 'u-pizza'); ?></label>
                    <select id="pizza_extra_components" name="pizza_extra_components[]" multiple>
                        <?php foreach ($pizza_data as $group) : ?>
                            <optgroup label="<?php echo esc_attr($group['group_name']); ?>">
                                <?php foreach ($group['components'] as $component) : ?>
                                    <option value="<?php echo esc_attr($component['id']); ?>" <?php $pizza_product_data ? selected(in_array($component['id'], wp_list_pluck($pizza_product_data['pizza']['extra'], 'id')), true) : ''; ?>><?php echo esc_html($component['name']); ?> </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>

                </div>
            </div>
            <div class="group-components" id="pizza_consists_block">
                <?php if ($pizza_product_data && !empty($pizza_product_data['pizza']['base'])) : ?>
                    <?php foreach ($pizza_product_data['pizza']['base'] as $component) : ?>
                        <div class="group-component" data-id="<?php echo esc_attr($component['id']); ?>">
                            <div class="component-header">
                                <div class="component-details">
                                    <span><?php echo esc_html($component['name']); ?></span>
                                    <span><?php echo wc_price($component['price']); ?></span>


                                </div>
                                <div class="component-actions">
                                    <span class="dashicons dashicons-edit edit-component"></span>

                                </div>
                            </div>
                            <div class="component-body">
                                <div class="component-img">
                                    <img src="<?php echo esc_attr(wp_get_attachment_image_url($component['imageId'], 'medium')); ?>" alt="">

                                </div>
                            </div>
                            <div class="component-body-collapse">

                                <div class="form-group-full">
                                    <label for=""><?php esc_html_e('Price', 'u-pizza'); ?></label>
                                    <input type="text" name="pizza_base[<?php echo esc_attr($component['id']); ?>][price]" value="<?php echo esc_attr($component['price']); ?>">
                                </div>
                                <div class="form-group-full">
                                    <label for=""><?php esc_html_e('Weight', 'u-pizza'); ?></label>
                                    <input type="text" name="pizza_base[<?php echo esc_attr($component['id']); ?>][weight]" value="<?php echo esc_attr($component['weight']); ?>">
                                </div>

                                <div class="form-group-full">

                                    <input type="checkbox" name="pizza_base[<?php echo esc_attr($component['id']); ?>][required]" <?php checked($component['required'], 1); ?>>
                                    <label for=""><?php esc_html_e('Required', 'u-pizza'); ?></label>
                                </div>
                                <div class="form-group-full">

                                    <input type="checkbox" name="pizza_base[<?php echo esc_attr($component['id']); ?>][visible]" <?php checked($component['visible'], 1); ?>>
                                    <label for=""><?php esc_html_e('Visible', 'u-pizza'); ?></label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="form-group-full">

                <input type="checkbox" id="pizza_sides" name="pizza_sides" <?php $pizza_product_data ? checked($pizza_product_data['pizza']['sides']['enabled'], true) : ''; ?>>
                <label for="pizza_sides"><?php esc_html_e('Enable sides block', 'u-pizza'); ?></label>
            </div>

            <div class="group-components" id="pizza_sides_block" style="<?php echo $pizza_product_data && $pizza_product_data['pizza']['sides']['enabled'] ? '' : 'display:none;'; ?>">
                <?php if ($pizza_product_data && !empty($pizza_product_data['pizza']['sides']['components'])) : ?>
                    <?php

                    foreach (u_pizza_sides() as $component) {

                        if (!in_array($component['id'], wp_list_pluck($pizza_product_data['pizza']['sides']['components'], 'id'))) {
                            array_push($pizza_product_data['pizza']['sides']['components'], $component);
                        }
                    }
                    ?>
                    <?php foreach ($pizza_product_data['pizza']['sides']['components'] as $component) : ?>

                        <div class="group-component" data-id="<?php echo esc_attr($component['id']); ?>">
                            <div class="component-header">
                                <div class="component-details">
                                    <span><?php echo esc_html($component['name']); ?></span>
                                    <span><?php echo wc_price($component['price']); ?></span>


                                </div>
                                <div class="component-actions">
                                    <span class="dashicons dashicons-edit edit-component"></span>

                                </div>
                            </div>
                            <div class="component-body">
                                <div class="component-img">
                                    <img src="<?php echo esc_attr(wp_get_attachment_image_url($component['imageId'], 'medium')); ?>" alt="">

                                </div>
                            </div>
                            <div class="component-body-collapse">

                                <div class="form-group-full">
                                    <label for=""><?php esc_html_e('Price', 'u-pizza'); ?></label>
                                    <input type="text" name="pizza_side[<?php echo esc_attr($component['id']); ?>][price]" value="<?php echo esc_attr($component['price']); ?>">
                                </div>
                                <div class="form-group-full">
                                    <label for=""><?php esc_html_e('Weight', 'u-pizza'); ?></label>
                                    <input type="text" name="pizza_side[<?php echo esc_attr($component['id']); ?>][weight]" value="<?php echo esc_attr($component['weight']); ?>">
                                </div>


                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php elseif (!empty(u_pizza_sides())) : ?>
                    <?php foreach (u_pizza_sides() as $component) : ?>

                        <div class="group-component" data-id="<?php echo esc_attr($component['id']); ?>">
                            <div class="component-header">
                                <div class="component-details">
                                    <span><?php echo esc_html($component['name']); ?></span>
                                    <span><?php echo wc_price($component['price']); ?></span>


                                </div>
                                <div class="component-actions">
                                    <span class="dashicons dashicons-edit edit-component"></span>

                                </div>
                            </div>
                            <div class="component-body">
                                <div class="component-img">
                                    <img src="<?php echo esc_attr(wp_get_attachment_image_url($component['imageId'], 'medium')); ?>" alt="">

                                </div>
                            </div>
                            <div class="component-body-collapse">

                                <div class="form-group-full">
                                    <label for=""><?php esc_html_e('Price', 'u-pizza'); ?></label>
                                    <input type="text" name="pizza_side[<?php echo esc_attr($component['id']); ?>][price]" value="<?php echo esc_attr($component['price']); ?>">
                                </div>
                                <div class="form-group-full">
                                    <label for=""><?php esc_html_e('Weight', 'u-pizza'); ?></label>
                                    <input type="text" name="pizza_side[<?php echo esc_attr($component['id']); ?>][weight]" value="<?php echo esc_attr($component['weight']); ?>">
                                </div>


                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="form-group-full">

                <input type="checkbox" id="pizza_floors" name="pizza_floors" <?php $pizza_product_data ? checked($pizza_product_data['pizza']['floors']['enabled'], true) : ''; ?>>
                <label for="pizza_floors"><?php esc_html_e('Enable floors block', 'u-pizza'); ?></label>
            </div>
            <div id="pizza_floors_block" class="form-group" style="<?php echo $pizza_product_data && $pizza_product_data['pizza']['floors']['enabled'] ? '' : 'display:none;'; ?>">
                <label for="pizza_floor_products"><?php esc_html_e('Products for floors', 'u-pizza'); ?></label>
                <select class="wc-product-search" id="pizza_floor_products" name="pizza_floor_products[]" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo esc_attr($post->ID); ?>" data-placeholder="<?php esc_html_e('Search for a product…', 'u-pizza'); ?>" style="width: 400px;" multiple>
                    <?php foreach ($wc_products as $product) : ?>
                        <option value="<?php echo esc_attr($product->get_id()); ?>" <?php $pizza_product_data && !empty($pizza_product_data['pizza']['floors']['components']) ? selected(in_array($product->get_id(), $pizza_product_data['pizza']['floors']['components']), true) : ''; ?>><?php echo esc_html($product->get_name()); ?> </option>

                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <!-- For Dish -->
        <div id="pizza_block_2">
            <div class="form-group-full">
                <label for="dish_components"><?php esc_html_e('Base components', 'u-pizza'); ?></label>
                <select id="dish_components" name="dish_components[]" multiple>
                    <?php foreach ($pizza_data as $group) : ?>
                        <optgroup label="<?php echo esc_attr($group['group_name']); ?>">
                            <?php foreach ($group['components'] as $component) : ?>
                                <option value="<?php echo esc_attr($component['id']); ?>" <?php $pizza_product_data ? selected(in_array($component['id'], wp_list_pluck($pizza_product_data['dish']['components'], 'id')), true) : ''; ?>><?php echo esc_html($component['name']); ?> </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group-full">

                <input type="checkbox" id="dish_tabs" name="dish_tabs" <?php $pizza_product_data ? checked($pizza_product_data['dish']['tabs'], true) : ''; ?>>
                <label for="dish_tabs"><?php esc_html_e('Enable tabs', 'u-pizza'); ?></label>
            </div>
        </div>
    </div> 
</div>


<script>
    if ( jQuery('#_u_pizza').is(':checked') ) {
        jQuery('.show_if_u_pizza').show(); 
    }
    else {
        jQuery('.show_if_u_pizza').hide(); 
    }
    jQuery('#_u_pizza').on('change', function() {
        if ( jQuery(this).is(':checked') ) {
            jQuery('.show_if_u_pizza').show(); 
        }
        else {
            jQuery('.show_if_u_pizza').hide(); 
        } 
    });

</script>