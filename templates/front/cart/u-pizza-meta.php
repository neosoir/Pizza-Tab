<?php

/**
 * Template Pizza fancybox on Cart/Checkout pages
 
 */
if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="pizza-composition-block">
    <div class="pizza-composition-toggle" data-product-id='<?php echo esc_attr($key); ?>'>
        <span><?php echo apply_filters('u_pizza_composition_text', esc_html(__('Pizza composition', 'u-pizza')), $product->get_id()); ?></span>
    </div>

</div>
<div id="u-pizza-<?php echo esc_attr($key); ?>" class="u-pizza-fancy-ingredients" style="display: none;">
    <ul>
        <?php if (isset($item_data['pizza']['base'])) : ?>
            <li>
                <strong><?php echo esc_html($item_data['pizza']['base_text']); ?></strong>
                <?php foreach ($item_data['pizza']['base'] as $component) : ?>
                    <p><span><?php echo wp_kses_post($component['key']); ?></span><span><?php echo wp_kses_post($component['value']); ?></span></p>
                <?php endforeach; ?>
            </li>
        <?php endif; ?>
        <?php if (isset($item_data['pizza']['extra'])) : ?>
            <li>
                <strong><?php echo esc_html($item_data['pizza']['extra_text']); ?></strong>
                <?php foreach ($item_data['pizza']['extra'] as $component) : ?>
                    <p><span><?php echo wp_kses_post($component['key']); ?></span><span><?php echo wp_kses_post($component['value']); ?></span></p>
                <?php endforeach; ?>
            </li>
        <?php endif; ?>

        <?php if (isset($item_data['pizza']['floors'])) : ?>
            <li>
                <strong><?php echo esc_html($item_data['pizza']['floor_text']); ?></strong>
                <?php foreach ($item_data['pizza']['floors'] as $component) : ?>
                    <p><span><?php echo wp_kses_post($component['key']); ?></span><span><?php echo wp_kses_post($component['value']); ?></span></p>
                <?php endforeach; ?>
            </li>
        <?php endif; ?>
        <?php if (isset($item_data['pizza']['sides'])) : ?>
            <li>
                <strong><?php echo esc_html($item_data['pizza']['side_text']); ?></strong>
                <?php foreach ($item_data['pizza']['sides'] as $component) : ?>
                    <p><span><?php echo wp_kses_post($component['key']); ?></span><span><?php echo wp_kses_post($component['value']); ?></span></p>
                <?php endforeach; ?>
            </li>
        <?php endif; ?>
    </ul>
</div>