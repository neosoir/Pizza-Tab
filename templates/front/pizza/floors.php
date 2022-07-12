<?php

/**
 * Template for floors fancybox
 */
defined('ABSPATH') || exit;


?>

<div class="pizza-fancybox-floors">
    <div class="pizza-floors-selected__footermobile">
        <div class="u_pizza_total">
            <span class="floors-total"><?php esc_html_e('Total:', 'u-pizza'); ?></span>
            <span class="floors-total-price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
        </div>
        <button class="u-pizza-button choose-floor-button"><?php echo apply_filters('u_pizza_add_floor_button', esc_html(__('Choose', 'u-pizza'))); ?></button>
    </div>
    <div class="pizza-floors-block">
        <?php foreach ($data['pizza']['floors']['components'] as $product_id) : ?>
            <?php $inner_product = wc_get_product($product_id); ?>
            <div class="pizza-floor-item" data-floor="<?php echo esc_attr($product_id); ?>" data-floor-price="<?php echo esc_attr(U_Pizza_Product::get_product($inner_product)->get_price()); ?>">
                <?php echo $inner_product->get_image(); ?>

                <span class="u-pizza-title"><?php echo wp_kses_post($inner_product->get_name()); ?></span>
                <span class="u-pizza-price"><?php echo wp_kses_post($inner_product->get_price_html()); ?> </span>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="pizza-floors-selected">
        <div class="pizza-floors-selected__header">
            <span><?php echo apply_filters('u_pizza_add_floor_title', wp_kses_post('<span class="pizza-highlight">' . __('Add', 'u-pizza') . '</span><span>' . __(' floor', 'u-pizza') . '</span>')); ?></span>
        </div>
        <div class="pizza-floors-selected__block">
            <div class="pizza-floors-selected__item" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                <div class="pizza-floors-left">
                    <?php echo wp_kses_post($product->get_image()); ?>
                </div>
                <div class="pizza-floors-right">
                    <span><?php echo wp_kses_post($product->get_name()); ?></span>
                    <span class="pizza-variable-price"><?php echo $product->get_price_html(); ?> </span>
                </div>
            </div>
            <?php foreach (range(2, apply_filters('u_pizza_floors_count', 3)) as $item) : ?>
                <div class="pizza-floors-selected__item" data-product-id="">
                    <a href="#" class="u-remove-floor">
                        <svg width="10" height="9" viewBox="0 0 10 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.00426 3.44918L7.97954 0H9.90622L5.98465 4.46291L10 9H8.05627L5.00426 5.48901L1.93521 9H0L4.02387 4.46291L0.0937766 0H2.01194L5.00426 3.44918Z" fill="#C3C3C3" />
                        </svg>
                    </a>
                    <div class="pizza-floors-left">
                        <img src=" <?php echo esc_url(u_pizza_get_image_placeholder('empty_floor')); ?>" alt="">
                    </div>
                    <div class="pizza-floors-right">
                        <span class="pizza-text-placeholder">
                            <?php echo apply_filters('u_pizza_empty_floor_text', sprintf(esc_html__('Choose %d flour pizza', 'u-pizza'), $item)); ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="pizza-floors-selected__footer">
            <div class="u_pizza_total">
                <span class="floors-total"><?php esc_html_e('Total:', 'u-pizza'); ?></span>
                <span class="floors-total-price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
            </div>
            <button class="u-pizza-button choose-floor-button"><?php echo apply_filters('u_pizza_add_floor_button', esc_html__('Choose', 'u-pizza')); ?></button>
        </div>
    </div>
</div>