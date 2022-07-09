<?php

/**
 * Template for sides fancybox
 */
defined('ABSPATH') || exit;


?>
<div class="pizza-fancybox-sides">
    <div class="pizza-floors-selected__footermobile">
        <div class="u_pizza_total">
            <span class="floors-total"><?php esc_html_e('Total:', 'u-pizza'); ?></span>
            <span class="floors-total-price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
        </div>
        <button class="u-pizza-button choose-floor-button"><?php echo apply_filters('u_pizza_add_floor_button', esc_html__('Choose', 'u-pizza')); ?></button>
    </div>
    <div class="pizza-floors-block">
        <?php foreach ($data['pizza']['sides']['components'] as $component) : ?>
            <div class="pizza-floor-item" data-side-id="<?php echo esc_attr($component['id']); ?>">
                <img src="<?php echo esc_url(wp_get_attachment_image_url($component['imageId'], 'medium')); ?>" alt="">

                <span class="u-pizza-title"><?php echo esc_html($component['name']); ?></span>
                <?php if (!empty($component['weight'])) : ?>
                    <p><span class="u-pizza-weight"><?php echo esc_html($component['weight']) . '/'; ?></span><span class="u-pizza-price"><?php echo wc_price($component['price']); ?> </span> </p>
                <?php else : ?>
                    <span class="u-pizza-price"><?php echo wc_price($component['price']); ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="pizza-floors-selected">
        <div class="pizza-floors-selected__header">
            <span><?php echo apply_filters('u_pizza_add_sides_title', wp_kses_post('<span class="pizza-highlight">' . __('Choose', 'u-pizza') . '</span><span>' . __(' side', 'u-pizza') . '</span>')); ?></span>
        </div>
        <div class="pizza-floors-selected__block">
            <div class="pizza-floors-selected__item pizza-sides-selected__item">
                <div class="pizza-floors-left">
                    <img src=" <?php echo esc_url(u_pizza_get_image_placeholder('empty_side')); ?>" alt="">
                </div>
                <div class="pizza-floors-right">
                    <span class="pizza-text-placeholder">
                        <?php echo apply_filters('u_pizza_empty_side_text', esc_html__('Choose cheese', 'u-pizza')); ?>
                    </span>
                </div>
            </div>
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